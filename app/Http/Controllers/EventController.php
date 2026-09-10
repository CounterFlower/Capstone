<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class EventController extends Controller
{
    public function store(Request $request)
    {
        // 1. Normalize field aliases if submitted from alternate form layouts
        if (! $request->filled('event_name') && $request->filled('title')) {
            $request->merge(['event_name' => $request->input('title')]);
        }

        if (! $request->filled('location') && $request->filled('venue')) {
            $request->merge(['location' => $request->input('venue')]);
        }

        if (! $request->filled('event_date') && $request->filled('date')) {
            $time = $request->filled('time') ? $request->input('time') : '00:00';
            $request->merge(['event_date' => $request->input('date') . ' ' . $time]);
        }

        if (! $request->filled('end_date') && $request->filled('end_date')) {
            $endTime = $request->filled('end_time') ? $request->input('end_time') : '23:59';
            $request->merge(['end_date' => $request->input('end_date') . ' ' . $endTime]);
        }

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
            $destination = public_path('uploads/events');
            if (! File::isDirectory($destination)) {
                File::makeDirectory($destination, 0755, true, true);
            }

            $file = $request->file('cover_image');
            $imageFilename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($destination, $imageFilename);
        }

        $payload = [
            'Event_Name'      => $validated['event_name'],
            'Event_Date'      => $validated['event_date'],
            'End_Date'        => $validated['end_date'] ?? null,
            'Location'        => $validated['location'],
            'Available_Slots' => $validated['available_slots'],
            'Cover_Image'     => $imageFilename,
            'Created_By'      => session('admin_user_id') ?? 1,
        ];

        // Safely map Description or Summary based on actual database column
        if (Schema::hasColumn('event', 'Description')) {
            $payload['Description'] = $validated['summary'] ?? null;
        } elseif (Schema::hasColumn('event', 'Summary')) {
            $payload['Summary'] = $validated['summary'] ?? null;
        }

        DB::table('event')->insert($payload);

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
        // 1. Normalize field aliases
        if (! $request->filled('event_name') && $request->filled('title')) {
            $request->merge(['event_name' => $request->input('title')]);
        }

        if (! $request->filled('location') && $request->filled('venue')) {
            $request->merge(['location' => $request->input('venue')]);
        }

        if (! $request->filled('event_date') && $request->filled('date')) {
            $time = $request->filled('time') ? $request->input('time') : '00:00';
            $request->merge(['event_date' => $request->input('date') . ' ' . $time]);
        }

        if (! $request->filled('end_date') && $request->filled('end_date')) {
            $endTime = $request->filled('end_time') ? $request->input('end_time') : '23:59';
            $request->merge(['end_date' => $request->input('end_date') . ' ' . $endTime]);
        }

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
            $destination = public_path('uploads/events');
            if (! File::isDirectory($destination)) {
                File::makeDirectory($destination, 0755, true, true);
            }

            // Remove old uploaded photo from disk if present
            if ($imageFilename && File::exists($destination . '/' . $imageFilename)) {
                File::delete($destination . '/' . $imageFilename);
            }

            $file = $request->file('cover_image');
            $imageFilename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($destination, $imageFilename);
        }

        $payload = [
            'Event_Name'      => $validated['event_name'],
            'Event_Date'      => $validated['event_date'],
            'End_Date'        => $validated['end_date'] ?? null,
            'Location'        => $validated['location'],
            'Available_Slots' => $validated['available_slots'],
            'Cover_Image'     => $imageFilename,
        ];

        // Safely map Description or Summary based on actual database column
        if (Schema::hasColumn('event', 'Description')) {
            $payload['Description'] = $validated['summary'] ?? null;
        } elseif (Schema::hasColumn('event', 'Summary')) {
            $payload['Summary'] = $validated['summary'] ?? null;
        }

        DB::table('event')->where('Event_ID', $event_id)->update($payload);

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