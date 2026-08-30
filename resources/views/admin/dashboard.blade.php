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

    <section class="tab-panel {{ ($activeTab ?? 'overview') === 'overview' ? 'active' : '' }}" data-tab-panel="overview">
        <section class="stats" id="overview">
            <article class="stat">
                <span>Total Residents</span>
                <strong>1,254</strong>
            </article>
            <article class="stat">
                <span>Pending Document Requests</span>
                <strong>36</strong>
            </article>
            <article class="stat">
                <span>Open Cases</span>
                <strong>14</strong>
            </article>
            <article class="stat">
                <span>Event Sign-Ups</span>
                <strong>{{ count($registrations) }}</strong>
            </article>
        </section>

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
                            <td>5 new submissions today</td>
                            <td><span class="badge alert">Priority</span></td>
                        </tr>
                        <tr>
                            <td>Document Requests</td>
                            <td>36 total requests in queue</td>
                            <td><span class="badge warn">Review</span></td>
                        </tr>
                        <tr>
                            <td>Resident Records</td>
                            <td>27 incomplete profiles</td>
                            <td><span class="badge good">Stable</span></td>
                        </tr>
                        <tr>
                            <td>Event Registrations</td>
                            <td>{{ count($registrations) }} current sign-ups</td>
                            <td><span class="badge good">Tracked</span></td>
                        </tr>
                    </tbody>
                </table>
            </article>

            <article class="card">
                <h2>Quick Notes</h2>
                <div class="list">
                    <div class="list-item">
                        <strong>Pending validation</strong>
                        Clearance requests are the largest current backlog.
                    </div>
                    <div class="list-item">
                        <strong>Case handling</strong>
                        Boundary disputes and noise complaints are the most common incident types this week.
                    </div>
                    <div class="list-item">
                        <strong>Records quality</strong>
                        Several resident entries still need household ID or contact number verification.
                    </div>
                </div>
            </article>
        </section>
    </section>

    <section class="tab-panel {{ ($activeTab ?? 'overview') === 'cases' ? 'active' : '' }}" data-tab-panel="cases">
        <section class="panels" id="cases">
            <article class="card">
                <h2>Case Monitoring</h2>
                <p class="subtext" style="margin-bottom: 16px;">Live incident reports pulled from the barangay system.</p>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Case ID</th>
                            <th>Type</th>
                            <th>Reporter</th>
                            <th>Complaint Details</th>
                            <th>Date Filed</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($caseRecords as $case)
                            <tr>
                                <td>{{ $case->Incident_ID }}</td>
                                <td>{{ $case->Category ?? 'Uncategorized' }}</td>
                                <td>{{ $case->Reporter_Name ?? 'Unknown' }}</td>
                                <td>{{ Str::limit($case->Description ?? 'No description provided', 80) }}</td>
                                <td>{{ $case->Date_Filed ? Carbon::parse($case->Date_Filed)->format('M d, Y') : 'N/A' }}</td>
                                <td><span class="badge {{ $badgeMap[$case->Resolution_Status ?? 'Pending'] ?? 'warn' }}">{{ $case->Resolution_Status ?? 'Pending' }}</span></td>
                                <td>
                                    <form method="POST" action="{{ route('admin.incidents.review') }}" style="display:flex; gap:8px; align-items:center;">
                                        @csrf
                                        <input type="hidden" name="incident_id" value="{{ $case->Incident_ID }}">
                                        <select name="resolution_status" style="padding:8px; border-radius:8px; border:1px solid #d5d8de;">
                                            @foreach (['Pending', 'Active', 'Resolved', 'Escalated'] as $status)
                                                <option value="{{ $status }}" {{ ($case->Resolution_Status ?? 'Pending') === $status ? 'selected' : '' }}>{{ $status }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="form-submit" style="padding:8px 12px;">Review</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">No incident cases found.</td>
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
    </section>

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

    <section class="tab-panel {{ ($activeTab ?? 'overview') === 'requests' ? 'active' : '' }}" data-tab-panel="requests">
        <section class="panels" id="requests">
            <article class="card">
                <h2>Pending Document Requests</h2>
                <p class="subtext" style="margin-bottom: 16px;">Resident requests linked by Resident_ID and awaiting staff approval.</p>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Resident</th>
                            <th>Resident ID</th>
                            <th>Document Type</th>
                            <th>Purpose</th>
                            <th>Date Requested</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pendingDocumentRequests as $request)
                            <tr>
                                <td>{{ $request->Request_ID }}</td>
                                <td>{{ $request->Resident_Name }}</td>
                                <td>{{ $request->Resident_ID }}</td>
                                <td>{{ $request->Document_Type }}</td>
                                <td>{{ $request->Purpose ?: 'Not specified' }}</td>
                                <td>{{ Carbon::parse($request->Date_Requested)->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}</td>
                                <td><span class="badge warn">{{ $request->Status }}</span></td>
                                <td>
                                    <form method="POST" action="{{ route('admin.documents.approve') }}">
                                        @csrf
                                        <input type="hidden" name="request_id" value="{{ $request->Request_ID }}">
                                        <button type="submit" class="form-submit">Approve</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">No pending document requests.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </article>

            <article class="card">
                <h2>Request Categories</h2>
                <div class="list">
                    <div class="list-item"><strong>Barangay Clearance</strong> Highest request volume this month.</div>
                    <div class="list-item"><strong>Certificate of Residency</strong> Most common for school and employment use.</div>
                    <div class="list-item"><strong>Business Endorsement</strong> Lower volume, longer review path.</div>
                    <div class="list-item"><strong>Certificate of Indigency</strong> Frequently requested for assistance and hospital support.</div>
                </div>
            </article>
        </section>
    </section>

    <section class="tab-panel {{ ($activeTab ?? 'overview') === 'events' ? 'active' : '' }}" data-tab-panel="events">
        <section class="panels">
            <article class="card">
                <h2>Create Event</h2>
                @if (session('status'))
                    <p class="badge good" style="margin-bottom: 16px;">{{ session('status') }}</p>
                @endif
                <form class="admin-form" method="POST" action="{{ route('admin.events.store') }}">
                    @csrf
                    <input type="hidden" name="active_tab" value="events">
                    <div class="form-grid">
                        <label class="form-field">Event title<input name="title" value="{{ old('title') }}" placeholder="e.g. Barangay Clean-up Drive" required></label>
                        <label class="form-field">Event date<input type="date" name="date" value="{{ old('date') }}" required></label>
                        <label class="form-field">Event time<input type="time" name="time" value="{{ old('time') }}" required></label>
                        <label class="form-field">Venue<input name="venue" value="{{ old('venue') }}" placeholder="e.g. Covered Court" required></label>
                        <label class="form-field">Available slots<input type="number" name="available_slots" min="1" value="{{ old('available_slots') }}" placeholder="e.g. 50"></label>
                    </div>
                    <label class="form-field" style="margin-top: 16px; display: block;">Summary<textarea name="summary" rows="4" placeholder="Brief event description">{{ old('summary') }}</textarea></label>
                    @if ($errors->any())
                        <div class="badge alert" style="margin-top: 16px;">{{ $errors->first() }}</div>
                    @endif
                    <button type="submit" class="form-submit" style="margin-top: 16px;">Save event</button>
                </form>
            </article>

            <article class="card">
                <h2>Event Registration Summary</h2>
                <p class="subtext" style="margin-bottom: 16px;">Residents who signed up for barangay activities from the public event page.</p>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Resident</th>
                            <th>Event</th>
                            <th>Purok</th>
                            <th>Submitted</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($registrations as $registration)
                            <tr>
                                <td>{{ $registration['reference'] }}</td>
                                <td>{{ $registration['resident_name'] }}</td>
                                <td>{{ $registration['event_title'] }}</td>
                                <td>{{ $registration['purok'] }}</td>
                                <td>{{ $registration['submitted_at'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">No event registrations yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </article>

            <article class="card">
                <h2>Scheduled Activities</h2>
                <div class="list">
                    @forelse ($events as $event)
                        <div class="list-item">
                            <strong>{{ $event->title }}</strong>
                            {{ $event->date }} at {{ Carbon::parse($event->time)->format('g:i A') }}<br>
                            {{ $event->venue }}
                        </div>
                    @empty
                        <div class="list-item">No events created yet.</div>
                    @endforelse
                </div>
            </article>
        </section>
    </section>

    <section class="tab-panel {{ ($activeTab ?? 'overview') === 'analytics' ? 'active' : '' }}" data-tab-panel="analytics">
        <section class="grid" id="analytics">
            <article class="card">
                <h2>Statistical Layouts</h2>
                @foreach ($analyticsBlocks as $metric)
                    <div class="chart-row">
                        <label><span>{{ $metric['label'] }}</span><span>{{ $metric['value'] }}</span></label>
                        <div class="bar-track"><div class="bar-fill" style="width: {{ $metric['width'] }}%;"></div></div>
                    </div>
                @endforeach
            </article>


        </section>
    </section>
@endsection
