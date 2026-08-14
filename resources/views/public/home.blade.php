@extends('layouts.public')

@section('content')
    <section class="hero">
        <section>
            <div class="section-head" style="margin-bottom: 18px;">
                <div>
                    <p class="eyebrow">Barangay Activities</p>
                    <h2>Community event gallery</h2>
                </div>
                <p class="section-copy">Top section now uses the photos from your `resources/photos` folder.</p>
            </div>

            <div class="photo-grid">
                @foreach ($photos as $photo)
                    <article class="photo-card">
                        <img src="{{ route('public.photos', ['filename' => $photo['file']]) }}" alt="{{ $photo['title'] }}">
                        <div class="photo-copy">
                            <h3>{{ $photo['title'] }}</h3>
                            <p>{{ $photo['description'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <aside class="notice-card">
            <p class="eyebrow">Latest Announcements</p>
            <h3>Barangay bulletin board</h3>
            <div class="notice-stack">
                <div class="notice-item">
                    <strong>Barangay Assembly</strong>
                    May 16, 2026 at the covered court. Household representatives are expected to attend.
                </div>
                <div class="notice-item">
                    <strong>Medical Mission Schedule</strong>
                    Free check-up and consultation this Saturday, 8:00 AM to 12:00 PM.
                </div>
                <div class="notice-item">
                    <strong>Service Advisory</strong>
                    Clearance printing resumes at 1:00 PM after scheduled system maintenance.
                </div>
            </div>
        </aside>
    </section>

    <section class="section">
        <div class="section-head">
            <div>
                <p class="eyebrow">Resident Services</p>
                <h2>Public access modules</h2>
            </div>
            <p class="section-copy">These modules simulate resident-facing services without database writes.</p>
        </div>

        <div class="service-grid">
            <article class="service-card">
                <h3>Incident Reporting</h3>
                <p>Residents can file complaints or report incidents through a guided intake form layout.</p>
                <div class="hero-actions">
                    <a class="button secondary" href="{{ route('public.incidents') }}">Report Incident</a>
                </div>
            </article>

            <article class="service-card">
                <h3>Document Requests</h3>
                <p>Residents can view document types, processing flow, and prototype request forms.</p>
                <div class="hero-actions">
                    <a class="button secondary" href="{{ route('public.documents') }}">Reqeust Document</a>
                </div>
            </article>
        </div>
    </section>

    <section class="section">
        <div class="section-head">
            <div>
                <p class="eyebrow">Event Registration</p>
                <h2>Join barangay activities</h2>
            </div>
            <p class="section-copy">Residents can register for barangay events through the prototype event module.</p>
        </div>

        <div class="service-grid">
            @foreach ($events as $event)
                <article class="service-card">
                    <h3>{{ $event['title'] }}</h3>
                    <p>{{ $event['summary'] }}</p>
                    <div class="list-item" style="margin-top: 14px;">
                        {{ $event['date'] }} at {{ $event['time'] }}<br>
                        {{ $event['venue'] }}
                    </div>
                    <div class="hero-actions">
                        <a class="button secondary" href="{{ route('public.events', ['event' => $event['id']]) }}">Register Now</a>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <div class="content-grid">
            <article class="card">
                <p class="eyebrow">Upcoming Activities</p>
                <h3>Community calendar</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Activity</th>
                            <th>Venue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>May 12</td>
                            <td>Senior citizen payout assistance</td>
                            <td>Barangay hall</td>
                        </tr>
                        <tr>
                            <td>May 16</td>
                            <td>Barangay assembly</td>
                            <td>Covered court</td>
                        </tr>
                        <tr>
                            <td>May 20</td>
                            <td>Clean-up drive</td>
                            <td>Purok 3 and Purok 4</td>
                        </tr>
                    </tbody>
                </table>
            </article>

            <article class="card">
                <p class="eyebrow">Quick Information</p>
                <h3>Resident reference block</h3>
                <div class="list-item">
                    Office hours: Monday to Friday, 8:00 AM to 5:00 PM
                </div>
                <div class="list-item">
                    Hotline: 09XX-XXX-XXXX
                </div>
                <div class="list-item">
                    Office location: Barangay Hall, Bagumbayan, Daraga, Albay
                </div>
            </article>
        </div>
    </section>
@endsection
