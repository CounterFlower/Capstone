@extends('layouts.dashboard')

@section('content')
    <header class="topbar">
        <div>
            <p class="eyebrow">2.1.1 Manual, Paper-Based, Fragmented Barangay Workflows</p>
            <h1>Document Request Dashboard</h1>
            <p>Simple service queue layout for digitizing barangay clearances, certificates of residency, and certification requests that are normally handled on paper.</p>
        </div>
        <div class="status-pill">Service queue</div>
    </header>

    <section class="stats">
        <article class="stat-card"><span>New Requests Today</span><strong>09</strong></article>
        <article class="stat-card"><span>For Validation</span><strong>15</strong></article>
        <article class="stat-card"><span>Ready for Release</span><strong>11</strong></article>
        <article class="stat-card"><span>Average Turnaround</span><strong>2 days</strong></article>
    </section>

    <section class="split">
        <article class="card">
            <h3>Request Queue</h3>
            <div class="list">
                <div class="queue-item">
                    <strong>Barangay Clearance - Juan Dela Cruz</strong>
                    Request received, pending staff validation.
                </div>
                <div class="queue-item">
                    <strong>Certificate of Residency - Maria Santos</strong>
                    Verified and waiting for captain approval.
                </div>
                <div class="queue-item">
                    <strong>Business Endorsement - Pedro Ramos</strong>
                    Ready for printing and release.
                </div>
            </div>
        </article>

        <article class="card">
            <h3>Workflow Stages</h3>
            <div class="list">
                <div class="list-item"><strong>1. Intake</strong> Resident submits a request and supporting details.</div>
                <div class="list-item"><strong>2. Validation</strong> Staff checks identity and record completeness.</div>
                <div class="list-item"><strong>3. Approval</strong> Authorized barangay staff approves or returns the request.</div>
                <div class="list-item"><strong>4. Release</strong> Document is printed, recorded, and marked released.</div>
            </div>
        </article>
    </section>
@endsection
