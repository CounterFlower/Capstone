<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResidentRepository
{
    public function getResidentProfiles(): \Illuminate\Support\Collection
    {
        if (! Schema::hasTable('resident')) {
            return collect();
        }

        return DB::table('resident')
            ->leftJoin('household', 'resident.Household_Index', '=', 'household.Household_Index')
            ->select([
                'resident.Resident_ID',
                'resident.First_Name',
                'resident.Middle_Name',
                'resident.Last_Name',
                'resident.Household_Index as Household_Index',
                'household.Household_Id',
                'household.House_Number',
                'resident.Date_of_Birth',
                'resident.Place_of_Birth',
                'resident.Gender',
                'resident.Civil_Status',
                'resident.Contact_Number',
                'resident.Is_Verified',
                'household.Zone_Purok',
            ])
            ->orderBy('resident.Last_Name')
            ->get();
    }

    public function getPendingDocumentRequests(): \Illuminate\Support\Collection
    {
        if (! Schema::hasTable('document_request') || ! Schema::hasTable('resident')) {
            return collect();
        }

        return DB::table('document_request')
            ->join('resident', 'document_request.Resident_ID', '=', 'resident.Resident_ID')
            ->select([
                'document_request.Request_ID',
                'document_request.Resident_ID',
                'document_request.Document_Type',
                'document_request.Purpose',
                'document_request.Date_Requested',
                'document_request.Status',
                DB::raw("CONCAT(resident.First_Name, ' ', COALESCE(resident.Middle_Name, ''), ' ', resident.Last_Name) as Resident_Name"),
            ])
            ->where('document_request.Status', 'Pending')
            ->orderByDesc('document_request.Date_Requested')
            ->get();
    }

    public function getIncidentCases(): \Illuminate\Support\Collection
    {
        if (! Schema::hasTable('incident_blotter') || ! Schema::hasTable('incident_types')) {
            return collect();
        }

        $query = DB::table('incident_blotter as ib')
            ->leftJoin('incident_types as it', 'it.Category_Id', '=', 'ib.Category_Id')
            ->leftJoin('resident as complainant', 'complainant.Resident_ID', '=', 'ib.Complainant_Id')
            ->leftJoin('resident as respondent', 'respondent.Resident_ID', '=', 'ib.Respondent_Id');

        if (Schema::hasTable('guest')) {
            $query->leftJoin('guest as g', 'g.Guest_Id', '=', 'ib.Guest_Id');
        }

        $reporterExpression = "CONCAT(complainant.First_Name, ' ', COALESCE(complainant.Middle_Name, ''), ' ', complainant.Last_Name)";

        if (Schema::hasTable('guest')) {
            $reporterExpression = "COALESCE(
                    CONCAT(complainant.First_Name, ' ', COALESCE(complainant.Middle_Name, ''), ' ', complainant.Last_Name),
                    CONCAT(g.First_Name, ' ', COALESCE(g.Middle_Name, ''), ' ', g.Last_Name)
                )";
        }

        return $query
            ->select([
                'ib.Incident_ID as Incident_ID',
                'it.Category as Category',
                'ib.Description as Description',
                'ib.Date_Filed as Date_Filed',
                'ib.Resolution_Status as Resolution_Status',
                'ib.Handled_By as Handled_By',
                'ib.Latitude as Latitude',
                'ib.Longitude as Longitude',
                DB::raw($reporterExpression.' as Reporter_Name'),
            ])
            ->orderByDesc('ib.Date_Filed')
            ->get();
    }

    public function findMatchingResident(array $payload): ?object
    {
        $query = DB::table('resident');

        $singleName = trim($payload['resident_name'] ?? ($payload['name'] ?? ''));
        $firstName  = strtolower(trim($payload['first_name'] ?? ''));
        $middleName = strtolower(trim($payload['middle_name'] ?? ''));
        $lastName   = strtolower(trim($payload['last_name'] ?? ''));

        // 1. Name Resolution (Supports single-field full string or 3 discrete fields)
        if (! empty($singleName)) {
            $rawName = strtolower($singleName);

            $query->where(function ($q) use ($rawName) {
                $q->whereRaw("LOWER(CONCAT_WS(' ', TRIM(First_Name), NULLIF(TRIM(Middle_Name), ''), TRIM(Last_Name))) = ?", [$rawName])
                  ->orWhereRaw("LOWER(CONCAT(TRIM(First_Name), ' ', TRIM(Last_Name))) = ?", [$rawName])
                  ->orWhere(function ($sub) use ($rawName) {
                      $sub->whereRaw("? LIKE LOWER(CONCAT('%', TRIM(Last_Name), '%'))", [$rawName])
                          ->whereRaw("? LIKE LOWER(CONCAT('%', TRIM(First_Name), '%'))", [$rawName]);
                  });
            });
        } else {
            // Split fields verification
            if (! empty($lastName)) {
                $query->whereRaw('LOWER(TRIM(Last_Name)) = ?', [$lastName]);
            }

            if (! empty($firstName)) {
                $query->where(function ($q) use ($firstName) {
                    $q->whereRaw('LOWER(TRIM(First_Name)) LIKE ?', ["%{$firstName}%"])
                      ->orWhereRaw('? LIKE LOWER(CONCAT("%", TRIM(First_Name), "%"))', [$firstName]);
                });
            }

            // Middle name is optional: match only if provided
            if (! empty($middleName)) {
                $query->where(function ($q) use ($middleName) {
                    $q->whereRaw('LOWER(TRIM(Middle_Name)) LIKE ?', ["%{$middleName}%"])
                      ->orWhereRaw('? LIKE LOWER(CONCAT("%", TRIM(Middle_Name), "%"))', [$middleName]);
                });
            }
        }

        // 2. Date of Birth Check (optional)
        if (! empty($payload['date_of_birth'])) {
            $query->whereDate('Date_of_Birth', $payload['date_of_birth']);
        }

        // 3. Flexible Household / Purok Matching (Digit-friendly)
        if (! empty($payload['purok']) || ! empty($payload['house_number'])) {
            $householdQuery = DB::table('household');

            if (! empty($payload['house_number'])) {
                $householdQuery->whereRaw('LOWER(TRIM(House_Number)) = ?', [strtolower(trim($payload['house_number']))]);
            }

            if (! empty($payload['purok'])) {
                $rawPurok = strtolower(trim($payload['purok']));
                $purokNumber = preg_replace('/[^0-9]/', '', $rawPurok);

                $householdQuery->where(function ($h) use ($rawPurok, $purokNumber) {
                    // Direct string match or substring match
                    $h->whereRaw('LOWER(TRIM(Zone_Purok)) = ?', [$rawPurok])
                      ->orWhereRaw('LOWER(Zone_Purok) LIKE ?', ["%{$rawPurok}%"]);

                    // Digit match: allows "6" to match "Purok 6", "Zone 6", or "6"
                    if (! empty($purokNumber)) {
                        $h->orWhereRaw("REGEXP_REPLACE(Zone_Purok, '[^0-9]', '') = ?", [$purokNumber])
                          ->orWhereRaw("LOWER(Zone_Purok) LIKE ?", ["%{$purokNumber}%"]);
                    }
                });
            }

            $indexes = $householdQuery->pluck('Household_Index')->all();

            if (! empty($indexes)) {
                $query->whereIn('Household_Index', $indexes);
            }
        }

        return $query->first([
            'Resident_ID',
            'First_Name',
            'Middle_Name',
            'Last_Name',
            'Date_of_Birth',
            'Contact_Number',
            'Household_Index',
            'Is_Verified',
        ]);
    }

    public function createDocumentRequest(array $data): int
    {
        return DB::table('document_request')->insertGetId([
            'Resident_ID' => $data['resident_id'],
            'Date_Requested' => now()->setTimezone('Asia/Manila')->format('Y-m-d H:i:s'),
            'Years_Stayed' => $data['years_stayed'] ?? null,
            'Document_Type' => $data['document_type'],
            'Purpose' => $data['purpose'] ?? null,
            'Status' => 'Pending',
        ]);
    }

    public function getOrCreateHouseholdForResident(array $payload): int
    {
        $household = DB::table('household')
            ->where('House_Number', $payload['house_number'])
            ->where('Zone_Purok', $payload['zone_purok'])
            ->first();

        if ($payload['household_id'] !== null) {
            $officialHousehold = DB::table('household')
                ->where('Household_Id', $payload['household_id'])
                ->first();

            if ($officialHousehold) {
                return $officialHousehold->Household_Index;
            }

            if ($household) {
                DB::table('household')
                    ->where('Household_Index', $household->Household_Index)
                    ->update(['Household_Id' => $payload['household_id']]);

                return $household->Household_Index;
            }

            return DB::table('household')->insertGetId([
                'Household_Id' => $payload['household_id'],
                'House_Number' => $payload['house_number'],
                'Zone_Purok' => $payload['zone_purok'],
            ]);
        }

        if ($household) {
            return $household->Household_Index;
        }

        return DB::table('household')->insertGetId([
            'Household_Id' => null,
            'House_Number' => $payload['house_number'],
            'Zone_Purok' => $payload['zone_purok'],
        ]);
    }

    public function createResident(array $payload): void
    {
        $householdIndex = $this->getOrCreateHouseholdForResident($payload);

        DB::table('resident')->insert([
            'First_Name' => $payload['first_name'],
            'Middle_Name' => $payload['middle_name'] ?? null,
            'Last_Name' => $payload['last_name'],
            'Date_of_Birth' => $payload['date_of_birth'] ?? null,
            'Place_of_Birth' => $payload['place_of_birth'] ?? null,
            'Gender' => $payload['gender'] ?? null,
            'Civil_Status' => $payload['civil_status'] ?? null,
            'Contact_Number' => $payload['contact_number'] ?? null,
            'Household_Index' => $householdIndex,
            'Is_Verified' => 0,
        ]);
    }

    public function getResidentById(int $residentId): ?object
    {
        return DB::table('resident')
            ->where('Resident_ID', $residentId)
            ->first(['Resident_ID', 'Household_Index']);
    }

    public function getHouseholdById(int $householdId): ?object
    {
        return DB::table('household')
            ->where('Household_Id', $householdId)
            ->first(['Household_Index', 'Household_Id']);
    }

    public function getAddressHousehold(string $houseNumber, string $zonePurok): ?object
    {
        return DB::table('household')
            ->where('House_Number', $houseNumber)
            ->where('Zone_Purok', $zonePurok)
            ->first(['Household_Index', 'Household_Id']);
    }

    public function getHouseholdByIndex(int $householdIndex): ?object
    {
        return DB::table('household')
            ->where('Household_Index', $householdIndex)
            ->first(['Household_Index', 'Household_Id']);
    }

    public function updateResidentProfile(array $payload): void
    {
        $resident = $this->getResidentById((int) $payload['resident_id']);

        if (! $resident) {
            throw new \RuntimeException('No resident matches the supplied resident ID.');
        }

        $currentHousehold = $resident->Household_Index
            ? $this->getHouseholdByIndex((int) $resident->Household_Index)
            : null;

        $matchingAddressHousehold = $this->getAddressHousehold($payload['house_number'], $payload['zone_purok']);

        if ($payload['household_id'] !== null) {
            $officialHousehold = $this->getHouseholdById((int) $payload['household_id']);

            if ($officialHousehold) {
                DB::table('household')
                    ->where('Household_Index', $officialHousehold->Household_Index)
                    ->update([
                        'House_Number' => $payload['house_number'],
                        'Zone_Purok' => $payload['zone_purok'],
                    ]);

                $householdIndex = $officialHousehold->Household_Index;
            } elseif ($matchingAddressHousehold) {
                DB::table('household')
                    ->where('Household_Index', $matchingAddressHousehold->Household_Index)
                    ->update(['Household_Id' => $payload['household_id']]);

                $householdIndex = $matchingAddressHousehold->Household_Index;
            } elseif ($currentHousehold) {
                DB::table('household')
                    ->where('Household_Index', $currentHousehold->Household_Index)
                    ->update([
                        'Household_Id' => $payload['household_id'],
                        'House_Number' => $payload['house_number'],
                        'Zone_Purok' => $payload['zone_purok'],
                    ]);

                $householdIndex = $currentHousehold->Household_Index;
            } else {
                $householdIndex = DB::table('household')->insertGetId([
                    'Household_Id' => $payload['household_id'],
                    'House_Number' => $payload['house_number'],
                    'Zone_Purok' => $payload['zone_purok'],
                ]);
            }
        } elseif ($currentHousehold) {
            DB::table('household')
                ->where('Household_Index', $currentHousehold->Household_Index)
                ->update([
                    'House_Number' => $payload['house_number'],
                    'Zone_Purok' => $payload['zone_purok'],
                ]);

            $householdIndex = $currentHousehold->Household_Index;
        } elseif ($matchingAddressHousehold) {
            $householdIndex = $matchingAddressHousehold->Household_Index;
        } else {
            $householdIndex = DB::table('household')->insertGetId([
                'Household_Id' => null,
                'House_Number' => $payload['house_number'],
                'Zone_Purok' => $payload['zone_purok'],
            ]);
        }

        DB::table('resident')
            ->where('Resident_ID', $payload['resident_id'])
            ->update([
                'First_Name' => $payload['first_name'],
                'Middle_Name' => $payload['middle_name'] ?? null,
                'Last_Name' => $payload['last_name'],
                'Date_of_Birth' => $payload['date_of_birth'] ?? null,
                'Place_of_Birth' => $payload['place_of_birth'] ?? null,
                'Gender' => $payload['gender'] ?? null,
                'Civil_Status' => $payload['civil_status'] ?? null,
                'Contact_Number' => $payload['contact_number'] ?? null,
                'Household_Index' => $householdIndex,
            ]);

        if ($resident->Household_Index && (int) $resident->Household_Index !== (int) $householdIndex) {
            $stillAssigned = DB::table('resident')
                ->where('Household_Index', $resident->Household_Index)
                ->exists();

            if (! $stillAssigned) {
                DB::table('household')
                    ->where('Household_Index', $resident->Household_Index)
                    ->delete();
            }
        }
    }

    public function assignHousehold(array $payload): void
    {
        $resident = $this->getResidentById((int) $payload['resident_id']);

        if (! $resident) {
            throw new \RuntimeException('No resident matches the supplied resident ID.');
        }

        $householdIndex = DB::table('household')
            ->where('Household_Id', $payload['household_id'])
            ->value('Household_Index');

        if ($householdIndex === null) {
            if (! $resident->Household_Index) {
                throw new \RuntimeException('The resident is not assigned to a household row.');
            }

            DB::table('household')
                ->where('Household_Index', $resident->Household_Index)
                ->update([
                    'Household_Id' => $payload['household_id'],
                    'House_Number' => $payload['house_number'],
                    'Zone_Purok' => $payload['zone_purok'],
                ]);

            return;
        }

        DB::table('household')
            ->where('Household_Index', $householdIndex)
            ->update([
                'House_Number' => $payload['house_number'],
                'Zone_Purok' => $payload['zone_purok'],
            ]);

        DB::table('resident')
            ->where('Resident_ID', $resident->Resident_ID)
            ->update(['Household_Index' => $householdIndex]);

        if ($resident->Household_Index && (int) $resident->Household_Index !== (int) $householdIndex) {
            $stillAssigned = DB::table('resident')
                ->where('Household_Index', $resident->Household_Index)
                ->exists();

            if (! $stillAssigned) {
                DB::table('household')
                    ->where('Household_Index', $resident->Household_Index)
                    ->delete();
            }
        }
    }

    public function verifyResident(int $residentId, bool $isVerified): void
    {
        DB::table('resident')
            ->where('Resident_ID', $residentId)
            ->update(['Is_Verified' => $isVerified]);
    }

    public function approveDocumentRequest(int $requestId): bool
    {
        return (bool) DB::table('document_request')
            ->where('Request_ID', $requestId)
            ->update([
                'Status' => 'Approved',
                'Pickup_Schedule' => now()->setTimezone('Asia/Manila')->format('Y-m-d H:i:s'),
            ]);
    }

    public function reviewIncident(int $incidentId, string $resolutionStatus, int $handledBy): bool
    {
        return (bool) DB::table('incident_blotter')
            ->where('Incident_ID', $incidentId)
            ->update([
                'Resolution_Status' => $resolutionStatus,
                'Handled_By' => $handledBy,
            ]);
    }
   public function getEventRegistrations(?int $eventId = null)
    {
        $query = DB::table('event_rsvp')
            ->join('resident', 'event_rsvp.Resident_ID', '=', 'resident.Resident_ID')
            ->join('event', 'event_rsvp.Event_ID', '=', 'event.Event_ID')
            ->leftJoin('household', 'resident.Household_Index', '=', 'household.Household_Index')
            ->select([
                'event_rsvp.*',
                'resident.First_Name',
                'resident.Middle_Name',
                'resident.Last_Name',
                'resident.Date_of_Birth',
                'resident.Contact_Number',
                'household.House_Number',
                'household.Zone_Purok',
                'event.Event_Name',
                'event.Event_Date',
            ]);

        if ($eventId) {
            $query->where('event_rsvp.Event_ID', $eventId);
        }

        return $query->orderByDesc('event_rsvp.Date_Registered')->get();
    }
}