<?php

use App\Models\EventRegistration;

test('a resident can register for an event', function () {
    $response = $this->post(route('public.events.submit'), [
        'resident_name' => 'Juan Dela Cruz',
        'contact_number' => '09171234567',
        'purok' => 'Purok 1',
        'event_id' => 'barangay-assembly',
    ]);

    $response->assertRedirect(route('public.events', ['event' => 'barangay-assembly']));
    $response->assertSessionHas('status', 'Registration submitted for Barangay Assembly.');

    $registration = EventRegistration::query()->sole();

    expect($registration)
        ->resident_name->toBe('Juan Dela Cruz')
        ->event_title->toBe('Barangay Assembly')
        ->reference->toStartWith('EVT-');
});
