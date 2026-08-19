<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ResidentController extends Controller
{
    public function store(Request $request)
    {
        $this->ensureAdmin();

        $payload = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'max:20'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'house_number' => ['required', 'string', 'max:50'],
            'zone_purok' => ['required', 'string', 'max:100'],
            'household_id' => ['nullable', 'integer'],
        ]);

        try {
            DB::transaction(function () use ($payload) {
                $household = DB::table('household')
                    ->where('House_Number', $payload['house_number'])
                    ->where('Zone_Purok', $payload['zone_purok'])
                    ->first();

                if ($payload['household_id'] !== null) {
                    $officialHousehold = DB::table('household')
                        ->where('Household_Id', $payload['household_id'])
                        ->first();

                    if ($officialHousehold) {
                        $householdIndex = $officialHousehold->Household_Index;
                    } elseif ($household) {
                        DB::table('household')
                            ->where('Household_Index', $household->Household_Index)
                            ->update(['Household_Id' => $payload['household_id']]);

                        $householdIndex = $household->Household_Index;
                    } else {
                        $householdIndex = DB::table('household')->insertGetId([
                            'Household_Id' => $payload['household_id'],
                            'House_Number' => $payload['house_number'],
                            'Zone_Purok' => $payload['zone_purok'],
                        ]);
                    }
                } elseif ($household) {
                    $householdIndex = $household->Household_Index;
                } else {
                    $householdIndex = DB::table('household')->insertGetId([
                        'Household_Id' => null,
                        'House_Number' => $payload['house_number'],
                        'Zone_Purok' => $payload['zone_purok'],
                    ]);
                }

                DB::table('resident')->insert([
                    'First_Name' => $payload['first_name'],
                    'Middle_Name' => $payload['middle_name'] ?? null,
                    'Last_Name' => $payload['last_name'],
                    'Date_of_Birth' => $payload['date_of_birth'] ?? null,
                    'Gender' => $payload['gender'] ?? null,
                    'Contact_Number' => $payload['contact_number'] ?? null,
                    'Household_ID' => $householdIndex,
                    'Is_Verified' => 0,
                ]);
            });
        } catch (RuntimeException $exception) {
            return $this->redirectWithErrors($request, 'household', $exception->getMessage());
        } catch (QueryException $exception) {
            return $this->redirectWithErrors($request, 'resident', 'The resident could not be added. Check the supplied database values.');
        }

        return $this->redirectToResidents($request, 'Resident profile added successfully.');
    }

    public function assignHousehold(Request $request)
    {
        $this->ensureAdmin();

        $payload = $request->validate([
            'resident_id' => ['required', 'integer'],
            'household_id' => ['required', 'integer'],
            'house_number' => ['required', 'string', 'max:50'],
            'zone_purok' => ['required', 'string', 'max:100'],
        ]);

        try {
            DB::transaction(function () use ($payload) {
                $resident = DB::table('resident')
                    ->where('Resident_ID', $payload['resident_id'])
                    ->first(['Resident_ID', 'Household_ID']);
                $householdIndex = DB::table('household')
                    ->where('Household_Id', $payload['household_id'])
                    ->value('Household_Index');

                if (! $resident) {
                    throw new RuntimeException('No resident matches the supplied resident ID.');
                }

                if ($householdIndex === null) {
                    if (! $resident->Household_ID) {
                        throw new RuntimeException('The resident is not assigned to a household row.');
                    }

                    DB::table('household')
                        ->where('Household_Index', $resident->Household_ID)
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
                    ->update(['Household_ID' => $householdIndex]);

                if ($resident->Household_ID && $resident->Household_ID !== $householdIndex) {
                    $stillAssigned = DB::table('resident')
                        ->where('Household_ID', $resident->Household_ID)
                        ->exists();

                    if (! $stillAssigned) {
                        DB::table('household')
                            ->where('Household_Index', $resident->Household_ID)
                            ->delete();
                    }
                }
            });
        } catch (RuntimeException $exception) {
            return $this->redirectWithErrors($request, 'assignment', $exception->getMessage());
        } catch (QueryException $exception) {
            return $this->redirectWithErrors($request, 'assignment', 'The household assignment could not be updated.');
        }

        return $this->redirectToResidents($request, 'Resident household assignment updated.');
    }

    public function update(Request $request)
    {
        $this->ensureAdmin();

        $payload = $request->validate([
            'resident_id' => ['required', 'integer'],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'max:20'],
            'contact_number' => ['nullable', 'string', 'max:50'],
        ]);

        if (! DB::table('resident')->where('Resident_ID', $payload['resident_id'])->exists()) {
            return $this->redirectWithErrors($request, 'details', 'No resident matches the supplied resident ID.');
        }

        DB::table('resident')
            ->where('Resident_ID', $payload['resident_id'])
            ->update([
                'First_Name' => $payload['first_name'],
                'Middle_Name' => $payload['middle_name'] ?? null,
                'Last_Name' => $payload['last_name'],
                'Date_of_Birth' => $payload['date_of_birth'] ?? null,
                'Gender' => $payload['gender'] ?? null,
                'Contact_Number' => $payload['contact_number'] ?? null,
            ]);

        return $this->redirectToResidents($request, 'Resident profile details updated.');
    }

    public function verify(Request $request)
    {
        $this->ensureAdmin();

        $payload = $request->validate([
            'resident_id' => ['required', 'integer'],
            'is_verified' => ['required', 'boolean'],
        ]);

        DB::table('resident')
            ->where('Resident_ID', $payload['resident_id'])
            ->update(['Is_Verified' => $payload['is_verified']]);

        return $this->redirectToResidents($request, $payload['is_verified']
            ? 'Resident profile marked as verified.'
            : 'Resident profile returned to pending.');
    }

    private function ensureAdmin(): void
    {
        if (! session('is_admin')) {
            abort(redirect()->route('admin.login'));
        }
    }

    private function redirectToResidents(Request $request, string $status)
    {
        return redirect()->route('admin.dashboard', [
            'tab' => $request->input('active_tab', 'residents'),
        ])->with('status', $status);
    }

    private function redirectWithErrors(Request $request, string $key, string $message)
    {
        return redirect()->route('admin.dashboard', [
            'tab' => $request->input('active_tab', 'residents'),
        ])->withErrors([$key => $message])->withInput();
    }
}
