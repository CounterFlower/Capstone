<?php

namespace App\Http\Controllers;

use App\Models\IncidentBlotter;
use App\Repositories\ResidentRepository;
use App\Services\PrototypeEventService;
use App\Services\ResidentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function __construct(
        protected PrototypeEventService $prototypeEventService,
        protected ResidentService $residentService,
        protected ResidentRepository $residentRepository
    ) {}

    /**
     * Display the main administrative dashboard.
     */
    public function dashboard(Request $request)
    {
        if (! session('is_admin')) {
            return redirect()->route('admin.login');
        }

        // --- 2-Minute Inactivity Timeout Check ---
        $lastActivity = session('admin_last_activity');
        $timeoutSeconds = 120; // 2 minutes

        if ($lastActivity && (time() - $lastActivity > $timeoutSeconds)) {
            $request->session()->forget([
                'is_admin',
                'admin_user_id',
                'admin_username',
                'admin_full_name',
                'admin_role',
                'admin_last_activity',
            ]);

            return redirect()->route('admin.login')
                ->with('status', 'You have been logged out due to 2 minutes of inactivity.');
        }

        // Keep session timestamp refreshed on active navigation
        session(['admin_last_activity' => time()]);

        // Retrieve service dashboard analytics (includes rates and Chart.js datasets)
        $dashboardData = $this->residentService->getDashboardData();

        // 1. Events list with aggregated registration count
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

        // 2. Event filter for attendee registrations
        $selectedEventFilter = $request->filled('event_filter')
            ? (int) $request->input('event_filter')
            : null;

        // 3. Enlisted residents retrieved via repository
        $eventRegistrations = $this->residentRepository->getEventRegistrations($selectedEventFilter);

        // Keep active tab on 'events' if event_filter is applied or tab is requested
        $activeTab = $request->filled('event_filter')
            ? 'events'
            : $request->query('tab', 'overview');

        return view('admin.dashboard', array_merge($dashboardData, [
            'caseRecords'         => $this->residentService->getIncidentCases(),
            'events'              => $events,
            'eventsList'          => $events,
            'eventRegistrations'  => $eventRegistrations,
            'selectedEventFilter' => $selectedEventFilter,
            'activeTab'           => $activeTab,
        ]));
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

        $payload = $request->validate([
            'status' => ['required', 'string', 'in:Pending,Active,Resolved,Escalated'],
        ]);

        $this->residentService->reviewIncident(
            (int) $incident_id,
            $payload['status'],
            (int) (session('admin_user_id') ?? 1)
        );

        return redirect()->route('admin.incidents.review', $incident_id)
            ->with('status', 'Case status updated successfully to ' . $payload['status'] . '.');
    }

    /**
     * Show admin login form (GET: /admin/login)
     */
    public function showLoginForm()
    {
        if (session('is_admin')) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    /**
     * Authenticate admin / staff user (POST: /admin/login)
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = DB::table('system_user')
            ->where('Username', $credentials['username'])
            ->where('Is_Active', 1)
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->Password_Hash)) {
            return back()->withErrors(['username' => 'Invalid credentials or inactive account.'])->withInput();
        }

        // Initialize credentials and the 2-minute activity timer
        session([
            'is_admin'            => true,
            'admin_user_id'       => $user->User_ID,
            'admin_username'      => $user->Username,
            'admin_full_name'     => $user->Full_Name,
            'admin_role'          => $user->Role,
            'admin_last_activity' => time(),
        ]);

        return redirect()->route('admin.dashboard');
    }

    /**
     * Log out admin user (POST or GET: /admin/logout)
     */
    public function logout(Request $request)
    {
        $request->session()->forget([
            'is_admin',
            'admin_user_id',
            'admin_username',
            'admin_full_name',
            'admin_role',
            'admin_last_activity',
        ]);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $message = $request->query('reason') === 'inactivity'
            ? 'Logged out automatically after 2 minutes of inactivity.'
            : 'You have been successfully logged out.';

        return redirect()->route('admin.login')->with('status', $message);
    }
}