@extends('layouts.dashboard')

@section('content')
    <header class="topbar">
        <div>
            <p class="eyebrow">Prototype Overview</p>
            <h1>Barangay Management Layout Set</h1>
            <p>Simple linked layouts for the five requested modules. Each page is focused on layout structure only: cards, tables, queues, and summary blocks you can later connect to real data.</p>
        </div>
        <div class="status-pill">5 modules ready</div>
    </header>

    <section class="stats">
        <article class="stat-card">
            <span>Layouts Created</span>
            <strong>05</strong>
        </article>
        <article class="stat-card">
            <span>Primary Use Cases</span>
            <strong>Records</strong>
        </article>
        <article class="stat-card">
            <span>Operations Focus</span>
            <strong>Service Flow</strong>
        </article>
        <article class="stat-card">
            <span>Readiness</span>
            <strong>Prototype</strong>
        </article>
    </section>

    <section class="panel-grid">
        <article class="card">
            <h3>Administrative Dashboard</h3>
            <p>Executive overview with metrics, service load, case trends, and decision-support panels.</p>
        </article>
        <article class="card">
            <h3>Document Request</h3>
            <p>Queue view for clearances, certifications, approvals, and release status tracking.</p>
        </article>
        <article class="card">
            <h3>Resident Profiles</h3>
            <p>Centralized resident records, demographic breakdown, and household-linked profiles.</p>
        </article>
        <article class="card">
            <h3>Incident Monitoring</h3>
            <p>Report intake, case status, escalation tracking, and action timeline layout.</p>
        </article>
        <article class="card">
            <h3>Announcements Board</h3>
            <p>Public notices, service advisories, event updates, and community information board.</p>
        </article>
    </section>
@endsection
