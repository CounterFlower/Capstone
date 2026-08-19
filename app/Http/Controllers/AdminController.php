<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        if (! session('is_admin')) {
            return redirect()->route('admin.login');
        }

        $events = prototypeEvents();
        $registrations = prototypeReadRegistrations();
        $residentProfiles = DB::table('resident')
            ->leftJoin('household', 'resident.Household_ID', '=', 'household.Household_Index')
            ->select([
                'resident.Resident_ID',
                'resident.First_Name',
                'resident.Middle_Name',
                'resident.Last_Name',
                'resident.Household_ID as Household_Index',
                'household.Household_Id',
                'household.House_Number',
                'resident.Date_of_Birth',
                'resident.Gender',
                'resident.Contact_Number',
                'resident.Is_Verified',
                'household.Zone_Purok',
            ])
            ->orderBy('resident.Last_Name')
            ->get();

        $eventCounts = collect($events)->map(function (array $event) use ($registrations) {
            $count = collect($registrations)
                ->where('event_id', $event['id'])
                ->count();

            return [
                'title' => $event['title'],
                'date' => $event['date'],
                'count' => $count,
            ];
        })->all();

        return view('admin.dashboard', [
            'registrations' => $registrations,
            'eventCounts' => $eventCounts,
            'residentProfiles' => $residentProfiles,
            'activeTab' => $request->query('tab', 'overview'),
        ]);
    }
}
