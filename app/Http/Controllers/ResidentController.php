<?php

namespace App\Http\Controllers;

use App\Services\ResidentService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use RuntimeException;
use Illuminate\Support\Facades\DB;

class ResidentController extends Controller
{
    public function __construct(protected ResidentService $residentService) {}

    public function store(Request $request)
    {
        $this->ensureAdmin();

        $payload = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'place_of_birth' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'max:20'],
            'civil_status' => ['nullable', 'string', 'max:50'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'house_number' => ['required', 'string', 'max:50'],
            'zone_purok' => ['required', 'string', 'max:100'],
            'household_id' => ['nullable', 'integer'],
        ]);

        try {
            $this->residentService->createResident($payload);
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
            $this->residentService->assignHousehold($payload);
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
            'place_of_birth' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'max:20'],
            'civil_status' => ['nullable', 'string', 'max:50'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'house_number' => ['required', 'string', 'max:50'],
            'zone_purok' => ['required', 'string', 'max:100'],
            'household_id' => ['nullable', 'integer'],
        ]);

        try {
            $this->residentService->updateResident($payload);
        } catch (RuntimeException $exception) {
            return $this->redirectWithErrors($request, 'details', $exception->getMessage());
        } catch (QueryException $exception) {
            return $this->redirectWithErrors($request, 'details', 'The resident profile could not be updated.');
        }

        return $this->redirectToResidents($request, 'Resident profile updated successfully.');
    }

    public function verify(Request $request)
    {
        $this->ensureAdmin();

        $payload = $request->validate([
            'resident_id' => ['required', 'integer'],
            'is_verified' => ['required', 'boolean'],
        ]);

        $this->residentService->verifyResident((int) $payload['resident_id'], (bool) $payload['is_verified']);

        return $this->redirectToResidents($request, $payload['is_verified']
            ? 'Resident profile marked as verified.'
            : 'Resident profile returned to pending.');
    }

    public function approveDocumentRequest(Request $request)
    {
        $this->ensureAdmin();

        $payload = $request->validate([
            'request_id' => ['required', 'integer'],
        ]);

        $updated = $this->residentService->approveDocumentRequest((int) $payload['request_id']);

        if (! $updated) {
            return redirect()->route('admin.dashboard', ['tab' => 'requests'])
                ->withErrors(['document_request' => 'The selected request could not be approved.']);
        }

        return redirect()->route('admin.dashboard', ['tab' => 'requests'])
            ->with('status', 'Document request approved successfully.');
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
   public function updateDocumentStatus(Request $request)
{
    $validated = $request->validate([
        'request_id' => ['required', 'integer'],
        'status'     => ['required', 'string', 'in:Pending,Not Approved,Released'],
    ]);

    \DB::table('document_request')
        ->where('Request_ID', $validated['request_id'])
        ->update([
            'Status' => $validated['status'],
        ]);

    return redirect()->route('admin.dashboard', ['tab' => 'requests'])
        ->with('status', 'Document request status updated successfully.');
}
public function printDocument($request_id)
    {
        if (! session('is_admin')) {
            return redirect()->route('admin.login');
        }

        // 1. Fetch document request and resident information
        $document = DB::table('document_request')
            ->leftJoin('resident', 'document_request.Resident_ID', '=', 'resident.Resident_ID')
            ->leftJoin('household', 'resident.Household_Index', '=', 'household.Household_Index')
            ->where('document_request.Request_ID', $request_id)
            ->select([
                'document_request.*',
                'resident.First_Name',
                'resident.Middle_Name',
                'resident.Last_Name',
                'resident.Civil_Status',
                'resident.Contact_Number',
                'household.Zone_Purok',
                DB::raw("TRIM(CONCAT(COALESCE(resident.First_Name, ''), ' ', COALESCE(resident.Middle_Name, ''), ' ', COALESCE(resident.Last_Name, ''))) as resident_name"),
            ])
            ->first();

        abort_if(! $document, 404, 'Document request record not found.');

        // 2. Identify the active logged-in Staff / Admin
        $currentUserId = session('admin_user_id') ?? 1;
        $currentUser = DB::table('system_user')->where('User_ID', $currentUserId)->first();
        
        $staffName = $currentUser->Full_Name ?? session('admin_username') ?? 'Francis Julius G. Castuera';
        $staffRole = ucfirst($currentUser->Role ?? 'Staff-in-Charge');

        // 3. Automatically retrieve Punong Barangay (username 'kap' or User_ID 3 from your seeder)
        $captainUser = DB::table('system_user')
            ->where('Username', 'kap')
            ->orWhere('User_ID', 3)
            ->first();

        $captainName = $captainUser->Full_Name ?? 'Prince Marvin E. Azul';

        return view('dashboards.document_print', [
            'document'    => $document,
            'staffName'   => $staffName,
            'staffRole'   => $staffRole,
            'captainName' => $captainName,
        ]);
    }
}
