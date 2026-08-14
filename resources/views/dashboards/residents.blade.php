@extends('layouts.dashboard')

@section('content')
    <header class="topbar">
        <div>
            <p class="eyebrow">2.1.2 Resident Profiling and Centralized Records Management</p>
            <h1>Resident Profile Dashboard</h1>
            <p>Layout for resident records, household grouping, demographic summaries, and profile completeness checks.</p>
        </div>
        <div class="status-pill">Records view</div>
    </header>

    <section class="stats">
        <article class="stat-card"><span>Total Profiles</span><strong>1,254</strong></article>
        <article class="stat-card"><span>Households</span><strong>342</strong></article>
        <article class="stat-card"><span>Senior Citizens</span><strong>119</strong></article>
        <article class="stat-card"><span>Incomplete Records</span><strong>27</strong></article>
    </section>

    <section class="split">
        <article class="card">
            <h3>Profile Summary</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Resident</th>
                        <th>Household</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Maria Santos</td>
                        <td>Purok 2 - H014</td>
                        <td><span class="badge good">Complete</span></td>
                    </tr>
                    <tr>
                        <td>Juan Dela Cruz</td>
                        <td>Purok 4 - H093</td>
                        <td><span class="badge warn">Missing ID</span></td>
                    </tr>
                    <tr>
                        <td>Ana Ramos</td>
                        <td>Purok 1 - H021</td>
                        <td><span class="badge good">Complete</span></td>
                    </tr>
                </tbody>
            </table>
        </article>

        <article class="card">
            <h3>Record Management Focus</h3>
            <div class="list">
                <div class="list-item"><strong>Centralized records</strong> Store resident identity, address, contact, and household linkage in one place.</div>
                <div class="list-item"><strong>Search and filtering</strong> Locate residents by purok, household, age bracket, or status.</div>
                <div class="list-item"><strong>Data quality checks</strong> Flag incomplete or duplicate records for staff review.</div>
            </div>
        </article>
    </section>
@endsection
