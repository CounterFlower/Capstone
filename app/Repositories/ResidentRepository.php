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
            $reporterExpression = "COALESCE(\n                    CONCAT(complainant.First_Name, ' ', COALESCE(complainant.Middle_Name, ''), ' ', complainant.Last_Name),\n                    CONCAT(g.First_Name, ' ', COALESCE(g.Middle_Name, ''), ' ', g.Last_Name)\n                )";
        }

        return $query
            ->select([
                'ib.Incident_ID as Incident_ID',
                'it.Category as Category',
                'ib.Description as Description',
                'ib.Date_Filed as Date_Filed',
                'ib.Resolution_Status as Resolution_Status',
                'ib.Handled_By as Handled_By',
                DB::raw($reporterExpression.' as Reporter_Name'),
            ])
            ->orderByDesc('ib.Date_Filed')
            ->get();
    }

    public function findMatchingResident(array $payload): ?object
    {
        $query = DB::table('resident')
            ->where('First_Name', trim($payload['first_name']))
            ->where('Last_Name', trim($payload['last_name']));

        if (! empty($payload['date_of_birth'])) {
            $query->whereDate('Date_of_Birth', $payload['date_of_birth']);
        }

        if (! empty($payload['contact_number'])) {
            $query->where('Contact_Number', trim($payload['contact_number']));
        }

        if (! empty($payload['house_number']) || ! empty($payload['purok'])) {
            $householdIndexes = DB::table('household')
                ->when(! empty($payload['house_number']), fn ($q) => $q->where('House_Number', trim($payload['house_number'])))
                ->when(! empty($payload['purok']), fn ($q) => $q->where('Zone_Purok', trim($payload['purok'])))
                ->pluck('Household_Index');

            $householdIndexes = $householdIndexes instanceof \Illuminate\Support\Collection
                ? $householdIndexes->all()
                : (array) $householdIndexes;

            if (! empty($householdIndexes)) {
                $query->whereIn('Household_Index', $householdIndexes);
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
                'Pickup_Schedule' => now()->setTimezone('Asia/Manila')->addDays(3)->format('Y-m-d H:i:s'),
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
}
