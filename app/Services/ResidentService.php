<?php

namespace App\Services;

use App\Repositories\ResidentRepository;
use RuntimeException;

class ResidentService
{
    public function __construct(
        protected ResidentRepository $residentRepository,
        protected PrototypeEventService $prototypeEventService,
    ) {}

    public function getDashboardData(): array
    {
        $events = $this->prototypeEventService->events();
        $registrations = $this->prototypeEventService->readRegistrations();

        return [
            'registrations' => $registrations,
            'eventCounts' => collect($events)->map(function (array $event) use ($registrations) {
                return [
                    'title' => $event['title'],
                    'date' => $event['date'],
                    'count' => collect($registrations)->where('event_id', $event['id'])->count(),
                ];
            })->all(),
            'residentProfiles' => $this->residentRepository->getResidentProfiles(),
            'pendingDocumentRequests' => $this->residentRepository->getPendingDocumentRequests(),
        ];
    }

    public function createResident(array $payload): void
    {
        $this->residentRepository->createResident($payload);
    }

    public function assignHousehold(array $payload): void
    {
        $this->residentRepository->assignHousehold($payload);
    }

    public function updateResident(array $payload): void
    {
        $this->residentRepository->updateResidentProfile($payload);
    }

    public function verifyResident(int $residentId, bool $isVerified): void
    {
        $this->residentRepository->verifyResident($residentId, $isVerified);
    }

    public function approveDocumentRequest(int $requestId): bool
    {
        return $this->residentRepository->approveDocumentRequest($requestId);
    }

    public function submitDocumentRequest(array $payload): object
    {
        $resident = $this->residentRepository->findMatchingResident($payload);

        if (! $resident) {
            throw new RuntimeException('No resident record matched the details you entered. Please check your name, birthday, contact number, or address.');
        }

        $requestId = $this->residentRepository->createDocumentRequest([
            'resident_id' => $resident->Resident_ID,
            'years_stayed' => $payload['years_stayed'] ?? null,
            'document_type' => $payload['document_type'],
            'purpose' => $payload['purpose'] ?? null,
        ]);

        return (object) [
            'request_id' => $requestId,
            'resident_name' => $resident->First_Name.' '.($resident->Middle_Name ? $resident->Middle_Name.' ' : '').$resident->Last_Name,
        ];
    }
}
