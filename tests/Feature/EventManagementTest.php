<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_an_event(): void
    {
        $response = $this->withSession(['is_admin' => true])
            ->post('/admin/events', [
                'title' => 'Barangay Clean-up Drive',
                'date' => '2026-09-15',
                'time' => '07:00',
                'venue' => 'Covered Court',
                'available_slots' => 50,
                'summary' => 'Community clean-up drive for all residents.',
            ]);

        $response->assertRedirect('/admin?tab=events');
        $this->assertDatabaseHas('event', [
            'Event_Name' => 'Barangay Clean-up Drive',
            'Location' => 'Covered Court',
            'Available_Slots' => 50,
            'Summary' => 'Community clean-up drive for all residents.',
        ]);
    }

    public function test_resident_can_register_for_event(): void
    {
        $eventId = \DB::table('event')->insertGetId([
            'Event_Name' => 'Barangay Clean-up Drive',
            'Event_Date' => '2026-09-15 07:00:00',
            'Location' => 'Covered Court',
            'Available_Slots' => 50,
            'Summary' => 'Community clean-up drive for all residents.',
        ]);

        $residentId = \DB::table('resident')->insertGetId([
            'First_Name' => 'Maria',
            'Last_Name' => 'Santos',
            'Date_of_Birth' => '1990-05-15',
            'Contact_Number' => '09171234567',
            'Household_Index' => 1,
            'Is_Verified' => 1,
        ]);

        $response = $this->post('/event-registration', [
            'resident_name' => 'Maria Santos',
            'contact_number' => '09171234567',
            'purok' => 'Purok 2',
            'event_id' => (string) $eventId,
        ]);

        $response->assertRedirect('/event-registration?event='.$eventId);
        $this->assertDatabaseHas('event_rsvp', [
            'Event_ID' => $eventId,
            'Resident_ID' => $residentId,
            'Attendance_Status' => 'Confirmed',
        ]);
    }
}
