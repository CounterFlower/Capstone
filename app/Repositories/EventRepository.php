<?php

namespace App\Repositories;

use App\Models\Event;

class EventRepository
{
    public function create(array $data): Event
    {
        return Event::create([
            'Event_Name' => $data['title'],
            'Event_Date' => $data['date'].' '.($data['time'] ?? '00:00'),
            'Location' => $data['venue'],
            'Available_Slots' => $data['available_slots'] ?? null,
            'Summary' => $data['summary'] ?? null,
            'Created_By' => $data['created_by'] ?? null,
        ]);
    }

    public function all()
    {
        return Event::orderBy('Event_Date', 'asc')->get();
    }
}
