<?php

namespace App\Http\Controllers;

use App\Services\CloudinaryImageStorage;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class EventController extends Controller
{
    public function __construct(private CloudinaryImageStorage $imageStorage) {}

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

        $imageUrl = $request->hasFile('cover_image')
            ? $this->imageStorage->store($request->file('cover_image'))
            : null;

        $adminUserId = session('admin_user_id');
        $createdBy = $adminUserId && DB::table('system_user')->where('User_ID', $adminUserId)->exists()
            ? $adminUserId
            : null;

        $payload = [
            'Event_Name'      => $validated['event_name'],
            'Event_Date'      => $validated['event_date'],
            'End_Date'        => $validated['end_date'] ?? null,
            'Location'        => $validated['location'],
            'Available_Slots' => $validated['available_slots'],
            'Cover_Image'     => $imageUrl,
            'Created_By'      => $createdBy,
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

        $imageUrl = $event->Cover_Image;

        if ($request->hasFile('cover_image')) {
            $imageUrl = $this->imageStorage->store($request->file('cover_image'));
        }

        $payload = [
            'Event_Name'      => $validated['event_name'],
            'Event_Date'      => $validated['event_date'],
            'End_Date'        => $validated['end_date'] ?? null,
            'Location'        => $validated['location'],
            'Available_Slots' => $validated['available_slots'],
            'Cover_Image'     => $imageUrl,
        ];

        // Safely map Description or Summary based on actual database column
        if (Schema::hasColumn('event', 'Description')) {
            $payload['Description'] = $validated['summary'] ?? null;
        } elseif (Schema::hasColumn('event', 'Summary')) {
            $payload['Summary'] = $validated['summary'] ?? null;
        }

        DB::table('event')->where('Event_ID', $event_id)->update($payload);

        if ($request->hasFile('cover_image') && $event->Cover_Image !== $imageUrl) {
            $this->removeImage($event->Cover_Image, $event_id, 'replaced');
        }

        return redirect()->route('admin.dashboard', ['tab' => 'events'])
            ->with('status', 'Event updated successfully.');
    }

    public function destroy($event_id)
    {
        $event = DB::table('event')->where('Event_ID', $event_id)->first();
        if ($event) {
            DB::table('event')->where('Event_ID', $event_id)->delete();
            $this->removeImage($event->Cover_Image, $event_id, 'deleted');
        }

        return redirect()->route('admin.dashboard', ['tab' => 'events'])
            ->with('status', 'Event deleted successfully.');
    }

    private function removeImage(?string $image, int|string $eventId, string $action): void
    {
        if (! $image) {
            return;
        }

        if (str_starts_with($image, 'https://res.cloudinary.com/')) {
            try {
                $this->imageStorage->delete($image);
            } catch (RequestException|\RuntimeException $exception) {
                Log::warning("Unable to remove {$action} event image from Cloudinary.", [
                    'event_id' => $eventId,
                    'exception' => $exception->getMessage(),
                ]);
            }

            return;
        }

        if (basename($image) !== $image) {
            Log::warning("Unable to remove {$action} legacy event image with an invalid filename.", [
                'event_id' => $eventId,
            ]);

            return;
        }

        $path = public_path('uploads/events/'.$image);
        if (File::exists($path) && ! File::delete($path)) {
            Log::warning("Unable to remove {$action} legacy event image from local storage.", [
                'event_id' => $eventId,
                'path' => $path,
            ]);
        }
    }
}