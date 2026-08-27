<?php

namespace App\Services;

use App\Repositories\EventRepository;

class EventService
{
    public function __construct(protected EventRepository $eventRepository) {}

    public function createEvent(array $data): \App\Models\Event
    {
        return $this->eventRepository->create($data);
    }

    public function getEvents()
    {
        return $this->eventRepository->all();
    }
}
