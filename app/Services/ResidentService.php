<?php

namespace App\Services;

use App\Repositories\ResidentRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

        // -------------------------------------------------------------
        // 1. Document Requests Calculations
        // -------------------------------------------------------------
        $totalDocRequests = DB::table('document_request')->count();
        $releasedDocRequests = DB::table('document_request')
            ->whereRaw('LOWER(TRIM(Status)) = ?', ['released'])
            ->count();
        $docRate = $totalDocRequests > 0 ? round(($releasedDocRequests / $totalDocRequests) * 100, 1) : 0;

        // Determine the best date column for document_request
        $docDateCol = 'Date_Requested';
        if (! Schema::hasColumn('document_request', 'Date_Requested')) {
            $docDateCol = Schema::hasColumn('document_request', 'created_at') ? 'created_at' : 'Request_Date';
        }

        $docTimeline = DB::table('document_request')
            ->select([
                DB::raw("DATE({$docDateCol}) as req_date"),
                DB::raw('COUNT(*) as total_count'),
                DB::raw("SUM(CASE WHEN LOWER(TRIM(Status)) = 'released' THEN 1 ELSE 0 END) as released_count")
            ])
            ->whereNotNull($docDateCol)
            ->groupBy(DB::raw("DATE({$docDateCol})"))
            ->orderBy(DB::raw("DATE({$docDateCol})"), 'asc')
            ->get();

        $docChartLabels = [];
        $docChartRates = [];
        $runningDocTotal = 0;
        $runningDocReleased = 0;

        foreach ($docTimeline as $point) {
            $runningDocTotal += (int) $point->total_count;
            $runningDocReleased += (int) $point->released_count;
            $rate = $runningDocTotal > 0 ? round(($runningDocReleased / $runningDocTotal) * 100, 1) : 0;

            $docChartLabels[] = Carbon::parse($point->req_date)->format('M d');
            $docChartRates[] = $rate;
        }

        // Fallback default coordinates if no timeline records exist
        if (empty($docChartLabels)) {
            $docChartLabels = [now()->subDays(2)->format('M d'), now()->subDay()->format('M d'), now()->format('M d')];
            $docChartRates = [$docRate, $docRate, $docRate];
        }

        // -------------------------------------------------------------
        // 2. Incident Cases Calculations
        // -------------------------------------------------------------
        $caseTable = Schema::hasTable('incident_blotter') ? 'incident_blotter' : 'cases';
        $totalCases = DB::table($caseTable)->count();
        $resolvedCases = DB::table($caseTable)
            ->where(function ($q) {
                $q->whereRaw('LOWER(TRIM(Resolution_Status)) IN (?, ?, ?)', ['resolved', 'settled', 'closed'])
                  ->orWhereRaw('LOWER(TRIM(Resolution_Status)) LIKE ?', ['%settled%']);
            })
            ->count();
        $caseRate = $totalCases > 0 ? round(($resolvedCases / $totalCases) * 100, 1) : 0;

        // Determine the best date column for incident_blotter
        $caseDateCol = 'Date_Reported';
        if (! Schema::hasColumn($caseTable, 'Date_Reported')) {
            $caseDateCol = Schema::hasColumn($caseTable, 'Date_Filed') ? 'Date_Filed' : 'created_at';
        }

        $caseTimeline = DB::table($caseTable)
            ->select([
                DB::raw("DATE({$caseDateCol}) as case_date"),
                DB::raw('COUNT(*) as total_count'),
                DB::raw("SUM(CASE WHEN LOWER(TRIM(Resolution_Status)) IN ('resolved', 'settled', 'closed') THEN 1 ELSE 0 END) as settled_count")
            ])
            ->whereNotNull($caseDateCol)
            ->groupBy(DB::raw("DATE({$caseDateCol})"))
            ->orderBy(DB::raw("DATE({$caseDateCol})"), 'asc')
            ->get();

        $caseChartLabels = [];
        $caseChartRates = [];
        $runningCaseTotal = 0;
        $runningCaseSettled = 0;

        foreach ($caseTimeline as $point) {
            $runningCaseTotal += (int) $point->total_count;
            $runningCaseSettled += (int) $point->settled_count;
            $rate = $runningCaseTotal > 0 ? round(($runningCaseSettled / $runningCaseTotal) * 100, 1) : 0;

            $caseChartLabels[] = Carbon::parse($point->case_date)->format('M d');
            $caseChartRates[] = $rate;
        }

        if (empty($caseChartLabels)) {
            $caseChartLabels = [now()->subDays(2)->format('M d'), now()->subDay()->format('M d'), now()->format('M d')];
            $caseChartRates = [$caseRate, $caseRate, $caseRate];
        }

        return [
            'registrations'           => $registrations,
            'eventCounts'             => collect($events)->map(function (array $event) use ($registrations) {
                return [
                    'title' => $event['title'],
                    'date'  => $event['date'],
                    'count' => collect($registrations)->where('event_id', $event['id'])->count(),
                ];
            })->all(),
            'residentProfiles'        => $this->residentRepository->getResidentProfiles(),
            'pendingDocumentRequests' => $this->getPendingDocumentRequests(),

            // Statistical summary numbers
            'totalDocRequests'        => $totalDocRequests,
            'releasedDocRequests'     => $releasedDocRequests,
            'docRate'                 => $docRate,
            'docChartLabels'          => $docChartLabels,
            'docChartRates'           => $docChartRates,

            'totalCases'              => $totalCases,
            'resolvedCases'           => $resolvedCases,
            'caseRate'                => $caseRate,
            'caseChartLabels'         => $caseChartLabels,
            'caseChartRates'          => $caseChartRates,
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

        $query = DB::table('resident')
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
        $guestId = null;

        if (($payload['reporter_type'] ?? '') === 'guest') {
            $guestId = DB::table('guest')->insertGetId([
                'First_Name'     => $payload['complainant_first_name'] ?? null,
                'Middle_Name'    => $payload['complainant_middle_name'] ?? null,
                'Last_Name'      => $payload['complainant_last_name'] ?? null,
                'Contact_Number' => $payload['guest_contact_number'] ?? null,
                'Address'        => $payload['guest_address'] ?? null,
            ]);
        }

        $latitude = $payload['Latitude'] ?? $payload['latitude'] ?? null;
        $longitude = $payload['Longitude'] ?? $payload['longitude'] ?? null;

        DB::table('incident_blotter')->insert([
            'Complainant_Id'    => $complainantResidentId,
            'Respondent_Id'     => $respondentResidentId,
            'Guest_Id'          => $guestId,
            'Category_Id'       => $payload['category_id'],
            'Description'       => $payload['complaint_details'],
            'Requested_Relief'  => $payload['requested_relief'] ?? null,
            'Date_Reported'     => now()->setTimezone('Asia/Manila')->format('Y-m-d H:i:s'),
            'Date_Filed'        => now()->setTimezone('Asia/Manila')->format('Y-m-d H:i:s'),
            'Resolution_Status' => 'Pending',
            'Latitude'          => $latitude !== null ? (float) $latitude : null,
            'Longitude'         => $longitude !== null ? (float) $longitude : null,
            'Handled_By'        => null,
        ]);
    }

    public function submitDocumentRequest(array $payload): object
    {
        $resident = $this->residentRepository->findMatchingResident($payload);

        if (! $resident) {
            throw new RuntimeException('No resident record matched the details you entered. Please check your name, birthday, contact number, or address.');
        }

        $requestId = $this->residentRepository->createDocumentRequest([
            'resident_id'   => $resident->Resident_ID,
            'years_stayed'  => $payload['years_stayed'] ?? null,
            'document_type' => $payload['document_type'],
            'purpose'       => $payload['purpose'] ?? null,
        ]);

        return (object) [
            'request_id'    => $requestId,
            'resident_name' => $resident->First_Name.' '.($resident->Middle_Name ? $resident->Middle_Name.' ' : '').$resident->Last_Name,
        ];
    }

   public function getPendingDocumentRequests()
    {
        // 1. Detect Address / Purok column
        $addressExpr = 'NULL';
        if (Schema::hasColumn('resident', 'Address')) {
            $addressExpr = 'resident.Address';
        } elseif (Schema::hasColumn('resident', 'Purok')) {
            $addressExpr = "CONCAT('Purok ', resident.Purok, ', Bagumbayan, Daraga, Albay')";
        } elseif (Schema::hasColumn('resident', 'Purok_Number')) {
            $addressExpr = "CONCAT('Purok ', resident.Purok_Number, ', Bagumbayan, Daraga, Albay')";
        } elseif (Schema::hasColumn('resident', 'Zone')) {
            $addressExpr = "CONCAT('Zone ', resident.Zone, ', Bagumbayan, Daraga, Albay')";
        }

        // 2. Detect Contact column
        $contactExpr = 'NULL';
        if (Schema::hasColumn('resident', 'Contact_Number')) {
            $contactExpr = 'resident.Contact_Number';
        } elseif (Schema::hasColumn('resident', 'Contact')) {
            $contactExpr = 'resident.Contact';
        } elseif (Schema::hasColumn('resident', 'Phone_Number')) {
            $contactExpr = 'resident.Phone_Number';
        }

        return DB::table('document_request')
            ->leftJoin('resident', 'document_request.Resident_ID', '=', 'resident.Resident_ID')
            ->select([
                'document_request.*',
                'resident.Date_of_Birth as birthday',
                DB::raw("{$addressExpr} as full_address"),
                DB::raw("{$contactExpr} as contact"),
                DB::raw("TRIM(CONCAT(COALESCE(resident.First_Name, ''), ' ', COALESCE(resident.Middle_Name, ''), ' ', COALESCE(resident.Last_Name, ''))) as resident_name"),
            ])
            ->orderByDesc('document_request.Request_ID')
            ->get();
    }
}