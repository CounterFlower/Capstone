@extends('layouts.dashboard')

@section('content')
    <!-- Map Assets -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
    <script src="{{ asset('js/barangay-map-data.js') }}"></script>

    <header class="topbar">
        <div>
            <p class="eyebrow">2.1.5 Incident Reporting and Case Monitoring</p>
            <h1>Incident Reporting and Case Monitoring</h1>
            <p>Layout for report intake, case categorization, assigned officer tracking, and resolution timeline monitoring.</p>
        </div>
        <div class="status-pill">Case tracker</div>
    </header>

    <section class="stats">
        <article class="stat-card"><span>New Reports</span><strong>{{ sprintf('%02d', $stats['new'] ?? 5) }}</strong></article>
        <article class="stat-card"><span>Under Review</span><strong>{{ sprintf('%02d', $stats['under_review'] ?? 8) }}</strong></article>
        <article class="stat-card"><span>Escalated Cases</span><strong>{{ sprintf('%02d', $stats['escalated'] ?? 3) }}</strong></article>
        <article class="stat-card"><span>Resolved This Month</span><strong>{{ sprintf('%02d', $stats['resolved'] ?? 19) }}</strong></article>
    </section>

    <!-- Incident Geographic Distribution Section -->
    <section class="card" style="margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
            <div>
                <h3 style="margin: 0;">Geographic Incident Distribution</h3>
                <small style="color: #64748b;">Active incident markers across Barangay Bagumbayan puroks</small>
            </div>
            <!-- Status Legend Indicator -->
            <div style="display: flex; gap: 0.75rem; font-size: 0.75rem; flex-wrap: wrap;">
                <span style="display: inline-flex; align-items: center;"><span style="width: 8px; height: 8px; border-radius: 50%; background: #eab308; margin-right: 4px;"></span> Pending / New</span>
                <span style="display: inline-flex; align-items: center;"><span style="width: 8px; height: 8px; border-radius: 50%; background: #3b82f6; margin-right: 4px;"></span> Under Review</span>
                <span style="display: inline-flex; align-items: center;"><span style="width: 8px; height: 8px; border-radius: 50%; background: #dc2626; margin-right: 4px;"></span> Escalated</span>
                <span style="display: inline-flex; align-items: center;"><span style="width: 8px; height: 8px; border-radius: 50%; background: #22c55e; margin-right: 4px;"></span> Resolved</span>
            </div>
        </div>

        <div id="admin-incident-map" style="width: 100%; height: 380px; border-radius: 6px; border: 1px solid #e2e8f0; z-index: 0;"></div>
    </section>

    <!-- Case Board Table with Read-Only Preview & Review Action -->
    <article class="card" style="margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin: 0;">Case Board</h3>
            <span style="font-size: 0.75rem; color: #64748b;">Status changes require formal case review</span>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; text-align: left;">
                <thead>
                    <tr style="border-bottom: 2px solid #e2e8f0; color: #475569;">
                        <th style="padding: 10px 8px;">Case ID</th>
                        <th style="padding: 10px 8px;">Type</th>
                        <th style="padding: 10px 8px;">Reporter</th>
                        <th style="padding: 10px 8px;">Complaint Details</th>
                        <th style="padding: 10px 8px;">Date Filed</th>
                        <th style="padding: 10px 8px;">Status</th>
                        <th style="padding: 10px 8px; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($incidents ?? [] as $incident)
                        @php
                            $status = $incident->status ?? 'New';
                            $statusBg = '#f1f5f9';
                            $statusColor = '#475569';
                            if (in_array($status, ['New', 'Pending'])) {
                                $statusBg = '#fef9c3'; $statusColor = '#854d0e';
                            } elseif (in_array($status, ['Under Review', 'Under Investigation'])) {
                                $statusBg = '#dbeafe'; $statusColor = '#1e40af';
                            } elseif ($status === 'Escalated') {
                                $statusBg = '#fee2e2'; $statusColor = '#991b1b';
                            } elseif ($status === 'Resolved') {
                                $statusBg = '#dcfce7'; $statusColor = '#166534';
                            }
                        @endphp
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 12px 8px; font-weight: 700; color: #0f172a; white-space: nowrap;">
                                #{{ $incident->case_no ?? ('IR-' . str_pad($incident->id, 3, '0', STR_PAD_LEFT)) }}
                            </td>
                            <td style="padding: 12px 8px; color: #1e293b; white-space: nowrap;">
                                {{ $incident->category->Category ?? $incident->incident_type ?? 'Complaint' }}
                            </td>
                            <td style="padding: 12px 8px; color: #334155; white-space: nowrap;">
                                {{ $incident->complainant_name ?? 'Anonymous' }}
                            </td>
                            <td style="padding: 12px 8px; color: #64748b; max-width: 280px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $incident->complaint_details }}">
                                {{ \Illuminate\Support\Str::limit($incident->complaint_details ?? 'No details provided', 60) }}
                            </td>
                            <td style="padding: 12px 8px; color: #64748b; white-space: nowrap;">
                                {{ $incident->created_at ? $incident->created_at->format('M d, Y') : 'N/A' }}
                            </td>
                            <td style="padding: 12px 8px; white-space: nowrap;">
                                <!-- Read-Only Status Badge (Cannot change status here) -->
                                <span style="display: inline-block; padding: 3px 8px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: {{ $statusBg }}; color: {{ $statusColor }};">
                                    {{ $status }}
                                </span>
                            </td>
                            <td style="padding: 12px 8px; text-align: center; white-space: nowrap;">
                                <a href="{{ route('admin.incidents.review', $incident->id) }}" style="display: inline-block; padding: 6px 12px; background: #2563eb; color: #ffffff; text-decoration: none; border-radius: 6px; font-size: 0.78rem; font-weight: 600;">
                                    Review Case
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding: 24px 8px; text-align: center; color: #94a3b8;">
                                No incident records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </article>

    <section class="split">
        <article class="card">
            <h3>Monitoring Priorities</h3>
            <div class="list">
                <div class="list-item"><strong>Intake quality</strong> Capture complete incident type, date, location, and involved residents.</div>
                <div class="list-item"><strong>Status tracking</strong> Monitor whether a case is new, under review, in mediation, escalated, or resolved.</div>
                <div class="list-item"><strong>Case history</strong> Preserve chronological actions and outcomes for future reference.</div>
            </div>
        </article>

        <article class="card">
            <h3>Procedural Integrity</h3>
            <div class="list">
                <div class="list-item"><strong>Status Locking</strong> Direct status editing is restricted to the comprehensive review view to maintain case accountability.</div>
                <div class="list-item"><strong>Geographic Audit</strong> Verify incident pins against reported purok jurisdictions before escalating.</div>
            </div>
        </article>
    </section>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const geo = window.BagumbayanGeoData;
        const adminMap = L.map('admin-incident-map').fitBounds(geo.bounds, { padding: [25, 25] });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(adminMap);

        // Purok boundary overlays
        geo.puroks.forEach(function (purok) {
            L.polygon(purok.coords, {
                color: purok.color,
                weight: 1.5,
                fillOpacity: 0.12,
                dashArray: '3, 4'
            }).bindTooltip(purok.name, { sticky: true }).addTo(adminMap);
        });

        // Key landmarks
        geo.landmarks.forEach(function (landmark) {
            L.circleMarker(landmark.coords, {
                radius: 4,
                fillColor: "#0f172a",
                color: "#ffffff",
                weight: 1.5,
                fillOpacity: 0.9
            }).bindTooltip(landmark.name, { direction: 'top' }).addTo(adminMap);
        });

        const clusterGroup = L.markerClusterGroup({
            maxClusterRadius: 35,
            spiderfyOnMaxZoom: true
        });

        const incidentRecords = @json($incidents ?? []);

        const statusColors = {
            'New': '#eab308',
            'Pending': '#eab308',
            'Under Review': '#3b82f6',
            'Under Investigation': '#3b82f6',
            'Escalated': '#dc2626',
            'Resolved': '#22c55e',
            'Rejected': '#6b7280'
        };

        if (Array.isArray(incidentRecords)) {
            incidentRecords.forEach(function (incident) {
                const lat = parseFloat(incident.latitude);
                const lng = parseFloat(incident.longitude);

                if (isNaN(lat) || isNaN(lng) || lat < -90 || lat > 90 || lng < -180 || lng > 180) {
                    return;
                }

                const rawStatus = incident.status || 'New';
                const markerColor = statusColors[rawStatus] || '#64748b';

                const marker = L.circleMarker([lat, lng], {
                    radius: 7,
                    fillColor: markerColor,
                    color: '#ffffff',
                    weight: 2,
                    fillOpacity: 0.95
                });

                const safeCaseNo = incident.case_no || ('IR-' + (incident.id ? incident.id.toString().padStart(3, '0') : '000'));
                const safeType = (incident.category && incident.category.Category) ? incident.category.Category : (incident.incident_type || 'Incident Report');
                const safeDate = incident.created_at ? new Date(incident.created_at).toLocaleDateString() : 'N/A';
                const reviewUrl = "{{ url('admin/incidents') }}/" + incident.id + "/review";

                const popupHtml = `
                    <div style="font-family: inherit; font-size: 12px; line-height: 1.4; min-width: 160px;">
                        <div style="font-weight: 700; color: #1e293b; margin-bottom: 2px;">#${safeCaseNo}</div>
                        <div style="color: #334155; margin-bottom: 4px;">${safeType}</div>
                        <div style="margin-bottom: 4px;">Status: <span style="color: ${markerColor}; font-weight: 600;">${rawStatus}</span></div>
                        <div style="color: #64748b; font-size: 11px; margin-bottom: 8px;">Date: ${safeDate}</div>
                        <a href="${reviewUrl}" style="display: block; text-align: center; background: #2563eb; color: #fff; text-decoration: none; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                            Review Case &rarr;
                        </a>
                    </div>
                `;

                marker.bindPopup(popupHtml);
                clusterGroup.addLayer(marker);
            });
        }

        adminMap.addLayer(clusterGroup);

        setTimeout(function () {
            adminMap.invalidateSize();
        }, 200);
    });
    </script>
@endsection