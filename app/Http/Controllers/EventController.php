<?php

namespace App\Http\Controllers;

use App\Services\EventService;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct(protected EventService $eventService) {}

    public function store(Request $request)
    {
        if (! session('is_admin')) {
            return redirect()->route('admin.login');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:H:i'],
            'venue' => ['required', 'string', 'max:255'],
            'available_slots' => ['nullable', 'integer', 'min:1'],
            'summary' => ['nullable', 'string'],
        ]);

        $this->eventService->createEvent($validated);

        return redirect()->route('admin.dashboard', ['tab' => 'events'])
            ->with('status', 'Event created successfully.');
    }
}
