@extends('layouts.dashboard')

@section('content')
    <header class="topbar">
        <div>
            <p class="eyebrow">2.1.5 Incident Reporting and Case Monitoring</p>
            <h1>Incident Reporting and Case Monitoring</h1>
            <p>Layout for report intake, case categorization, assigned officer tracking, and resolution timeline monitoring.</p>
        </div>
        <div class="status-pill">Case tracker</div>
    </header>

    <section class="stats">
        <article class="stat-card"><span>New Reports</span><strong>05</strong></article>
        <article class="stat-card"><span>Under Review</span><strong>08</strong></article>
        <article class="stat-card"><span>Escalated Cases</span><strong>03</strong></article>
        <article class="stat-card"><span>Resolved This Month</span><strong>19</strong></article>
    </section>

    <section class="split">
        <article class="card">
            <h3>Case Board</h3>
            <div class="list">
                <div class="queue-item">
                    <strong>Noise Complaint - Case #IR-014</strong>
                    Filed today, awaiting barangay officer review.
                </div>
                <div class="queue-item">
                    <strong>Boundary Dispute - Case #IR-011</strong>
                    Mediation schedule set for Friday afternoon.
                </div>
                <div class="queue-item">
                    <strong>Minor Physical Altercation - Case #IR-009</strong>
                    Evidence submitted, case elevated for further assessment.
                </div>
            </div>
        </article>

        <article class="card">
            <h3>Monitoring Priorities</h3>
            <div class="list">
                <div class="list-item"><strong>Intake quality</strong> Capture complete incident type, date, location, and involved residents.</div>
                <div class="list-item"><strong>Status tracking</strong> Monitor whether a case is new, under review, in mediation, escalated, or resolved.</div>
                <div class="list-item"><strong>Case history</strong> Preserve chronological actions and outcomes for future reference.</div>
            </div>
        </article>
    </section>
@endsection
