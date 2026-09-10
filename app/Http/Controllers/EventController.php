<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class EventController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_name'      => ['required', 'string', 'max:255'],
            'event_date'      => ['required', 'date'],
            'end_date'        => ['nullable', 'date', 'after:event_date'],
            'location'        => ['required', 'string', 'max:255'],
            'available_slots' => ['required', 'integer', 'min:1'],
            'summary'         => ['nullable', 'string'],
            'cover_image'     => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $imageFilename = null;
        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $imageFilename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/events'), $imageFilename);
        }

        DB::table('event')->insert([
            'Event_Name'      => $validated['event_name'],
            'Event_Date'      => $validated['event_date'],
            'End_Date'        => $validated['end_date'] ?? null,
            'Location'        => $validated['location'],
            'Available_Slots' => $validated['available_slots'],
            'Summary'         => $validated['summary'] ?? null,
            'Cover_Image'     => $imageFilename,
            'Created_By'      => session('admin_user_id') ?? 1,
        ]);

        return redirect()->route('admin.dashboard', ['tab' => 'events'])
            ->with('status', 'Event scheduled successfully.');
    }

    public function edit($event_id)
    {
        $event = DB::table('event')->where('Event_ID', $event_id)->first();
        abort_if(! $event, 404);

        return view('dashboards.event_edit', compact('event'));
    }

    public function update(Request $request, $event_id)
    {
        $validated = $request->validate([
            'event_name'      => ['required', 'string', 'max:255'],
            'event_date'      => ['required', 'date'],
            'end_date'        => ['nullable', 'date', 'after:event_date'],
            'location'        => ['required', 'string', 'max:255'],
            'available_slots' => ['required', 'integer', 'min:1'],
            'summary'         => ['nullable', 'string'],
            'cover_image'     => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $event = DB::table('event')->where('Event_ID', $event_id)->first();
        abort_if(! $event, 404);

        $imageFilename = $event->Cover_Image;
        if ($request->hasFile('cover_image')) {
            // Delete older file if exists
            if ($imageFilename && File::exists(public_path('uploads/events/' . $imageFilename))) {
                File::delete(public_path('uploads/events/' . $imageFilename));
            }
            $file = $request->file('cover_image');
            $imageFilename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/events'), $imageFilename);
        }

        DB::table('event')->where('Event_ID', $event_id)->update([
            'Event_Name'      => $validated['event_name'],
            'Event_Date'      => $validated['event_date'],
            'End_Date'        => $validated['end_date'] ?? null,
            'Location'        => $validated['location'],
            'Available_Slots' => $validated['available_slots'],
            'Summary'         => $validated['summary'] ?? null,
            'Cover_Image'     => $imageFilename,
        ]);

        return redirect()->route('admin.dashboard', ['tab' => 'events'])
            ->with('status', 'Event updated successfully.');
    }

    public function destroy($event_id)
    {
        $event = DB::table('event')->where('Event_ID', $event_id)->first();
        if ($event) {
            if ($event->Cover_Image && File::exists(public_path('uploads/events/' . $event->Cover_Image))) {
                File::delete(public_path('uploads/events/' . $event->Cover_Image));
            }
            DB::table('event')->where('Event_ID', $event_id)->delete();
        }

        return redirect()->route('admin.dashboard', ['tab' => 'events'])
            ->with('status', 'Event deleted successfully.');
    }
}