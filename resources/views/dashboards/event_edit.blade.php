@extends('layouts.dashboard')

@section('content')
<header class="topbar">
    <div>
        <p class="eyebrow">Event Management</p>
        <h1>Edit Event: {{ $event->Event_Name }}</h1>
    </div>
    <div>
        <a href="{{ route('admin.dashboard', ['tab' => 'events']) }}" class="status-pill" style="text-decoration: none;">&larr; Back to Dashboard</a>
    </div>
</header>

<div class="card" style="max-width: 680px; margin: 0 auto;">
    @if ($errors->any())
        <div style="padding: 12px 16px; background: #fef2f2; border-left: 4px solid #ef4444; color: #991b1b; border-radius: 8px; margin-bottom: 16px; font-size: 0.88rem;">
            <strong>Please correct the following:</strong>
            <ul style="margin: 6px 0 0 16px; padding: 0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.events.update', $event->Event_ID) }}" enctype="multipart/form-data" style="display: grid; gap: 16px;">
        @csrf
        @method('PUT')

        <div>
            <label style="display: block; font-weight: 700; font-size: 0.85rem; margin-bottom: 4px;">Event Title *</label>
            <input type="text" name="event_name" value="{{ old('event_name', $event->Event_Name) }}" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px;">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
            <div>
                <label style="display: block; font-weight: 700; font-size: 0.85rem; margin-bottom: 4px;">Start Time *</label>
                <input type="datetime-local" name="event_date" value="{{ old('event_date', $event->Event_Date ? date('Y-m-d\TH:i', strtotime($event->Event_Date)) : '') }}" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px;">
            </div>
            <div>
                <label style="display: block; font-weight: 700; font-size: 0.85rem; margin-bottom: 4px;">End Time</label>
                <input type="datetime-local" name="end_date" value="{{ old('end_date', $event->End_Date ? date('Y-m-d\TH:i', strtotime($event->End_Date)) : '') }}" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px;">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 12px;">
            <div>
                <label style="display: block; font-weight: 700; font-size: 0.85rem; margin-bottom: 4px;">Venue / Location *</label>
                <input type="text" name="location" value="{{ old('location', $event->Location) }}" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px;">
            </div>
            <div>
                <label style="display: block; font-weight: 700; font-size: 0.85rem; margin-bottom: 4px;">Capacity (Slots) *</label>
                <input type="number" name="available_slots" value="{{ old('available_slots', $event->Available_Slots) }}" min="1" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px;">
            </div>
        </div>

        <div>
            <label style="display: block; font-weight: 700; font-size: 0.85rem; margin-bottom: 4px;">Cover Image</label>
            @if ($event->Cover_Image)
                <div style="margin-bottom: 8px;">
                    <img src="{{ route('public.photos', ['filename' => $event->Cover_Image]) }}" alt="Current Cover" style="max-height: 120px; border-radius: 8px; border: 1px solid #cbd5e1;">
                </div>
            @endif
            <input type="file" name="cover_image" accept="image/*" style="width: 100%; border: 1px dashed #94a3b8; border-radius: 8px; padding: 10px; background: #f8fafc;">
            <small style="color: #64748b; font-size: 0.75rem;">Leave empty to keep the existing cover photo.</small>
        </div>

        <div>
            <label style="display: block; font-weight: 700; font-size: 0.85rem; margin-bottom: 4px;">Summary / Description</label>
            <textarea name="summary" rows="4" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px;">{{ old('summary', $event->Description ?? $event->description ?? $event->Summary ?? '') }}</textarea>
        </div>

        <button type="submit" style="background: #2563eb; color: #ffffff; border: 0; padding: 12px; border-radius: 8px; font-weight: 700; cursor: pointer;">
            Save Changes
        </button>
    </form>
</div>
@endsection