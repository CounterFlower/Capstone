@extends('layouts.dashboard')

@section('content')
    <header class="topbar">
        <div>
            <p class="eyebrow">2.1.3 Community Communication, Announcements, and Public Services</p>
            <h1>Announcements Board</h1>
            <p>Layout for public notices, service announcements, schedules, advisories, and basic barangay information publishing.</p>
        </div>
        <div class="status-pill">Public board</div>
    </header>

    <section class="stats">
        <article class="stat-card"><span>Active Posts</span><strong>08</strong></article>
        <article class="stat-card"><span>Scheduled Events</span><strong>04</strong></article>
        <article class="stat-card"><span>Service Advisories</span><strong>03</strong></article>
        <article class="stat-card"><span>Priority Alerts</span><strong>01</strong></article>
    </section>

    <section class="split">
        <article class="card">
            <h3>Current Notices</h3>
            <div class="list">
                <div class="notice-item">
                    <strong>Barangay Assembly</strong>
                    May 14, 2026 at the covered court. Attendance of household heads requested.
                </div>
                <div class="notice-item">
                    <strong>Medical Mission</strong>
                    Free check-up and blood pressure screening this Saturday from 8:00 AM to 12:00 PM.
                </div>
                <div class="notice-item">
                    <strong>Document Service Advisory</strong>
                    Clearance processing will pause for one hour during the scheduled system backup.
                </div>
            </div>
        </article>

        <article class="card">
            <h3>Public Service Blocks</h3>
            <div class="list">
                <div class="list-item"><strong>Announcements</strong> Routine notices and official barangay updates.</div>
                <div class="list-item"><strong>Events</strong> Community meetings, health drives, and local programs.</div>
                <div class="list-item"><strong>Service advisories</strong> Changes in office hours, release schedules, or emergency notices.</div>
            </div>
        </article>
    </section>
@endsection
