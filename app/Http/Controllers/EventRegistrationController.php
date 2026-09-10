<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRegistrationRequest;
use App\Repositories\ResidentRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class EventRegistrationController extends Controller
{
    public function __construct(
        protected ResidentRepository $residentRepository
    ) {}

    public function store(StoreEventRegistrationRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // 1. Verify Event Exists in Database or Fallback
        $event = DB::table('event')->where('Event_ID', $validated['event_id'])->first();

        if (! $event) {
            // Fallback check if prototypeEvents helper is still partially used
            if (function_exists('prototypeEvents')) {
                $prototypeEvent = collect(prototypeEvents())->keyBy('id')->get((string) $validated['event_id']);
                if ($prototypeEvent) {
                    $event = (object) [
                        'Event_ID' => $prototypeEvent['id'],
                        'Event_Name' => $prototypeEvent['title'],
                    ];
                }
            }
        }

        if (! $event) {
            return back()->withErrors(['event_id' => 'Selected event is not available.'])->withInput();
        }

        // 2. Verify Resident against the resident database records
        $resident = $this->residentRepository->findMatchingResident($validated);

        if (! $resident) {
            return back()->withErrors([
                'first_name' => 'No matching resident record found with this name in the barangay database.',
            ])->withInput();
        }

        // 3. Prevent duplicate registrations for the same event
        $alreadyRegistered = DB::table('event_rsvp')
            ->where('Event_ID', $event->Event_ID)
            ->where('Resident_ID', $resident->Resident_ID)
            ->exists();

        if ($alreadyRegistered) {
            return back()->withErrors([
                'first_name' => 'This resident is already registered for this event.',
            ])->withInput();
        }

        // 4. Save to event_rsvp table
        DB::table('event_rsvp')->insert([
            'Event_ID'          => $event->Event_ID,
            'Resident_ID'       => $resident->Resident_ID,
            'Date_Registered'   => now()->setTimezone('Asia/Manila')->format('Y-m-d H:i:s'),
            'Attendance_Status' => 'Confirmed',
        ]);

        return redirect()->route('public.events', ['event' => $event->Event_ID])
            ->with('status', 'Registration confirmed for ' . $resident->First_Name . ' ' . $resident->Last_Name . ' (' . ($event->Event_Name ?? 'Event') . ').');
    }
}