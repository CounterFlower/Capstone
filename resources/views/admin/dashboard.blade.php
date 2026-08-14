@extends('layouts.admin')

@section('content')
    @php
        $caseRecords = [
            ['id' => 'IR-014', 'type' => 'Noise Complaint', 'resident' => 'Juan Dela Cruz', 'respondent' => 'Mark Ramos', 'date' => 'May 07, 2026', 'status' => 'Under Review', 'badge' => 'warn'],
            ['id' => 'IR-013', 'type' => 'Verbal Altercation', 'resident' => 'Liza Mendoza', 'respondent' => 'Joel Mendoza', 'date' => 'May 06, 2026', 'status' => 'For Mediation', 'badge' => 'good'],
            ['id' => 'IR-012', 'type' => 'Boundary Dispute', 'resident' => 'Maria Santos', 'respondent' => 'Carlos Perez', 'date' => 'May 05, 2026', 'status' => 'Scheduled', 'badge' => 'good'],
            ['id' => 'IR-011', 'type' => 'Physical Altercation', 'resident' => 'Pedro Ramos', 'respondent' => 'Nestor Cruz', 'date' => 'May 03, 2026', 'status' => 'Escalated', 'badge' => 'alert'],
            ['id' => 'IR-010', 'type' => 'Property Damage', 'resident' => 'Ana Villanueva', 'respondent' => 'Rico Flores', 'date' => 'May 02, 2026', 'status' => 'Pending Review', 'badge' => 'warn'],
        ];

        $residentProfiles = [
            ['name' => 'Maria Santos', 'household' => 'H-014', 'purok' => 'Purok 2', 'age' => 43, 'civil_status' => 'Married', 'contact' => '0917-555-2104', 'status' => 'Complete', 'badge' => 'good'],
            ['name' => 'Juan Dela Cruz', 'household' => 'H-093', 'purok' => 'Purok 4', 'age' => 35, 'civil_status' => 'Single', 'contact' => '0918-332-1455', 'status' => 'Missing ID', 'badge' => 'warn'],
            ['name' => 'Ana Ramos', 'household' => 'H-021', 'purok' => 'Purok 1', 'age' => 29, 'civil_status' => 'Married', 'contact' => '0921-664-9120', 'status' => 'Complete', 'badge' => 'good'],
            ['name' => 'Pedro Ramos', 'household' => 'H-054', 'purok' => 'Purok 3', 'age' => 52, 'civil_status' => 'Married', 'contact' => '0915-881-7352', 'status' => 'For Verification', 'badge' => 'warn'],
            ['name' => 'Lourdes Mendoza', 'household' => 'H-101', 'purok' => 'Purok 5', 'age' => 67, 'civil_status' => 'Widowed', 'contact' => '0919-440-7188', 'status' => 'Senior Tagged', 'badge' => 'good'],
        ];

        $documentRequests = [
            ['id' => 'DR-101', 'resident' => 'Maria Santos', 'document' => 'Certificate of Residency', 'purpose' => 'School Requirement', 'date' => 'May 07, 2026', 'status' => 'For Approval', 'badge' => 'warn'],
            ['id' => 'DR-099', 'resident' => 'Juan Dela Cruz', 'document' => 'Barangay Clearance', 'purpose' => 'Employment', 'date' => 'May 06, 2026', 'status' => 'Ready for Release', 'badge' => 'good'],
            ['id' => 'DR-097', 'resident' => 'Pedro Ramos', 'document' => 'Business Endorsement', 'purpose' => 'Permit Renewal', 'date' => 'May 05, 2026', 'status' => 'Needs Validation', 'badge' => 'alert'],
            ['id' => 'DR-095', 'resident' => 'Liza Mendoza', 'document' => 'Certificate of Indigency', 'purpose' => 'Medical Assistance', 'date' => 'May 04, 2026', 'status' => 'Processing', 'badge' => 'warn'],
            ['id' => 'DR-093', 'resident' => 'Noel Villanueva', 'document' => 'Barangay Clearance', 'purpose' => 'Loan Application', 'date' => 'May 03, 2026', 'status' => 'Released', 'badge' => 'good'],
        ];

        $analyticsBlocks = [
            ['label' => 'Document requests completed', 'value' => '78%', 'width' => '78%'],
            ['label' => 'Resident profiles completed', 'value' => '92%', 'width' => '92%'],
            ['label' => 'Cases resolved this month', 'value' => '63%', 'width' => '63%'],
            ['label' => 'Event participation fill rate', 'value' => count($registrations) > 0 ? 'Live' : '0%', 'width' => min(count($registrations) * 12, 100) . '%'],
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

    <section class="tab-panel" data-tab-panel="overview">
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

    <section class="tab-panel" data-tab-panel="cases">
        <section class="panels" id="cases">
            <article class="card">
                <h2>Case Monitoring</h2>
                <p class="subtext" style="margin-bottom: 16px;">Sample layout for monitoring reported incidents and complaints from residents.</p>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Case ID</th>
                            <th>Type</th>
                            <th>Resident</th>
                            <th>Respondent</th>
                            <th>Date Filed</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($caseRecords as $case)
                            <tr>
                                <td>{{ $case['id'] }}</td>
                                <td>{{ $case['type'] }}</td>
                                <td>{{ $case['resident'] }}</td>
                                <td>{{ $case['respondent'] }}</td>
                                <td>{{ $case['date'] }}</td>
                                <td><span class="badge {{ $case['badge'] }}">{{ $case['status'] }}</span></td>
                            </tr>
                        @endforeach
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

    <section class="tab-panel" data-tab-panel="residents">
        <section class="panels" id="residents">
            <article class="card">
                <h2>Resident Profile Table</h2>
                <p class="subtext" style="margin-bottom: 16px;">Sample administrative table for centralized resident records management.</p>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Household ID</th>
                            <th>Purok</th>
                            <th>Age</th>
                            <th>Civil Status</th>
                            <th>Contact</th>
                            <th>Profile Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($residentProfiles as $resident)
                            <tr>
                                <td>{{ $resident['name'] }}</td>
                                <td>{{ $resident['household'] }}</td>
                                <td>{{ $resident['purok'] }}</td>
                                <td>{{ $resident['age'] }}</td>
                                <td>{{ $resident['civil_status'] }}</td>
                                <td>{{ $resident['contact'] }}</td>
                                <td><span class="badge {{ $resident['badge'] }}">{{ $resident['status'] }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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

    <section class="tab-panel" data-tab-panel="requests">
        <section class="panels" id="requests">
            <article class="card">
                <h2>Document Request Monitoring</h2>
                <p class="subtext" style="margin-bottom: 16px;">Sample administrative view for tracking resident requests and document types.</p>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Resident</th>
                            <th>Document Type</th>
                            <th>Purpose</th>
                            <th>Date Requested</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($documentRequests as $request)
                            <tr>
                                <td>{{ $request['id'] }}</td>
                                <td>{{ $request['resident'] }}</td>
                                <td>{{ $request['document'] }}</td>
                                <td>{{ $request['purpose'] }}</td>
                                <td>{{ $request['date'] }}</td>
                                <td><span class="badge {{ $request['badge'] }}">{{ $request['status'] }}</span></td>
                            </tr>
                        @endforeach
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

    <section class="tab-panel" data-tab-panel="events">
        <section class="panels">
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
                <h2>Activity Sign-Up Totals</h2>
                <div class="list">
                    @foreach ($eventCounts as $eventCount)
                        <div class="list-item">
                            <strong>{{ $eventCount['title'] }}</strong>
                            {{ $eventCount['date'] }}<br>
                            {{ $eventCount['count'] }} resident sign-up(s)
                        </div>
                    @endforeach
                </div>
            </article>
        </section>
    </section>

    <section class="tab-panel" data-tab-panel="analytics">
        <section class="grid" id="analytics">
            <article class="card">
                <h2>Statistical Layouts</h2>
                @foreach ($analyticsBlocks as $metric)
                    <div class="chart-row">
                        <label><span>{{ $metric['label'] }}</span><span>{{ $metric['value'] }}</span></label>
                        <div class="bar-track"><div class="bar-fill" style="width: {{ $metric['width'] }};"></div></div>
                    </div>
                @endforeach
            </article>


        </section>
    </section>
@endsection
