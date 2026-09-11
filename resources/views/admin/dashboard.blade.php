@php
    use Carbon\Carbon;
@endphp

@extends('layouts.admin')

@section('content')
    @php
        $analyticsBlocks = [
            ['label' => 'Document requests completed', 'value' => '78%', 'width' => '78%'],
            ['label' => 'Resident profiles completed', 'value' => '92%', 'width' => '92%'],
            ['label' => 'Cases resolved this month', 'value' => '63%', 'width' => '63%'],
            ['label' => 'Event participation fill rate', 'value' => count($registrations) > 0 ? 'Live' : '0%', 'width' => min(count($registrations) * 12, 100) . '%'],
        ];

        $badgeMap = [
            'Pending' => 'warn',
            'Active' => 'good',
            'Resolved' => 'good',
            'Escalated' => 'alert',
        ];
    @endphp

    <header class="topbar">
        <div>
            <p class="eyebrow">Administrative Dashboard</p>
            <h1>Barangay Management Control Panel</h1>
            <p>Prototype admin view for case monitoring, resident profile records, document request monitoring, event registrations, and analytics reporting.</p>
        </div>
        <div class="pill">Logged in as admin</div>
    </header>

    <section class="tabs">
        <button type="button" class="tab" data-tab-target="overview">Overview<small>Operational metrics and summary</small></button>
        <button type="button" class="tab" data-tab-target="cases">Case Monitoring<small>Incident reports and status flow</small></button>
        <button type="button" class="tab" data-tab-target="residents">Resident Profiles<small>Sample records table</small></button>
        <button type="button" class="tab" data-tab-target="requests">Document Requests<small>Service request monitoring</small></button>
        <button type="button" class="tab" data-tab-target="events">Event Sign-Ups<small>View resident activity registrations</small></button>
        <button type="button" class="tab" data-tab-target="analytics">Analytics<small>Barangay statistics and reporting</small></button>
    </section>

    <!-- OVERVIEW TAB -->
