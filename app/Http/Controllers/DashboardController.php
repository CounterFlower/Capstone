<?php

namespace App\Http\Controllers;

use App\Repositories\ResidentRepository;
use App\Services\PrototypeEventService;
use App\Services\ResidentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(
        protected PrototypeEventService $prototypeEventService,
        protected ResidentService $residentService,
        protected ResidentRepository $residentRepository
    ) {}

    public function dashboard(Request $request)
    {
        if (! session('is_admin')) {
            return redirect()->route('admin.login');
        }

        $lastActivity = session('admin_last_activity');
        $timeoutSeconds = 120;

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

        session(['admin_last_activity' => time()]);

        $dashboardData = $this->residentService->getDashboardData();

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

        $selectedEventFilter = $request->filled('event_filter')
            ? (int) $request->input('event_filter')
            : null;

        $activeTab = $request->filled('event_filter')
            ? 'events'
            : $request->query('tab', 'overview');

        return view('admin.dashboard', array_merge($dashboardData, [
            'caseRecords'         => $this->residentService->getIncidentCases(),
            'events'              => $events,
            'eventsList'          => $events,
            'eventRegistrations'  => $this->residentRepository->getEventRegistrations($selectedEventFilter),
            'selectedEventFilter' => $selectedEventFilter,
            'activeTab'           => $activeTab,
        ]));
    }
}
