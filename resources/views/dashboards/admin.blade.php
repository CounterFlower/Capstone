@extends('layouts.dashboard')

@section('content')
    <header class="topbar">
        <div>
            <p class="eyebrow">2.1.4 Analytics Reporting and Decision Support</p>
            <h1>Administrative Dashboard</h1>
            <p>High-level operational view for barangay officials. This layout is meant for service load, case volume, document turnaround, and monthly performance tracking.</p>
        </div>
        <div class="status-pill">Admin view</div>
    </header>

    <section class="stats">
        <article class="stat-card"><span>Total Residents</span><strong>1,254</strong></article>
        <article class="stat-card"><span>Pending Documents</span><strong>36</strong></article>
        <article class="stat-card"><span>Open Cases</span><strong>14</strong></article>
        <article class="stat-card"><span>Posted Announcements</span><strong>08</strong></article>
    </section>

    <section class="split">
        <article class="card">
            <h3>Operational Snapshot</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Service Area</th>
                        <th>Current Load</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Document Requests</td>
                        <td>36 active requests</td>
                        <td><span class="badge warn">Watch</span></td>
                    </tr>
                    <tr>
                        <td>Resident Registration</td>
                        <td>12 profiles pending review</td>
                        <td><span class="badge good">Stable</span></td>
                    </tr>
                    <tr>
                        <td>Incident Monitoring</td>
                        <td>14 active cases</td>
                        <td><span class="badge alert">Needs action</span></td>
                    </tr>
                </tbody>
            </table>
        </article>

        <article class="card">
            <h3>Decision Support Notes</h3>
            <div class="list">
                <div class="list-item">
                    <strong>Peak request period</strong>
                    Clearance requests are highest during the first week of the month.
                </div>
                <div class="list-item">
                    <strong>Case handling risk</strong>
                    Open incident count is rising faster than closed cases this week.
                </div>
                <div class="list-item">
                    <strong>Recommended action</strong>
                    Assign one staff member to document validation and another to incident follow-up.
                </div>
            </div>
        </article>
    </section>
@endsection
