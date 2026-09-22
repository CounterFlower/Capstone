<?php

test('a resident can register for an event', function () {
    $eventId = DB::table('event')->insertGetId([
        'Event_Name' => 'Barangay Assembly',
        'Event_Date' => '2026-09-15 07:00:00',
        'Location' => 'Covered Court',
        'Available_Slots' => 50,
    ]);

    $residentId = DB::table('resident')->insertGetId([
        'First_Name' => 'Juan',
        'Middle_Name' => 'Dela',
        'Last_Name' => 'Cruz',
        'Date_of_Birth' => '1998-04-12',
        'Contact_Number' => '09171234567',
        'Household_Index' => 1,
        'Is_Verified' => 1,
    ]);

    $response = $this->post(route('public.events.submit'), [
        'first_name' => 'Juan',
        'middle_name' => 'Dela',
        'last_name' => 'Cruz',
        'contact_number' => '09171234567',
        'purok' => 'Purok 1',
        'event_id' => (string) $eventId,
    ]);

    $response->assertRedirect(route('public.events', ['event' => $eventId]));

    expect(DB::table('event_rsvp')->where([
        'Event_ID' => $eventId,
        'Resident_ID' => $residentId,
        'Attendance_Status' => 'Confirmed',
    ])->exists())->toBeTrue();
});
