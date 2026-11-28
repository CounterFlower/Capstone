<?php

namespace App\Http\Controllers;

use App\Services\ResidentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncidentBlotterController extends Controller
{
    public function __construct(protected ResidentService $residentService) {}

    /**
     * Verify admin session and ensure the logged-in official is not a BHW.
     */
    private function authorizeBlotterAccess()
    {
        if (! session('is_admin')) {
            abort(redirect()->route('admin.login'));
        }

        $currentUser = DB::table('system_user')
            ->where('User_ID', session('admin_user_id'))
            ->first();

        $role = strtolower(trim((string) ($currentUser->Role ?? session('admin_role', ''))));

        if (in_array($role, ['bhw', 'barangay health worker'], true)) {
            abort(403, 'Access denied: Barangay Health Workers are not authorized to access or modify incident blotter records.');
        }

        return $currentUser;
    }

    public function reviewIncident(int $incident_id)
    {
        $this->authorizeBlotterAccess();

        $incident = DB::table('incident_blotter')
            ->leftJoin('incident_types', 'incident_blotter.Category_Id', '=', 'incident_types.Category_Id')
            ->leftJoin('resident as complainant', 'incident_blotter.Complainant_Id', '=', 'complainant.Resident_ID')
            ->leftJoin('resident as respondent', 'incident_blotter.Respondent_Id', '=', 'respondent.Resident_ID')
            ->leftJoin('guest', 'incident_blotter.Guest_Id', '=', 'guest.Guest_Id')
            ->where('incident_blotter.Incident_ID', $incident_id)
            ->select([
                'incident_blotter.*',
                'incident_types.Category as category_name',
                DB::raw("TRIM(CONCAT(COALESCE(complainant.First_Name, ''), ' ', COALESCE(complainant.Middle_Name, ''), ' ', COALESCE(complainant.Last_Name, ''))) as complainant_name"),
                DB::raw("TRIM(CONCAT(COALESCE(respondent.First_Name, ''), ' ', COALESCE(respondent.Middle_Name, ''), ' ', COALESCE(respondent.Last_Name, ''))) as respondent_name"),
                DB::raw("TRIM(CONCAT(COALESCE(guest.First_Name, ''), ' ', COALESCE(guest.Middle_Name, ''), ' ', COALESCE(guest.Last_Name, ''))) as guest_name"),
            ])
            ->first();

        abort_if(! $incident, 404, 'Incident case record not found.');

        return view('dashboards.incident_review', [
            'incident' => $incident,
        ]);
    }

    public function printIncident(int $incident_id)
    {
        $this->authorizeBlotterAccess();

        $incident = DB::table('incident_blotter')
            ->leftJoin('incident_types', 'incident_blotter.Category_Id', '=', 'incident_types.Category_Id')
            ->leftJoin('resident as complainant', 'incident_blotter.Complainant_Id', '=', 'complainant.Resident_ID')
            ->leftJoin('resident as respondent', 'incident_blotter.Respondent_Id', '=', 'respondent.Resident_ID')
            ->leftJoin('guest', 'incident_blotter.Guest_Id', '=', 'guest.Guest_Id')
            ->where('incident_blotter.Incident_ID', $incident_id)
            ->select([
                'incident_blotter.*',
                'incident_types.Category as category_name',
                DB::raw("TRIM(CONCAT(COALESCE(complainant.First_Name, ''), ' ', COALESCE(complainant.Middle_Name, ''), ' ', COALESCE(complainant.Last_Name, ''))) as complainant_name"),
                DB::raw("TRIM(CONCAT(COALESCE(respondent.First_Name, ''), ' ', COALESCE(respondent.Middle_Name, ''), ' ', COALESCE(respondent.Last_Name, ''))) as respondent_name"),
                DB::raw("TRIM(CONCAT(COALESCE(guest.First_Name, ''), ' ', COALESCE(guest.Middle_Name, ''), ' ', COALESCE(guest.Last_Name, ''))) as guest_name"),
            ])
            ->first();

        abort_if(! $incident, 404, 'Incident case record not found.');

        return view('dashboards.incident_print', [
            'incident' => $incident,
        ]);
    }

    public function updateIncidentStatus(Request $request, int $incident_id)
    {
        $currentUser = $this->authorizeBlotterAccess();

        $payload = $request->validate([
            'status' => ['required', 'string', 'in:Pending,Active,Resolved,Escalated'],
        ]);

        $this->residentService->reviewIncident(
            (int) $incident_id,
            $payload['status'],
            (int) ($currentUser->User_ID ?? session('admin_user_id') ?? 1)
        );

        return redirect()->route('admin.dashboard', ['tab' => 'cases'])
            ->with('status', 'Case status updated successfully to ' . $payload['status'] . '.');
    }
}