<section class="tab-panel {{ ($activeTab ?? 'overview') === 'overview' ? 'active' : '' }}" data-tab-panel="overview">
    <!-- Top Live Stats Counters -->
    <section class="stats" id="overview">
        <article class="stat">
            <span>Total Residents</span>
            <strong>{{ number_format(count($residentProfiles ?? [])) }}</strong>
        </article>
        <article class="stat">
            <span>Pending Document Requests</span>
            <strong>{{ number_format(collect($pendingDocumentRequests ?? [])->where('Status', 'Pending')->count()) }}</strong>
        </article>
        <article class="stat">
            <span>Open Incident Cases</span>
            <strong>{{ number_format(max(0, ($totalCases ?? 0) - ($resolvedCases ?? 0))) }}</strong>
        </article>
        <article class="stat">
            <span>Event Sign-Ups</span>
            <strong>{{ number_format(count($registrations ?? [])) }}</strong>
        </article>
    </section>

    <!-- Side-by-Side Accomplishment Line Charts (0-100%) -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(420px, 1fr)); gap: 20px; margin-bottom: 24px;">
        
        <!-- Document Requests Accomplished Trend -->
        <article class="card" style="padding: 22px; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0;">
            <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 16px;">
                <div>
                    <h3 style="margin: 0; font-size: 1.15rem; color: #1e293b;">Document Requests Accomplished</h3>
                    <small style="color: #64748b;">Chronological issuance rate</small>
                </div>
                <div style="text-align: right;">
                    <span style="font-size: 1.6rem; font-weight: 800; color: #10b981;">{{ $docRate ?? 0 }}%</span>
                    <div style="font-size: 0.75rem; color: #64748b; font-weight: 600;">
                        {{ $releasedDocRequests ?? 0 }} of {{ $totalDocRequests ?? 0 }} Released
                    </div>
                </div>
            </div>
            <div style="position: relative; height: 260px; width: 100%;">
                <canvas id="documentLineChart"></canvas>
            </div>
        </article>

        <!-- Incident Cases Solved Trend -->
        <article class="card" style="padding: 22px; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0;">
            <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 16px;">
                <div>
                    <h3 style="margin: 0; font-size: 1.15rem; color: #1e293b;">Incident Cases Solved</h3>
                    <small style="color: #64748b;">Chronological case resolution rate</small>
                </div>
                <div style="text-align: right;">
                    <span style="font-size: 1.6rem; font-weight: 800; color: #2563eb;">{{ $caseRate ?? 0 }}%</span>
                    <div style="font-size: 0.75rem; color: #64748b; font-weight: 600;">
                        {{ $resolvedCases ?? 0 }} of {{ $totalCases ?? 0 }} Settled
                    </div>
                </div>
            </div>
            <div style="position: relative; height: 260px; width: 100%;">
                <canvas id="caseLineChart"></canvas>
            </div>
        </article>
    </div>

    <!-- Administrative Snapshot & Notes -->
    <section class="grid">
        <article class="card">
            <h2>Administrative Snapshot</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Module</th>
                        <th>Current Load</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Incident Reporting</td>
                        <td>{{ max(0, ($totalCases ?? 0) - ($resolvedCases ?? 0)) }} active pending blotters</td>
                        <td>
                            <span class="badge {{ (($totalCases ?? 0) - ($resolvedCases ?? 0)) > 5 ? 'alert' : 'warn' }}">
                                {{ (($totalCases ?? 0) - ($resolvedCases ?? 0)) > 0 ? 'Active' : 'Settled' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>Document Requests</td>
                        <td>{{ $totalDocRequests ?? 0 }} requests logged ({{ $releasedDocRequests ?? 0 }} released)</td>
                        <td>
                            <span class="badge {{ ($docRate ?? 0) >= 70 ? 'good' : 'warn' }}">
                                {{ ($docRate ?? 0) >= 70 ? 'Optimal' : 'Review' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>Resident Records</td>
                        <td>{{ number_format(count($residentProfiles ?? [])) }} profiles on file</td>
                        <td><span class="badge good">Stable</span></td>
                    </tr>
                    <tr>
                        <td>Event Registrations</td>
                        <td>{{ count($registrations ?? []) }} current sign-ups</td>
                        <td><span class="badge good">Tracked</span></td>
                    </tr>
                </tbody>
            </table>
        </article>

        <article class="card">
            <h2>Quick Notes</h2>
            <div class="list">
                <div class="list-item">
                    <strong>Document Handling</strong>
                    Clearance and residency certificates are tracked dynamically per status update.
                </div>
                <div class="list-item">
                    <strong>Resolution Metric</strong>
                    Incidents count as settled once recorded as resolved, settled, or closed.
                </div>
                <div class="list-item">
                    <strong>Operational Goal</strong>
                    Maintain both document issuance and blotter settlements above 80%.
                </div>
            </div>
        </article>
    </section>
</section>

<!-- Chart.js CDN & Rendering -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const commonYAxis = {
        min: 0,
        max: 100,
        ticks: {
            stepSize: 20,
            callback: function(value) {
                return value + '%';
            }
        },
        grid: {
            color: '#f1f5f9'
        }
    };

    // 1. Document Request Accomplished Trend
    const docCtx = document.getElementById('documentLineChart');
    if (docCtx) {
        new Chart(docCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($docChartLabels ?? []) !!},
                datasets: [{
                    label: 'Accomplished (%)',
                    data: {!! json_encode($docChartRates ?? []) !!},
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.12)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#10b981'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Accomplished: ' + context.parsed.y + '%';
                            }
                        }
                    }
                },
                scales: {
                    y: commonYAxis,
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // 2. Incident Case Resolution Trend
    const caseCtx = document.getElementById('caseLineChart');
    if (caseCtx) {
        new Chart(caseCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($caseChartLabels ?? []) !!},
                datasets: [{
                    label: 'Resolved (%)',
                    data: {!! json_encode($caseChartRates ?? []) !!},
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.12)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#2563eb'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Resolved: ' + context.parsed.y + '%';
                            }
                        }
                    }
                },
                scales: {
                    y: commonYAxis,
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }
});
</script>
    <!-- CASES TAB -->
    <section class="tab-panel {{ ($activeTab ?? 'overview') === 'cases' ? 'active' : '' }}" data-tab-panel="cases">
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
        <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
        <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
        <script src="{{ asset('js/barangay-map-data.js') }}"></script>

        <article class="card" style="margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
                <div>
                    <h2 style="margin: 0;">Geographic Incident Distribution</h2>
                    <p class="subtext" style="margin: 4px 0 0 0;">Visualized incident pins across Barangay Bagumbayan puroks.</p>
                </div>
                <div style="display: flex; gap: 10px; font-size: 0.75rem; flex-wrap: wrap;">
                    <span style="display: inline-flex; align-items: center;"><span style="width: 8px; height: 8px; border-radius: 50%; background: #eab308; margin-right: 5px;"></span> Pending</span>
                    <span style="display: inline-flex; align-items: center;"><span style="width: 8px; height: 8px; border-radius: 50%; background: #3b82f6; margin-right: 5px;"></span> Active / Review</span>
                    <span style="display: inline-flex; align-items: center;"><span style="width: 8px; height: 8px; border-radius: 50%; background: #dc2626; margin-right: 5px;"></span> Escalated</span>
                    <span style="display: inline-flex; align-items: center;"><span style="width: 8px; height: 8px; border-radius: 50%; background: #22c55e; margin-right: 5px;"></span> Resolved</span>
                </div>
            </div>
            <div id="cases-incident-map" style="width: 100%; height: 360px; border-radius: 12px; border: 1px solid #d5d8de; z-index: 0;"></div>
        </article>

        <section class="panels" id="cases">
            <article class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <div>
                        <h2 style="margin: 0;">Case Monitoring</h2>
                        <p class="subtext">Live incident reports pulled from the barangay system. Status can only be modified inside the case review page.</p>
                    </div>
                </div>

                <table class="table">
                    <thead>
                        <tr>
                            <th>Case ID</th>
                            <th>Type</th>
                            <th>Reporter</th>
                            <th>Complaint Details</th>
                            <th>Date Filed</th>
                            <th>Status</th>
                            <th style="text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($caseRecords as $case)
                            <tr>
                                <td><strong>#{{ $case->Incident_ID }}</strong></td>
                                <td>{{ $case->Category ?? 'Uncategorized' }}</td>
                                <td>{{ $case->Reporter_Name ?? 'Unknown' }}</td>
                                <td>{{ Str::limit($case->Description ?? ($case->complaint_details ?? 'No description provided'), 80) }}</td>
                                <td>{{ $case->Date_Filed ? \Carbon\Carbon::parse($case->Date_Filed)->format('M d, Y') : ($case->created_at ? $case->created_at->format('M d, Y') : 'N/A') }}</td>
                                <td>
                                    <span class="badge {{ $badgeMap[$case->Resolution_Status ?? $case->status ?? 'Pending'] ?? 'warn' }}">
                                        {{ $case->Resolution_Status ?? $case->status ?? 'Pending' }}
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <a href="{{ route('admin.incidents.review', $case->Incident_ID) }}" class="button primary" style="display: inline-block; padding: 6px 14px; text-decoration: none; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">
                                        Review
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; color: #64748b; padding: 20px;">No incident cases found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </article>

            <article class="card">
                <h2>Case Workflow Summary</h2>
                <div class="list">
                    <div class="list-item"><strong>New cases today</strong> 5 reports entered through the public complaint form.</div>
                    <div class="list-item"><strong>Cases awaiting mediation</strong> 3 disputes are already scheduled for barangay hearing.</div>
                    <div class="list-item"><strong>Escalated cases</strong> 2 incidents need external coordination or further review.</div>
                    <div class="list-item"><strong>Average resolution time</strong> 4.2 days from filing to final disposition.</div>
                </div>
            </article>
        </section>

        <script>
        document.addEventListener("DOMContentLoaded", function () {
            const geo = window.BagumbayanGeoData;
            const defaultCenter = (geo && geo.center) ? geo.center : [13.142648, 123.716538];
            const defaultBounds = (geo && geo.bounds) ? geo.bounds : [[13.139103, 123.713817], [13.146194, 123.719259]];

            const map = L.map('cases-incident-map').fitBounds(defaultBounds, { padding: [20, 20] });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            if (geo && geo.puroks) {
                geo.puroks.forEach(function (purok) {
                    L.polygon(purok.coords, {
                        color: purok.color,
                        weight: 1.5,
                        fillOpacity: 0.12,
                        dashArray: '3, 4'
                    }).bindTooltip(purok.name, { sticky: true }).addTo(map);
                });
            }

            if (geo && geo.landmarks) {
                geo.landmarks.forEach(function (landmark) {
                    L.circleMarker(landmark.coords, {
                        radius: 4,
                        fillColor: "#0f172a",
                        color: "#ffffff",
                        weight: 1.5,
                        fillOpacity: 0.9
                    }).bindTooltip(landmark.name, { direction: 'top' }).addTo(map);
                });
            }

            const clusterGroup = L.markerClusterGroup({
                maxClusterRadius: 35,
                spiderfyOnMaxZoom: true
            });

            const cases = @json($caseRecords ?? []);

            const statusColorMap = {
                'Pending': '#eab308',
                'Active': '#3b82f6',
                'Under Review': '#3b82f6',
                'Under Investigation': '#3b82f6',
                'Escalated': '#dc2626',
                'Resolved': '#22c55e'
            };

            if (Array.isArray(cases)) {
                cases.forEach(function (c) {
                    const lat = parseFloat(c.latitude || c.Latitude);
                    const lng = parseFloat(c.longitude || c.Longitude);

                    if (isNaN(lat) || isNaN(lng) || lat < -90 || lat > 90 || lng < -180 || lng > 180) {
                        return;
                    }

                    const currentStatus = c.Resolution_Status || c.status || 'Pending';
                    const markerColor = statusColorMap[currentStatus] || '#64748b';

                    const marker = L.circleMarker([lat, lng], {
                        radius: 7,
                        fillColor: markerColor,
                        color: '#ffffff',
                        weight: 2,
                        fillOpacity: 0.95
                    });

                    const caseId = c.Incident_ID || c.id;
                    const caseCategory = c.Category || 'Complaint';
                    const reviewUrl = "{{ url('admin/incidents') }}/" + caseId + "/review";

                    const popupContent = `
                        <div style="font-size: 12px; line-height: 1.4; min-width: 140px;">
                            <strong style="color:#0f172a;">#${caseId}</strong> - ${caseCategory}<br>
                            Status: <strong style="color:${markerColor};">${currentStatus}</strong><br>
                            <div style="margin-top: 6px;">
                                <a href="${reviewUrl}" style="color: #2563eb; text-decoration: underline; font-weight: 600;">Open Review</a>
                            </div>
                        </div>
                    `;

                    marker.bindPopup(popupContent);
                    clusterGroup.addLayer(marker);
                });
            }

            map.addLayer(clusterGroup);

            const tabTrigger = document.querySelector('[data-tab-target="cases"]');
            if (tabTrigger) {
                tabTrigger.addEventListener('click', function () {
                    setTimeout(function () { map.invalidateSize(); }, 200);
                });
            }
            setTimeout(function () { map.invalidateSize(); }, 250);
        });
        </script>
    </section>

    <!-- RESIDENTS TAB -->
    <section class="tab-panel {{ ($activeTab ?? 'overview') === 'residents' ? 'active' : '' }}" data-tab-panel="residents">
        <section class="panels" id="residents">
            <article class="card resident-table-card">
                <h2>Resident Profile Table</h2>
                @if (session('status'))
                    <p class="badge good" style="margin-bottom: 16px;">{{ session('status') }}</p>
                @endif
                @if ($errors->has('resident') || $errors->has('household') || $errors->has('details'))
                    <p class="badge alert" style="margin-bottom: 16px;">{{ $errors->first('resident') ?: ($errors->first('household') ?: $errors->first('details')) }}</p>
                @endif
                <div class="table-wrap">
                    <table class="table resident-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Resident ID</th>
                                <th>Household ID</th>
                                <th>Purok</th>
                                <th>Date of Birth</th>
                                <th>Place of Birth</th>
                                <th>Gender</th>
                                <th>Civil Status</th>
                                <th>Contact</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($residentProfiles as $resident)
                                <tr>
                                    <td>{{ $resident->First_Name }} {{ $resident->Middle_Name ? $resident->Middle_Name.' ' : '' }}{{ $resident->Last_Name }}</td>
                                    <td>{{ $resident->Resident_ID }}</td>
                                    <td>{{ $resident->Household_Id ?? 'Pending' }}</td>
                                    <td>{{ $resident->Zone_Purok ?? 'Not assigned' }}</td>
                                    <td>{{ $resident->Date_of_Birth ?? 'Not provided' }}</td>
                                    <td>{{ $resident->Place_of_Birth ?? 'Not provided' }}</td>
                                    <td>{{ $resident->Gender ?? 'Not provided' }}</td>
                                    <td>{{ $resident->Civil_Status ?? 'Not provided' }}</td>
                                    <td>{{ $resident->Contact_Number ?? 'Not provided' }}</td>
                                    <td><span class="badge {{ $resident->Is_Verified ? 'good' : 'warn' }}">{{ $resident->Is_Verified ? 'Verified' : 'Pending' }}</span></td>
                                    <td class="resident-actions">
                                        <button type="button" class="form-submit resident-edit" data-resident-id="{{ $resident->Resident_ID }}" data-first-name="{{ $resident->First_Name }}" data-middle-name="{{ $resident->Middle_Name }}" data-last-name="{{ $resident->Last_Name }}" data-date-of-birth="{{ $resident->Date_of_Birth }}" data-place-of-birth="{{ $resident->Place_of_Birth }}" data-gender="{{ $resident->Gender }}" data-civil-status="{{ $resident->Civil_Status }}" data-contact-number="{{ $resident->Contact_Number }}" data-household-id="{{ $resident->Household_Id }}" data-house-number="{{ $resident->House_Number }}" data-zone-purok="{{ $resident->Zone_Purok }}">Edit</button>
                                        <form method="POST" action="{{ route('admin.residents.verify') }}">
                                            @csrf
                                            <input type="hidden" name="active_tab" value="residents">
                                            <input type="hidden" name="resident_id" value="{{ $resident->Resident_ID }}">
                                            <input type="hidden" name="is_verified" value="{{ $resident->Is_Verified ? 0 : 1 }}">
                                            <button type="submit" class="form-submit">{{ $resident->Is_Verified ? 'Set pending' : 'Verify' }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            @if ($residentProfiles->isEmpty())
                                <tr>
                                    <td colspan="11">No resident profiles found.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </article>

            <article class="card">
                <h2>Add Resident Profile</h2>
                <form class="admin-form" method="POST" action="{{ route('admin.residents.store') }}">
                    @csrf
                    <input type="hidden" name="active_tab" value="residents">
                    <span class="form-section-label">Personal details</span>
                    <div class="form-grid">
                        <label class="form-field">First name<input name="first_name" value="{{ old('first_name') }}" placeholder="e.g. Maria" required></label>
                        <label class="form-field">Middle name<input name="middle_name" value="{{ old('middle_name') }}" placeholder="Optional"></label>
                        <label class="form-field">Last name<input name="last_name" value="{{ old('last_name') }}" placeholder="e.g. Santos" required></label>
                        <label class="form-field">Date of birth<input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"></label>
                        <label class="form-field">Place of birth<input name="place_of_birth" value="{{ old('place_of_birth') }}" placeholder="e.g. Pasig City"></label>
                        <label class="form-field">Gender<input name="gender" value="{{ old('gender') }}" placeholder="e.g. Female"></label>
                        <label class="form-field">Civil status<input name="civil_status" value="{{ old('civil_status') }}" placeholder="e.g. Single"></label>
                        <label class="form-field">Contact number<input name="contact_number" value="{{ old('contact_number') }}" placeholder="e.g. 0917 555 0101"></label>
                    </div>
                    <span class="form-section-label">Household assignment</span>
                    <div class="form-grid">
                        <label class="form-field">House number<input name="house_number" value="{{ old('house_number') }}" placeholder="e.g. 14" required></label>
                        <label class="form-field">Purok<input name="zone_purok" value="{{ old('zone_purok') }}" placeholder="e.g. Purok 2" required></label>
                        <label class="form-field">Household ID <span style="font-weight: 400; color: var(--muted);">(optional)</span><input type="number" name="household_id" value="{{ old('household_id') }}" placeholder="Leave blank if unknown"></label>
                    </div>
                    @if ($errors->any() && ! $errors->has('resident') && ! $errors->has('household'))
                        <div class="badge alert">{{ $errors->first() }}</div>
                    @endif
                    <button type="submit" class="form-submit">Add resident</button>
                </form>
            </article>

            <article class="card">
                <h2>Update Resident Profile</h2>
                <p class="subtext" style="margin-bottom: 16px;">Click Edit in the table to load existing values, then update personal and household details in one form.</p>
                <form id="resident-details-form" class="admin-form" method="POST" action="{{ route('admin.residents.update') }}">
                    @csrf
                    <input type="hidden" name="active_tab" value="residents">
                    <span class="form-section-label">Personal details</span>
                    <div class="form-grid">
                        <label class="form-field">Resident ID<input id="edit-resident-id" type="number" name="resident_id" value="{{ old('resident_id') }}" placeholder="e.g. 24" readonly required></label>
                        <label class="form-field">First name<input id="edit-first-name" name="first_name" value="{{ old('first_name') }}" placeholder="e.g. Maria" required></label>
                        <label class="form-field">Middle name<input id="edit-middle-name" name="middle_name" value="{{ old('middle_name') }}" placeholder="Optional"></label>
                        <label class="form-field">Last name<input id="edit-last-name" name="last_name" value="{{ old('last_name') }}" placeholder="e.g. Santos" required></label>
                        <label class="form-field">Date of birth<input id="edit-date-of-birth" type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"></label>
                        <label class="form-field">Place of birth<input id="edit-place-of-birth" name="place_of_birth" value="{{ old('place_of_birth') }}" placeholder="e.g. Pasig City"></label>
                        <label class="form-field">Gender<input id="edit-gender" name="gender" value="{{ old('gender') }}" placeholder="e.g. Female"></label>
                        <label class="form-field">Civil status<input id="edit-civil-status" name="civil_status" value="{{ old('civil_status') }}" placeholder="e.g. Single"></label>
                        <label class="form-field">Contact number<input id="edit-contact-number" name="contact_number" value="{{ old('contact_number') }}" placeholder="e.g. 0917 555 0101"></label>
                    </div>
                    <span class="form-section-label">Household assignment</span>
                    <div class="form-grid">
                        <label class="form-field">House number<input id="edit-house-number" name="house_number" value="{{ old('house_number') }}" placeholder="e.g. 14" required></label>
                        <label class="form-field">Purok<input id="edit-zone-purok" name="zone_purok" value="{{ old('zone_purok') }}" placeholder="e.g. Purok 2" required></label>
                        <label class="form-field">Household ID <span style="font-weight: 400; color: var(--muted);">(optional)</span><input id="edit-household-id" type="number" name="household_id" value="{{ old('household_id') }}" placeholder="Leave blank if unknown"></label>
                    </div>
                    <button type="submit" class="form-submit">Save resident profile</button>
                </form>
            </article>

            <article class="card">
                <h2>Records Monitoring</h2>
                <div class="list">
                    <div class="list-item"><strong>Total households</strong> 342 grouped resident records.</div>
                    <div class="list-item"><strong>Incomplete records</strong> 27 profiles need updated contact or identification details.</div>
                    <div class="list-item"><strong>Priority groups</strong> 119 senior citizens and 87 solo parent records tagged for service support.</div>
                    <div class="list-item"><strong>Recent profile updates</strong> 18 resident records were edited within the last 7 days.</div>
                </div>
            </article>
        </section>
    </section>

    <!-- REQUESTS TAB -->
<section class="tab-panel {{ in_array(($activeTab ?? ''), ['requests', 'documents']) ? 'active' : '' }}" data-tab-panel="requests">
    @if (session('status'))
        <div style="padding: 12px 16px; background: #ecfdf5; border-left: 4px solid #10b981; color: #065f46; border-radius: 8px; margin-bottom: 20px;">
            {{ session('status') }}
        </div>
    @endif

    <article class="card" style="width: 100%;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div>
                <h2 style="margin: 0; font-size: 1.35rem;">Document Issuance Requests</h2>
                <p style="color: #64748b; font-size: 0.85rem; margin-top: 4px;">Monitor, review, and issue barangay clearances, indigency certificates, and permits.</p>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #e2e8f0; text-align: left; color: #475569;">
                        <th style="padding: 12px 10px;">Req ID</th>
                        <th style="padding: 12px 10px;">Resident Name</th>
                        <th style="padding: 12px 10px;">Birthday</th>
                        <th style="padding: 12px 10px;">Address</th>
                        <th style="padding: 12px 10px; text-align: center;">Years Stayed</th>
                        <th style="padding: 12px 10px;">Document Type</th>
                        <th style="padding: 12px 10px;">Purpose</th>
                        <th style="padding: 12px 10px;">Requested On</th>
                        <th style="padding: 12px 10px; text-align: center;">Status</th>
                        <th style="padding: 12px 10px; text-align: center;">Update Status</th>
                        <th style="padding: 12px 10px; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $docList = $pendingDocumentRequests ?? [];
                    @endphp

                    @forelse ($docList as $doc)
                        @php
                            $status = $doc->Status ?? 'Pending';
                            $statusStyles = [
                                'Pending'      => 'background: #fef3c7; color: #92400e; border: 1px solid #fde68a;',
                                'Released'     => 'background: #dcfce7; color: #166534; border: 1px solid #bbf7d0;',
                                'Not Approved' => 'background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;',
                            ];
                            $badgeStyle = $statusStyles[$status] ?? 'background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0;';
                            $reqId = $doc->Request_ID ?? $doc->id;
                            $reqDate = $doc->Date_Requested ?? $doc->Request_Date ?? $doc->created_at ?? null;
                            $birthday = $doc->birthday ?? $doc->Date_of_Birth ?? null;
                            $address = $doc->full_address ?? $doc->Address ?? 'Barangay Bagumbayan, Daraga, Albay';
                            $years = $doc->Years_Stayed ?? $doc->years_stayed ?? '—';
                        @endphp
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 12px 10px; font-weight: 700; color: #1e293b; white-space: nowrap;">
                                #DOC-{{ str_pad($reqId, 3, '0', STR_PAD_LEFT) }}
                            </td>
                            <td style="padding: 12px 10px; font-weight: 600; color: #0f172a;">
                                {{ $doc->resident_name ?? 'N/A' }}
                                @if(!empty($doc->contact ?? $doc->Contact_Number ?? null))
                                    <div style="font-size: 0.75rem; color: #64748b; font-weight: 400;">{{ $doc->contact ?? $doc->Contact_Number }}</div>
                                @endif
                            </td>
                            <td style="padding: 12px 10px; color: #475569; white-space: nowrap;">
                                {{ !empty($birthday) ? \Carbon\Carbon::parse($birthday)->format('M d, Y') : '—' }}
                            </td>
                            <td style="padding: 12px 10px; color: #475569; max-width: 180px;">
                                {{ $address }}
                            </td>
                            <td style="padding: 12px 10px; color: #475569; text-align: center; white-space: nowrap;">
                                {{ is_numeric($years) ? $years . ' ' . \Illuminate\Support\Str::plural('yr', $years) : $years }}
                            </td>
                            <td style="padding: 12px 10px; font-weight: 600; color: #334155; white-space: nowrap;">
                                {{ $doc->document_name ?? $doc->Document_Type ?? 'Barangay Document' }}
                            </td>
                            <td style="padding: 12px 10px; color: #475569; max-width: 180px;">
                                {{ $doc->Purpose ?? $doc->purpose ?? 'General Use' }}
                            </td>
                            <td style="padding: 12px 10px; color: #475569; white-space: nowrap;">
                                {{ !empty($reqDate) ? \Carbon\Carbon::parse($reqDate)->format('M d, Y h:i A') : '—' }}
                            </td>
                            <td style="padding: 12px 10px; text-align: center; white-space: nowrap;">
                                <span style="display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 0.75rem; font-weight: 700; {{ $badgeStyle }}">
                                    {{ $status }}
                                </span>
                            </td>
                            <td style="padding: 12px 10px; text-align: center; white-space: nowrap;">
                                <form action="{{ route('admin.documents.update-status') }}" method="POST" style="display: inline-flex; gap: 4px; align-items: center;">
                                    @csrf
                                    <input type="hidden" name="request_id" value="{{ $reqId }}">

                                    @if ($status !== 'Pending')
                                        <button type="submit" name="status" value="Pending" style="background: #eab308; color: #fff; border: 0; border-radius: 6px; padding: 4px 8px; font-size: 0.72rem; font-weight: 600; cursor: pointer;">
                                            Pending
                                        </button>
                                    @endif

                                    @if ($status !== 'Released')
                                        <button type="submit" name="status" value="Released" style="background: #16a34a; color: #fff; border: 0; border-radius: 6px; padding: 4px 8px; font-size: 0.72rem; font-weight: 600; cursor: pointer;">
                                            Release
                                        </button>
                                    @endif

                                    @if ($status !== 'Not Approved')
                                        <button type="submit" name="status" value="Not Approved" onclick="return confirm('Mark this request as Not Approved?');" style="background: #ef4444; color: #fff; border: 0; border-radius: 6px; padding: 4px 8px; font-size: 0.72rem; font-weight: 600; cursor: pointer;">
                                            Decline
                                        </button>
                                    @endif
                                </form>
                            </td>
                            <td style="padding: 12px 10px; text-align: center; white-space: nowrap;">
                                <a href="{{ route('admin.documents.print', $reqId) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 4px; padding: 5px 10px; background: #0284c7; color: #ffffff; text-decoration: none; border-radius: 6px; font-size: 0.75rem; font-weight: 600;">
                                    🖨️ Print
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" style="padding: 32px; text-align: center; color: #94a3b8; font-style: italic;">
                                No document requests submitted yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </article>
</section>

    <!-- EVENTS TAB -->
   <section class="tab-panel {{ in_array(($activeTab ?? 'overview'), ['events', 'events-attendees']) ? 'active' : '' }}" data-tab-panel="events">
    <!-- 1. UPPER PART: Public Activity Attendance / Enlisted Residents Log -->
    <article class="card" style="margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 16px;">
            <div>
                <p class="eyebrow" style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Public Activity Attendance</p>
                <h2 style="margin: 4px 0 0 0; font-size: 1.35rem;">Enlisted Residents / RSVPs</h2>
            </div>

            <!-- Filter by Event Dropdown -->
            <form method="GET" action="{{ route('admin.dashboard') }}" style="display: flex; gap: 8px; align-items: center;">
                <input type="hidden" name="tab" value="events">
                
                <label for="event_filter" style="font-weight: 600; font-size: 0.85rem; color: #475569;">Filter Event:</label>
                <select name="event_filter" id="event_filter" onchange="this.form.submit()" style="padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font: inherit; font-size: 0.85rem; background: #fff;">
                    <option value="">-- All Events ({{ $eventRegistrations->count() }}) --</option>
                    @foreach ($eventsList as $evt)
                        <option value="{{ $evt->Event_ID }}" {{ (string)$selectedEventFilter === (string)$evt->Event_ID ? 'selected' : '' }}>
                            {{ $evt->Event_Name }} ({{ \Carbon\Carbon::parse($evt->Event_Date)->format('M d, Y') }})
                        </option>
                    @endforeach
                </select>

                @if ($selectedEventFilter)
                    <a href="{{ route('admin.dashboard', ['tab' => 'events']) }}" class="button secondary" style="padding: 6px 12px; font-size: 0.85rem; border-radius: 6px; text-decoration: none; color: #ef4444; border: 1px solid #fca5a5;">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        @if ($eventRegistrations->isEmpty())
            <p style="color: #94a3b8; font-style: italic; margin-top: 12px; padding: 20px; text-align: center;">No residents have enlisted for this activity yet.</p>
        @else
            <div style="overflow-x: auto;">
                <table class="table" style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.85rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e2e8f0; color: #475569;">
                            <th style="padding: 10px;">Name</th>
                            <th style="padding: 10px;">Birthday</th>
                            <th style="padding: 10px; text-align: center;">Age</th>
                            <th style="padding: 10px;">Event Enlisted</th>
                            <th style="padding: 10px;">Purok</th>
                            <th style="padding: 10px;">Address</th>
                            <th style="padding: 10px;">Contact Number</th>
                            <th style="padding: 10px;">Enlisted At</th>
                            <th style="padding: 10px; text-align: center;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($eventRegistrations as $reg)
                            @php
                                $bday = !empty($reg->Date_of_Birth) ? \Carbon\Carbon::parse($reg->Date_of_Birth) : null;
                                $age = $bday ? $bday->age : '—';
                                $purok = $reg->Zone_Purok ?? 'Purok 1';
                                $houseNum = $reg->House_Number ? '#'.$reg->House_Number.', ' : '';
                                $fullAddress = $houseNum . $purok . ', Bagumbayan, Daraga, Albay';
                            @endphp
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 10px; font-weight: 600; color: #0f172a; white-space: nowrap;">
                                    {{ $reg->Last_Name }}, {{ $reg->First_Name }} {{ $reg->Middle_Name ?? '' }}
                                </td>
                                <td style="padding: 12px 10px; color: #475569; white-space: nowrap;">
                                    {{ $bday ? $bday->format('M d, Y') : '—' }}
                                </td>
                                <td style="padding: 12px 10px; text-align: center; color: #334155; font-weight: 600;">
                                    {{ $age }}
                                </td>
                                <td style="padding: 12px 10px; color: #1e293b;">
                                    <strong>{{ $reg->Event_Name }}</strong><br>
                                    <span style="font-size: 0.78rem; color: #64748b;">{{ \Carbon\Carbon::parse($reg->Event_Date)->format('M d, Y g:i A') }}</span>
                                </td>
                                <td style="padding: 12px 10px; color: #475569; white-space: nowrap;">
                                    {{ $purok }}
                                </td>
                                <td style="padding: 12px 10px; color: #475569; max-width: 200px;">
                                    {{ $fullAddress }}
                                </td>
                                <td style="padding: 12px 10px; color: #475569; white-space: nowrap;">
                                    {{ $reg->Contact_Number ?? 'N/A' }}
                                </td>
                                <td style="padding: 12px 10px; font-size: 0.82rem; color: #64748b; white-space: nowrap;">
                                    {{ \Carbon\Carbon::parse($reg->Date_Registered)->format('M d, Y h:i A') }}
                                </td>
                                <td style="padding: 12px 10px; text-align: center; white-space: nowrap;">
                                    <span style="display: inline-block; padding: 4px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; background: #dcfce7; color: #166534; border: 1px solid #bbf7d0;">
                                        {{ $reg->Attendance_Status ?? 'Confirmed' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </article>

    <!-- 2. LOWER PART: Schedule New Activity & Manage Scheduled Activities -->
    <section class="panels" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 24px;">
        <!-- Schedule New Activity Card -->
        <article class="card">
            <h2 style="font-size: 1.25rem; margin-bottom: 8px;">Schedule New Activity</h2>
            <p class="subtext" style="color: #64748b; font-size: 0.85rem; margin-bottom: 16px;">Publish a new barangay assembly, vaccination drive, or outreach program.</p>
            
            @if (session('status'))
                <p class="badge good" style="margin-bottom: 16px;">{{ session('status') }}</p>
            @endif

            <form class="admin-form" method="POST" action="{{ route('admin.events.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="active_tab" value="events">
                <div class="form-grid">
                    <label class="form-field">Event title
                        <input name="event_name" value="{{ old('event_name') }}" placeholder="e.g. Barangay Clean-up Drive" required>
                    </label>
                    <label class="form-field">Venue
                        <input name="location" value="{{ old('location') }}" placeholder="e.g. Covered Court" required>
                    </label>
                    <label class="form-field">Start date & time
                        <input type="datetime-local" name="event_date" value="{{ old('event_date') }}" required>
                    </label>
                    <label class="form-field">End date & time <span style="font-weight: 400; color: var(--muted);">(optional)</span>
                        <input type="datetime-local" name="end_date" value="{{ old('end_date') }}">
                    </label>
                    <label class="form-field">Available slots
                        <input type="number" name="available_slots" min="1" value="{{ old('available_slots', 50) }}" placeholder="e.g. 50" required>
                    </label>
                    <label class="form-field">Cover image <span style="font-weight: 400; color: var(--muted);">(optional)</span>
                        <input type="file" name="cover_image" accept="image/*">
                    </label>
                </div>
                <label class="form-field" style="margin-top: 16px; display: block;">Summary
                    <textarea name="summary" rows="3" placeholder="Brief event description">{{ old('summary') }}</textarea>
                </label>
                @if ($errors->any())
                    <div class="badge alert" style="margin-top: 16px;">{{ $errors->first() }}</div>
                @endif
                <button type="submit" class="form-submit" style="margin-top: 16px;">Save & Publish Event</button>
            </form>
        </article>

        <!-- Scheduled Activities Table (Edit / Delete) -->
        <article class="card">
            <h2 style="font-size: 1.25rem; margin-bottom: 8px;">Scheduled Activities</h2>
            <p class="subtext" style="color: #64748b; font-size: 0.85rem; margin-bottom: 16px;">Active programs, live slots filled, and event modifications.</p>
            <div class="table-wrap" style="overflow-x: auto;">
                <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e2e8f0; color: #475569; text-align: left;">
                            <th style="padding: 10px;">ID</th>
                            <th style="padding: 10px;">Title</th>
                            <th style="padding: 10px;">Schedule</th>
                            <th style="padding: 10px;">Venue</th>
                            <th style="padding: 10px; text-align: center;">Slots</th>
                            <th style="padding: 10px; text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($events as $event)
                            @php
                                $eventId = $event->Event_ID ?? $event->id ?? null;
                                $title = $event->Event_Name ?? $event->title ?? 'Untitled Event';
                                $venue = $event->Location ?? $event->venue ?? 'TBA';
                                $capacity = (int)($event->Available_Slots ?? $event->available_slots ?? 0);
                                $registered = (int)($event->registered_count ?? 0);
                                
                                $startDate = $event->Event_Date ? \Carbon\Carbon::parse($event->Event_Date)->format('M d, Y') : ($event->date ?? 'N/A');
                                $startTime = $event->Event_Date ? \Carbon\Carbon::parse($event->Event_Date)->format('g:i A') : (!empty($event->time) ? \Carbon\Carbon::parse($event->time)->format('g:i A') : '');
                                
                                $isFull = $capacity > 0 && $registered >= $capacity;
                            @endphp
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 10px; font-weight: 700; color: #1e293b;">
                                    #EV-{{ str_pad($eventId, 3, '0', STR_PAD_LEFT) }}
                                </td>
                                <td style="padding: 10px; font-weight: 600; color: #0f172a;">
                                    {{ $title }}
                                </td>
                                <td style="padding: 10px; color: #475569; white-space: nowrap;">
                                    {{ $startDate }}<br><small style="color: #64748b;">{{ $startTime }}</small>
                                </td>
                                <td style="padding: 10px; color: #475569;">
                                    {{ $venue }}
                                </td>
                                <td style="padding: 10px; text-align: center; white-space: nowrap;">
                                    <span class="badge {{ $isFull ? 'alert' : 'good' }}" style="padding: 3px 8px; border-radius: 6px; font-size: 0.75rem;">
                                        {{ $registered }} / {{ $capacity }}
                                    </span>
                                </td>
                                <td style="padding: 10px; text-align: center; white-space: nowrap;">
                                    <div style="display: inline-flex; gap: 6px;">
                                        <a href="{{ route('admin.events.edit', $eventId) }}" class="button secondary" style="padding: 4px 10px; font-size: 0.75rem;">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.events.destroy', $eventId) }}" onsubmit="return confirm('Delete this event?');">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="active_tab" value="events">
                                            <button type="submit" class="form-submit" style="padding: 4px 10px; background: #b45142; color: #fff; font-size: 0.75rem; border: none; border-radius: 4px; cursor: pointer;">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: #94a3b8; padding: 20px;">No events scheduled yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>
</section>
</section>
    </section>


@endsection