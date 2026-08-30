<?php

namespace App\Http\Controllers;

use App\Services\PrototypeEventService;
use App\Services\ResidentService;
use Illuminate\Http\Request;

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

    public function reviewIncident(Request $request)
    {
        if (! session('is_admin')) {
            return redirect()->route('admin.login');
        }

        $payload = $request->validate([
            'incident_id' => ['required', 'integer'],
            'resolution_status' => ['required', 'string', 'in:Pending,Active,Resolved,Escalated'],
        ]);

        $this->residentService->reviewIncident(
            (int) $payload['incident_id'],
            $payload['resolution_status'],
            (int) (session('admin_user_id') ?? 1),
        );

        return redirect()->route('admin.dashboard', ['tab' => 'cases'])
            ->with('status', 'Case review saved successfully.');
    }
}
