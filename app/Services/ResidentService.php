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

    public function getIncidentCases(): \Illuminate\Support\Collection
    {
        return $this->residentRepository->getIncidentCases();
    }

    public function reviewIncident(int $incidentId, string $resolutionStatus, int $handledBy): bool
    {
        return $this->residentRepository->reviewIncident($incidentId, $resolutionStatus, $handledBy);
    }

    public function residentExistsByName(string $fullName, ?string $dateOfBirth = null, ?string $firstNameOverride = null, ?string $middleNameOverride = null, ?string $lastNameOverride = null): bool
    {
        $normalized = preg_replace('/\s+/', ' ', trim($fullName));

        if ($normalized === '') {
            return false;
        }

        $nameParts = array_values(array_filter(preg_split('/\s+/', $normalized), fn ($part) => $part !== ''));

        if (count($nameParts) < 2) {
            return false;
        }

        $firstName = trim((string) ($firstNameOverride ?? $nameParts[0]));
        $middleName = trim((string) ($middleNameOverride ?? ''));
        $lastName = trim((string) ($lastNameOverride ?? $nameParts[count($nameParts) - 1]));

        $candidateNames = array_values(array_unique(array_filter([
            $normalized,
            trim($firstName.' '.$middleName.' '.$lastName),
            trim($firstName.' '.$lastName),
            trim($firstName.' '.$middleName),
            $firstName,
            $lastName,
            trim($middleName.' '.$lastName),
        ], fn ($value) => $value !== '')));

        $query = \Illuminate\Support\Facades\DB::table('resident')
            ->where(function ($q) use ($candidateNames, $firstName, $middleName, $lastName) {
                $q->where(function ($inner) use ($firstName, $lastName, $middleName) {
                    $inner->whereRaw('TRIM(First_Name) = ?', [$firstName])
                        ->whereRaw('TRIM(Last_Name) = ?', [$lastName]);

                    if ($middleName !== '') {
                        $inner->whereRaw('TRIM(Middle_Name) = ?', [$middleName]);
                    }
                });

                foreach ($candidateNames as $candidate) {
                    $q->orWhereRaw("TRIM(CONCAT(COALESCE(First_Name, ''), ' ', COALESCE(Middle_Name, ''), ' ', COALESCE(Last_Name, ''))) = ?", [$candidate])
                      ->orWhereRaw("TRIM(CONCAT(COALESCE(First_Name, ''), ' ', COALESCE(Last_Name, ''))) = ?", [$candidate]);
                }
            });

        if ($dateOfBirth) {
            $query->whereDate('Date_of_Birth', $dateOfBirth);
        }

        return $query->exists();
    }

    public function submitIncidentReport(array $payload): void
    {
        $complainantResidentId = $payload['complainant_resident_id'] ?? null;
        $respondentResidentId = $payload['respondent_resident_id'] ?? null;

        \Illuminate\Support\Facades\DB::table('incident_blotter')->insert([
            'Complainant_Id' => $complainantResidentId,
            'Respondent_Id' => $respondentResidentId,
            'Guest_Id' => null,
            'Category_Id' => $payload['category_id'],
            'Description' => $payload['complaint_details'],
            'Requested_Relief' => $payload['requested_relief'] ?? null,
            'Date_Reported' => now()->setTimezone('Asia/Manila')->format('Y-m-d H:i:s'),
            'Date_Filed' => now()->setTimezone('Asia/Manila')->format('Y-m-d H:i:s'),
            'Resolution_Status' => 'Pending',
            'Latitude' => null,
            'Longitude' => null,
            'Handled_By' => null,
        ]);
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
