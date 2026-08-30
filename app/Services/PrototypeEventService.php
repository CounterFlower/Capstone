<?php

namespace App\Services;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;

class PrototypeEventService
{
    public function events(): array
    {
        $dbEvents = Event::query()
            ->orderBy('Event_Date')
            ->get();

        if ($dbEvents->isNotEmpty()) {
            return $dbEvents->map(function (Event $event) {
                $date = Carbon::parse($event->Event_Date ?? $event->date);

                return [
                    'id' => (string) $event->Event_ID,
                    'title' => $event->title,
                    'date' => $date->format('F d, Y'),
                    'time' => $date->format('g:i A'),
                    'venue' => $event->venue,
                    'summary' => $event->Summary ?? 'Community event.',
                ];
            })->all();
        }

        return [
            [
                'id' => 'barangay-assembly',
                'title' => 'Barangay Assembly',
                'date' => 'May 16, 2026',
                'time' => '2:00 PM',
                'venue' => 'Covered Court',
                'summary' => 'Monthly assembly for community concerns, reports, and announcements.',
            ],
            [
                'id' => 'medical-mission',
                'title' => 'Medical Mission',
                'date' => 'May 18, 2026',
                'time' => '8:00 AM',
                'venue' => 'Barangay Hall',
                'summary' => 'Free consultation, blood pressure check, and basic health screening.',
            ],
            [
                'id' => 'clean-up-drive',
                'title' => 'Clean-Up Drive',
                'date' => 'May 20, 2026',
                'time' => '7:00 AM',
                'venue' => 'Purok 3 and Purok 4',
                'summary' => 'Community clean-up and drainage clearing activity.',
            ],
        ];
    }

    public function photoGallery(): array
    {
        $files = collect(File::files(resource_path('photos')))
            ->sortBy(fn ($file) => $file->getFilename())
            ->values();

        return $files->map(function ($file, int $index) {
            return [
                'file' => $file->getFilename(),
                'title' => 'EVENT NAME',
                'description' => 'description of the event',
                'key' => 'photo-'.$index,
            ];
        })->all();
    }

    public function registrationPath(): string
    {
        return storage_path('app/prototype_event_registrations.json');
    }

    public function readRegistrations(): array
    {
        return DB::table('event_rsvp as rsvp')
            ->join('resident as resident', 'resident.Resident_ID', '=', 'rsvp.Resident_ID')
            ->join('event as event', 'event.Event_ID', '=', 'rsvp.Event_ID')
            ->leftJoin('household as household', 'household.Household_Index', '=', 'resident.Household_Index')
            ->select([
                'rsvp.RSVP_ID',
                'resident.First_Name',
                'resident.Middle_Name',
                'resident.Last_Name',
                'household.Zone_Purok as purok',
                'event.Event_Name as event_title',
                'rsvp.Date_Registered as submitted_at',
            ])
            ->orderByDesc('rsvp.Date_Registered')
            ->get()
            ->map(function ($row) {
                return [
                    'reference' => 'EVT-'.str_pad((string) $row->RSVP_ID, 3, '0', STR_PAD_LEFT),
                    'resident_name' => trim(implode(' ', array_filter([
                        $row->First_Name,
                        $row->Middle_Name,
                        $row->Last_Name,
                    ]))),
                    'event_title' => $row->event_title,
                    'purok' => $row->purok ?? '',
                    'submitted_at' => Carbon::parse($row->submitted_at)->setTimezone('Asia/Manila')->format('M d, Y h:i A'),
                ];
            })
            ->all();
    }

    public function writeRegistrations(array $registrations): void
    {
        // The live application stores event registrations in event_rsvp, not a JSON file.
    }

    public function getEventById(string $eventId): ?array
    {
        $events = collect($this->events())->keyBy('id');

        return $events->get($eventId);
    }

    public function registerForEvent(array $payload): array
    {
        $event = $this->getEventById((string) $payload['event_id']);

        if (! $event) {
            throw new RuntimeException('Selected event is not available.');
        }

        $residentName = trim((string) $payload['resident_name']);
        $nameParts = preg_split('/\s+/', $residentName, -1, PREG_SPLIT_NO_EMPTY);

        if (count($nameParts) < 2) {
            throw new RuntimeException('Please enter both first and last name.');
        }

        $firstName = $nameParts[0];
        $lastName = $nameParts[count($nameParts) - 1];

        $query = DB::table('resident')
            ->where('First_Name', $firstName)
            ->where('Last_Name', $lastName);

        if (! empty($payload['contact_number'])) {
            $query->where('Contact_Number', trim((string) $payload['contact_number']));
        }

        if (! empty($payload['purok'])) {
            $householdIds = DB::table('household')
                ->where('Zone_Purok', trim((string) $payload['purok']))
                ->pluck('Household_Index');

            $householdIds = $householdIds instanceof \Illuminate\Support\Collection
                ? $householdIds->all()
                : (array) $householdIds;

            if (! empty($householdIds)) {
                $query->whereIn('Household_Index', $householdIds);
            }
        }

        $resident = $query->first([
            'Resident_ID',
            'First_Name',
            'Middle_Name',
            'Last_Name',
            'Contact_Number',
            'Household_Index',
        ]);

        if (! $resident) {
            throw new RuntimeException('No matching resident was found for the provided event registration details.');
        }

        $existingRegistration = DB::table('event_rsvp')
            ->where('Event_ID', (int) $payload['event_id'])
            ->where('Resident_ID', (int) $resident->Resident_ID)
            ->exists();

        if ($existingRegistration) {
            throw new RuntimeException('This resident has already registered for this event.');
        }

        DB::table('event_rsvp')->insert([
            'Event_ID' => (int) $payload['event_id'],
            'Resident_ID' => (int) $resident->Resident_ID,
            'Date_Registered' => now()->setTimezone('Asia/Manila')->format('Y-m-d H:i:s'),
            'Attendance_Status' => 'Confirmed',
        ]);

        return $event;
    }
}
