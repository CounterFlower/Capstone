<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRegistrationRequest;
use App\Models\EventRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class EventRegistrationController extends Controller
{
    public function store(StoreEventRegistrationRequest $request): RedirectResponse
    {
        $event = collect(prototypeEvents())->keyBy('id')->get($request->string('event_id')->toString());

        if (! $event) {
            return back()->withErrors(['event_id' => 'Selected event is not available.'])->withInput();
        }

        EventRegistration::query()->create([
            ...$request->validated(),
            'reference' => 'EVT-'.Str::upper(Str::random(8)),
            'event_title' => $event['title'],
            'event_date' => $event['date'],
            'event_time' => $event['time'],
            'submitted_at' => now(),
        ]);

        return redirect()->route('public.events', ['event' => $event['id']])
            ->with('status', 'Registration submitted for '.$event['title'].'.');
    }
}
