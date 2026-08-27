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
            'events' => $events,
            'activeTab' => $request->query('tab', 'overview'),
        ]);
    }
}
