<?php

namespace App\Http\Controllers;

use App\Models\IncidentBlotter;
use App\Services\PrototypeEventService;
use App\Services\ResidentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function __construct(
        protected PrototypeEventService $prototypeEventService,
        protected ResidentService $residentService,
    ) {}

    public function dashboard(Request $request)
    {
        if (! session('is_admin')) {
            return redirect()->route('admin.login');
        }

        $dashboardData = $this->residentService->getDashboardData();
        $events = app(\App\Services\EventService::class)->getEvents();

        return view('admin.dashboard', [
            'registrations' => $dashboardData['registrations'],
            'eventCounts' => $dashboardData['eventCounts'],
            'residentProfiles' => $dashboardData['residentProfiles'],
            'pendingDocumentRequests' => $dashboardData['pendingDocumentRequests'],
            'caseRecords' => $this->residentService->getIncidentCases(),
            'events' => $events,
            'activeTab' => $request->query('tab', 'overview'),
        ]);
    }

    /**
     * Display the full incident review page (GET: admin/incidents/{incident_id}/review)
     */
public function reviewIncident($incident_id)
{
    if (! session('is_admin')) {
        return redirect()->route('admin.login');
    }

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

    // Point to dashboards.incident_review to match your physical folder path:
    return view('dashboards.incident_review', [
        'incident' => $incident,
    ]);
}

    /**
     * Update case status from inside the review page (PATCH: admin/incidents/{incident_id}/status)
     */
    public function updateIncidentStatus(Request $request, $incident_id)
    {
        if (! session('is_admin')) {
            return redirect()->route('admin.login');
        }

        // Validated against the Resolution_Status enum defined in the schema
        $payload = $request->validate([
            'status' => ['required', 'string', 'in:Pending,Active,Resolved,Escalated'],
        ]);

        // Updates through your existing service method
        $this->residentService->reviewIncident(
            (int) $incident_id,
            $payload['status'],
            (int) (session('admin_user_id') ?? 1)
        );

        return redirect()->route('admin.incidents.review', $incident_id)
            ->with('status', 'Case status updated successfully to ' . $payload['status'] . '.');
    }
}

// Inside AdminController.php -> dashboard()
$events = DB::table('event')
    ->leftJoin('event_rsvp', 'event.Event_ID', '=', 'event_rsvp.Event_ID')
    ->select([
        'event.Event_ID',
        'event.Event_Name',
        'event.Event_Date',
        'event.End_Date',
        'event.Location',
        'event.Available_Slots',
        'event.Cover_Image',
        DB::raw('COUNT(event_rsvp.RSVP_ID) as registered_count'),
    ])
    ->groupBy(
        'event.Event_ID',
        'event.Event_Name',
        'event.Event_Date',
        'event.End_Date',
        'event.Location',
        'event.Available_Slots',
        'event.Cover_Image'
    )
    ->orderBy('event.Event_Date', 'asc')
    ->get();