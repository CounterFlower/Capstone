<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
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

    public function test_admin_event_cover_images_are_uploaded_to_cloudinary_and_store_the_secure_url(): void
    {
        Config::set([
            'services.cloudinary.cloud_name' => 'test-cloud',
            'services.cloudinary.api_key' => 'test-api-key',
            'services.cloudinary.api_secret' => 'test-api-secret',
        ]);

        Http::fake([
            'https://api.cloudinary.com/*' => Http::response([
                'secure_url' => 'https://res.cloudinary.com/test-cloud/image/upload/v123/events/test-image.jpg',
            ]),
        ]);

        $response = $this->withSession(['is_admin' => true])
            ->post('/admin/events', [
                'event_name' => 'Community Meeting',
                'event_date' => '2026-10-20 07:00',
                'location' => 'Covered Court',
                'available_slots' => 50,
                'cover_image' => UploadedFile::fake()->image('cover.jpg'),
            ]);

        $response->assertRedirect('/admin?tab=events');
        $this->assertDatabaseHas('event', [
            'Event_Name' => 'Community Meeting',
            'Cover_Image' => 'https://res.cloudinary.com/test-cloud/image/upload/v123/events/test-image.jpg',
        ]);
        $this->get('/')->assertSee(
            'src="https://res.cloudinary.com/test-cloud/image/upload/v123/events/test-image.jpg"',
            false,
        );

        Http::assertSent(fn (Request $request): bool => str_contains($request->url(), '/image/upload')
            && $request->hasFile('file'));
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
