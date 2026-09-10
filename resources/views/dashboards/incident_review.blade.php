@extends('layouts.dashboard')

@section('content')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="{{ asset('js/barangay-map-data.js') }}"></script>

    @php
        $caseId = $incident->Incident_ID ?? $incident->id;
        $status = $incident->Resolution_Status ?? 'Pending';
        $lat = $incident->Latitude ?? null;
        $lng = $incident->Longitude ?? null;
        
        // Read directly from the aliased query columns
        $complainant = !empty(trim($incident->complainant_name ?? '')) 
            ? $incident->complainant_name 
            : (!empty(trim($incident->guest_name ?? '')) ? $incident->guest_name : 'Unspecified');

        $respondent = !empty(trim($incident->respondent_name ?? '')) 
            ? $incident->respondent_name 
            : 'Unspecified';

        $categoryName = $incident->category_name ?? 'Uncategorized';
        $dateFiled = !empty($incident->Date_Filed) 
            ? \Carbon\Carbon::parse($incident->Date_Filed)->format('M d, Y h:i A') 
            : (!empty($incident->Date_Reported) ? \Carbon\Carbon::parse($incident->Date_Reported)->format('M d, Y h:i A') : 'N/A');
    @endphp

    <header class="topbar">
        <div>
            <p class="eyebrow">Case Investigation & Action</p>
            <h1>Review Incident Case #{{ $caseId }}</h1>
            <p>Filed: {{ $dateFiled }} &bull; Complainant: <strong>{{ $complainant }}</strong></p>
        </div>
        <div>
            <a href="{{ route('admin.dashboard', ['tab' => 'cases']) }}" class="status-pill" style="text-decoration: none; display: inline-block;">&larr; Back to Incident Board</a>
        </div>
    </header>

    @if (session('status'))
        <div style="padding: 12px 16px; background: #ecfdf5; border-left: 4px solid #10b981; color: #065f46; border-radius: 6px; margin-bottom: 20px;">
            {{ session('status') }}
        </div>
    @endif

    <section class="split" style="grid-template-columns: 1.2fr 0.8fr; gap: 20px; align-items: start;">
        <article class="card">
            <h3>Incident Particulars</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 18px; border-bottom: 1px solid #f1f5f9; padding-bottom: 16px;">
                <div>
                    <label style="font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: bold;">Category</label>
                    <div style="font-size: 0.95rem; font-weight: 600; color: #1e293b; margin-top: 2px;">
                        {{ $categoryName }}
                    </div>
                </div>
                <div>
                    <label style="font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: bold;">Current Status</label>
                    <div style="font-size: 0.95rem; font-weight: 600; color: #1e293b; margin-top: 2px;">
                        {{ $status }}
                    </div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 18px; border-bottom: 1px solid #f1f5f9; padding-bottom: 16px;">
                <div>
                    <label style="font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: bold;">Complainant</label>
                    <div style="font-size: 0.95rem; color: #1e293b; margin-top: 2px;">
                        {{ $complainant }}
                    </div>
                </div>
                <div>
                    <label style="font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: bold;">Respondent</label>
                    <div style="font-size: 0.95rem; color: #1e293b; margin-top: 2px;">
                        {{ $respondent }}
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 18px;">
                <label style="font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: bold;">Complaint Description</label>
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; margin-top: 6px; font-size: 0.9rem; line-height: 1.6; color: #334155; white-space: pre-wrap;">{{ $incident->Description ?? 'No details provided.' }}</div>
            </div>

            <div>
                <label style="font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: bold;">Requested Relief</label>
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; margin-top: 6px; font-size: 0.9rem; line-height: 1.6; color: #334155; white-space: pre-wrap;">{{ $incident->Requested_Relief ?? 'No relief specified.' }}</div>
            </div>
        </article>

        <div style="display: grid; gap: 20px;">
            <!-- Status Update Action Form -->
            <article class="card">
                <h3>Update Resolution Status</h3>
                <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 14px;">
                    Modifying the status updates the blotter record.
                </p>

                <form method="POST" action="{{ route('admin.incidents.update-status', $caseId) }}">
                    @csrf
                    @method('PATCH')

                    <div style="margin-bottom: 16px;">
                        <label for="status" style="display: block; font-size: 0.8rem; font-weight: 600; color: #374151; margin-bottom: 6px;">Status</label>
                        <select name="status" id="status" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px; font-size: 0.9rem;" required>
                            @foreach (['Pending', 'Active', 'Resolved', 'Escalated'] as $stat)
                                <option value="{{ $stat }}" {{ $status === $stat ? 'selected' : '' }}>{{ $stat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" style="width: 100%; background: #2563eb; color: #ffffff; border: 0; padding: 10px 14px; border-radius: 8px; font-weight: 600; font-size: 0.9rem; cursor: pointer;">
                        Save Resolution Status
                    </button>
                </form>
            </article>

            <!-- Geographic Pinpoint Display -->
            <article class="card">
                <h3>Incident Location</h3>
                <div style="font-size: 0.8rem; color: #64748b; margin-bottom: 10px;">
                    Coordinates: 
                    <strong style="font-family: monospace; color: #1e293b;">
                        {{ ($lat && $lng) ? number_format((float)$lat, 6) . ', ' . number_format((float)$lng, 6) : 'No coordinates recorded' }}
                    </strong>
                </div>

                <div id="review-incident-map" style="width: 100%; height: 260px; border-radius: 8px; border: 1px solid #e2e8f0; z-index: 0;"></div>
                
                @if (empty($lat) || empty($lng))
                    <div style="margin-top: 8px; font-size: 0.75rem; color: #b45309; background: #fffbeb; padding: 6px 10px; border-radius: 6px; border: 1px solid #fef3c7;">
                        Notice: This report was logged without geographic coordinates.
                    </div>
                @endif
            </article>
        </div>
    </section>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const geo = window.BagumbayanGeoData;
        
        // Pass PHP values safely as JSON to avoid NaN/empty string parsing issues
        const rawLat = @json($lat ? (float)$lat : null);
        const rawLng = @json($lng ? (float)$lng : null);
        const hasCoords = rawLat !== null && rawLng !== null && !isNaN(rawLat) && !isNaN(rawLng);

        const defaultCenter = (geo && geo.center) ? geo.center : [13.142648, 123.716538];
        const initialPoint = hasCoords ? [rawLat, rawLng] : defaultCenter;
        
        const map = L.map('review-incident-map').setView(initialPoint, hasCoords ? 17 : 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Display Purok overlays for spatial reference
        if (geo && geo.puroks) {
            geo.puroks.forEach(function (purok) {
                L.polygon(purok.coords, {
                    color: purok.color,
                    weight: 1.5,
                    fillOpacity: 0.1,
                    dashArray: '3, 4'
                }).bindTooltip(purok.name, { sticky: true }).addTo(map);
            });
        }

        // Place marker only if coordinates exist
        if (hasCoords) {
            L.marker([rawLat, rawLng])
                .addTo(map)
                .bindPopup("<strong>Reported Location</strong><br>" + rawLat.toFixed(6) + ", " + rawLng.toFixed(6))
                .openPopup();
        }

        setTimeout(function () { 
            map.invalidateSize(); 
        }, 200);
    });
    </script>
@endsection