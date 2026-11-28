<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use App\Services\ResidentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DocumentRequestController extends Controller
{
    public function __construct(protected ResidentService $residentService) {}

    /**
     * Verify admin session and ensure the logged-in official is not a BHW.
     */
    private function authorizeDocumentAccess()
    {
        if (! session('is_admin')) {
            abort(redirect()->route('admin.login'));
        }

        $currentUser = DB::table('system_user')
            ->where('User_ID', session('admin_user_id'))
            ->first();

        $role = strtolower(trim((string) ($currentUser->Role ?? session('admin_role', ''))));

        if (in_array($role, ['bhw', 'barangay health worker'], true)) {
            abort(403, 'Access denied: Barangay Health Workers are not authorized to manage, approve, or print document requests.');
        }

        return $currentUser;
    }

    public function approveDocumentRequest(Request $request)
    {
        $this->authorizeDocumentAccess();

        $payload =$request->validate([
            'request_id' => ['required', 'integer'],
        ]);

        $updated = $this->residentService->approveDocumentRequest((int)$payload['request_id']);

        if (! $updated) {
            return redirect()->route('admin.dashboard', ['tab' => 'requests'])
                ->withErrors(['document_request' => 'The selected request could not be approved.']);
        }

        return redirect()->route('admin.dashboard', ['tab' => 'requests'])
            ->with('status', 'Document request approved successfully.');
    }

    public function updateDocumentStatus(Request $request)
    {
        $currentUser =$this->authorizeDocumentAccess();

        $validated =$request->validate([
            'request_id' => ['required', 'integer'],
            'status'     => ['required', 'string', 'in:Pending,Not Approved,Released'],
        ]);

        $updateData = ['Status' =>$validated['status']];

        if ($currentUser && in_array($validated['status'], ['Released', 'Not Approved'], true)) {
            $updateData['Processed_By'] =$currentUser->User_ID;
        }

        DocumentRequest::query()
            ->whereKey($validated['request_id'])
            ->update($updateData);

        return redirect()->route('admin.dashboard', ['tab' => 'requests'])
            ->with('status', 'Document request status updated successfully.');
    }

    public function printDocument(int $request_id)
    {
        $currentUser =$this->authorizeDocumentAccess();

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

        if (empty($document->QR_Hash)) {$qrHash = hash('sha256', $document->Request_ID . '-' .$document->Resident_ID . '-' . Str::random(16));

            DB::table('document_request')
                ->where('Request_ID', $document->Request_ID)
                ->update(['QR_Hash' => $qrHash]);

            $document->QR_Hash =$qrHash;
        }

        $verifyUrl = rtrim(config('app.url'), '/') . '/verify/document/' .$document->QR_Hash;

        $staffName =$currentUser->Full_Name ?? session('admin_username') ?? 'Francis Julius G. Castuera';
        $staffRole = ucfirst($currentUser->Role ?? 'Staff-in-Charge');

        $captainUser = DB::table('system_user')
            ->whereIn('Role', ['Punong Barangay'])
            ->orWhere('Username', 'kap')
            ->orWhere('User_ID', 3)
            ->first();

        $captainName =$captainUser->Full_Name ?? 'Prince Marvin E. Azul';

        return view('dashboards.document_print', [
            'document'    => $document,
            'staffName'   => $staffName,
            'staffRole'   => $staffRole,
            'captainName' => $captainName,
            'verifyUrl'   => $verifyUrl,
        ]);
    }

    public function verifyPublicDocument(string $hash)
    {
        $document = DB::table('document_request')
            ->leftJoin('resident', 'document_request.Resident_ID', '=', 'resident.Resident_ID')
            ->leftJoin('household', 'resident.Household_Index', '=', 'household.Household_Index')
            ->where('document_request.QR_Hash', $hash)
            ->select([
                'document_request.*',
                'resident.First_Name',
                'resident.Middle_Name',
                'resident.Last_Name',
                'resident.Civil_Status',
                'household.Zone_Purok',
                DB::raw("TRIM(CONCAT(COALESCE(resident.First_Name, ''), ' ', COALESCE(resident.
