<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class IncidentReportingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('incident_blotter');
        Schema::dropIfExists('incident_types');

        Schema::create('incident_types', function (Blueprint $table) {
            $table->increments('Category_Id');
            $table->string('Category')->nullable();
        });

        Schema::create('incident_blotter', function (Blueprint $table) {
            $table->increments('Incident_ID');
            $table->unsignedInteger('Complainant_Id')->nullable();
            $table->unsignedInteger('Respondent_Id')->nullable();
            $table->unsignedInteger('Guest_Id')->nullable();
            $table->unsignedInteger('Category_Id');
            $table->text('Description');
            $table->text('Requested_Relief')->nullable();
            $table->dateTime('Date_Reported')->nullable()->useCurrent();
            $table->dateTime('Date_Filed')->nullable();
            $table->string('Resolution_Status')->nullable()->default('Pending');
            $table->decimal('Latitude', 10, 8)->nullable();
            $table->decimal('Longitude', 11, 8)->nullable();
            $table->unsignedInteger('Handled_By')->nullable();
        });
    }

    public function test_resident_can_submit_incident_report_from_resident_form(): void
    {
        $resident = \DB::table('resident')->insertGetId([
            'First_Name' => 'Maria',
            'Middle_Name' => 'Santos',
            'Last_Name' => 'Dela Cruz',
            'Date_of_Birth' => '1990-05-15',
            'Contact_Number' => '09181234567',
            'Household_Index' => 1,
            'Is_Verified' => 1,
        ]);

        \DB::table('incident_types')->insert([
            'Category' => 'Noise Complaint',
        ]);

        $response = $this->post('/incident-reporting', [
            'reporter_type' => 'resident',
            'first_name' => 'Maria',
            'middle_name' => 'Santos',
            'last_name' => 'Dela Cruz',
            'date_of_birth' => '1990-05-15',
            'complainant_name' => 'Maria Santos Dela Cruz',
            'respondent_first_name' => 'Maria',
            'respondent_middle_name' => 'Santos',
            'respondent_last_name' => 'Dela Cruz',
            'respondent_date_of_birth' => '1990-05-15',
            'category_id' => 1,
            'complaint_details' => 'Loud music every night',
            'requested_relief' => 'Barangay intervention',
        ]);

        $response->assertRedirect('/incident-reporting');

        $this->assertDatabaseHas('incident_blotter', [
            'Category_Id' => 1,
            'Description' => 'Loud music every night',
            'Requested_Relief' => 'Barangay intervention',
            'Resolution_Status' => 'Pending',
        ]);
    }

    public function test_resident_name_with_multiple_first_name_words_can_match_respondent_lookup(): void
    {
        \DB::table('resident')->insert([
            'First_Name' => 'Prince Marvin',
            'Middle_Name' => 'Engay',
            'Last_Name' => 'Azul',
            'Date_of_Birth' => '2004-08-25',
            'Contact_Number' => '09189231139',
            'Household_Index' => 1,
            'Is_Verified' => 1,
        ]);

        \DB::table('incident_types')->insert([
            'Category_Id' => 1,
            'Category' => 'Noise Complaint',
        ]);

        $response = $this->post('/incident-reporting', [
            'reporter_type' => 'resident',
            'first_name' => 'Maria',
            'middle_name' => 'Santos',
            'last_name' => 'Dela Cruz',
            'date_of_birth' => '1990-05-15',
            'complainant_name' => 'Maria Santos Dela Cruz',
            'respondent_first_name' => 'Prince Marvin',
            'respondent_middle_name' => 'Engay',
            'respondent_last_name' => 'Azul',
            'respondent_date_of_birth' => '2004-08-25',
            'category_id' => 1,
            'complaint_details' => 'Neighbor dispute',
            'requested_relief' => 'Mediation',
        ]);

        $response->assertRedirect('/incident-reporting');

        $this->assertDatabaseHas('incident_blotter', [
            'Category_Id' => 1,
            'Description' => 'Neighbor dispute',
            'Requested_Relief' => 'Mediation',
            'Resolution_Status' => 'Pending',
        ]);
    }

    public function test_guest_can_submit_incident_report_with_guest_details(): void
    {
        \DB::table('resident')->insert([
            'First_Name' => 'Juan',
            'Middle_Name' => 'M.',
            'Last_Name' => 'Cruz',
            'Date_of_Birth' => '1988-02-01',
            'Contact_Number' => '09185551234',
            'Household_Index' => 1,
            'Is_Verified' => 1,
        ]);

        \DB::table('incident_types')->insert([
            'Category_Id' => 1,
            'Category' => 'Harassment',
        ]);

        $response = $this->post('/incident-reporting', [
            'reporter_type' => 'guest',
            'guest_first_name' => 'Ana',
            'guest_middle_name' => 'L.',
            'guest_last_name' => 'Reyes',
            'guest_address' => '123 Sample Street',
            'guest_contact_number' => '09181234567',
            'complainant_name' => 'Ana L. Reyes',
            'respondent_first_name' => 'Juan',
            'respondent_middle_name' => 'M.',
            'respondent_last_name' => 'Cruz',
            'respondent_date_of_birth' => '1988-02-01',
            'category_id' => 1,
            'complaint_details' => 'Threatening behavior',
            'requested_relief' => 'Protection order and mediation',
        ]);

        $response->assertRedirect('/incident-reporting');

        $this->assertDatabaseHas('incident_blotter', [
            'Category_Id' => 1,
            'Description' => 'Threatening behavior',
            'Requested_Relief' => 'Protection order and mediation',
            'Resolution_Status' => 'Pending',
        ]);
    }

    public function test_case_monitoring_does_not_duplicate_a_single_incident_when_complainant_and_respondent_are_different(): void
    {
        \DB::table('resident')->insert([
            [
                'Resident_ID' => 1,
                'First_Name' => 'Maria',
                'Middle_Name' => 'Santos',
                'Last_Name' => 'Dela Cruz',
                'Date_of_Birth' => '1990-05-15',
                'Contact_Number' => '09181234567',
                'Household_Index' => 1,
                'Is_Verified' => 1,
            ],
            [
                'Resident_ID' => 2,
                'First_Name' => 'Juan',
                'Middle_Name' => 'M.',
                'Last_Name' => 'Cruz',
                'Date_of_Birth' => '1988-02-01',
                'Contact_Number' => '09185551234',
                'Household_Index' => 1,
                'Is_Verified' => 1,
            ],
        ]);

        \DB::table('incident_types')->insert([
            'Category_Id' => 1,
            'Category' => 'Noise Complaint',
        ]);

        \DB::table('incident_blotter')->insert([
            'Incident_ID' => 11,
            'Complainant_Id' => 1,
            'Respondent_Id' => 2,
            'Guest_Id' => null,
            'Category_Id' => 1,
            'Description' => 'Loud music every night',
            'Requested_Relief' => 'Barangay intervention',
            'Date_Reported' => now()->subDay(),
            'Date_Filed' => now()->subDay(),
            'Resolution_Status' => 'Pending',
            'Latitude' => null,
            'Longitude' => null,
            'Handled_By' => null,
        ]);

        $response = $this->withSession(['is_admin' => true, 'admin_user_id' => 7])
            ->get('/admin?tab=cases');

        $response->assertOk();
        $this->assertSame(1, substr_count($response->getContent(), 'Loud music every night'));
    }

    public function test_admin_dashboard_shows_live_cases_and_review_updates_status(): void
    {
        \DB::table('incident_types')->insert([
            'Category_Id' => 1,
            'Category' => 'Noise Complaint',
        ]);

        \DB::table('incident_blotter')->insert([
            'Incident_ID' => 11,
            'Complainant_Id' => 5,
            'Respondent_Id' => 5,
            'Guest_Id' => null,
            'Category_Id' => 1,
            'Description' => 'Loud music every night',
            'Requested_Relief' => 'Barangay intervention',
            'Date_Reported' => now()->subDay(),
            'Date_Filed' => now()->subDay(),
            'Resolution_Status' => 'Pending',
            'Latitude' => null,
            'Longitude' => null,
            'Handled_By' => null,
        ]);

        $this->withSession(['is_admin' => true, 'admin_user_id' => 7])
            ->get('/admin?tab=cases')
            ->assertOk()
            ->assertSee('Noise Complaint')
            ->assertSee('Loud music every night');

        $this->withSession(['is_admin' => true, 'admin_user_id' => 7])
            ->post('/admin/incidents/review', [
                'incident_id' => 11,
                'resolution_status' => 'Active',
            ])
            ->assertRedirect('/admin?tab=cases');

        $this->assertDatabaseHas('incident_blotter', [
            'Incident_ID' => 11,
            'Resolution_Status' => 'Active',
            'Handled_By' => 7,
        ]);
    }
}
