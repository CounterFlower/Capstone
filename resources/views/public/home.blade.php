@extends('layouts.public')

@section('content')
    <!-- Leaflet Assets for Community Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <style>
        .community-map-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 24px;
            box-shadow: var(--shadow);
            padding: 24px;
        }

        .map-frame-wrapper {
            position: relative;
            width: 100%;
            height: 520px;
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid var(--line);
        }

        #homepage-community-map {
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .map-legend-overlay {
            position: absolute;
            bottom: 16px;
            left: 16px;
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(8px);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 12px 14px;
            z-index: 1000;
            box-shadow: 0 4px 18px rgba(22, 48, 36, 0.12);
            font-size: 0.78rem;
            max-width: 280px;
            pointer-events: auto;
        }

        .map-legend-overlay h4 {
            margin: 0 0 6px 0;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text);
            border-bottom: 1px solid var(--line);
            padding-bottom: 4px;
        }

        .legend-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px 10px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--text);
            font-weight: 600;
        }

        .legend-color-box {
            width: 12px;
            height: 12px;
            border-radius: 3px;
            flex-shrink: 0;
        }

        .purok-map-label {
            background: rgba(255, 255, 255, 0.9) !important;
            border: 1px solid rgba(22, 48, 36, 0.2) !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1) !important;
            color: var(--text) !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            padding: 2px 6px !important;
            border-radius: 4px !important;
        }

        .landmark-pin-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50% 50% 50% 0;
            transform: rotate(-45deg);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.28);
            border: 2px solid #ffffff;
            cursor: pointer;
            transition: transform 0.15s ease-out;
        }

        .landmark-pin-wrapper:hover {
            transform: rotate(-45deg) scale(1.15);
        }

        .landmark-pin-wrapper svg {
            transform: rotate(45deg);
            width: 16px;
            height: 16px;
            fill: #ffffff;
        }

        @media (max-width: 768px) {
            .map-frame-wrapper {
                height: 400px;
            }
            .map-legend-overlay {
                bottom: 10px;
                left: 10px;
                font-size: 0.72rem;
                padding: 8px 10px;
            }
        }
    </style>

   <section class="hero">
        <section>
            <div class="section-head" style="margin-bottom: 18px;">
                <div>
                    <p class="eyebrow">Barangay Activities</p>
                    <h2>Community event gallery</h2>
                </div>
            </div>

            <!-- Dynamic Event Gallery -->
            <div class="photo-grid">
                @forelse ($galleryEvents as $eventItem)
                    <article class="photo-card">
                        <img src="{{ route('public.photos', ['filename' => $eventItem->image]) }}" alt="{{ $eventItem->title }}">
                        <div class="photo-copy">
                            <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--accent); letter-spacing: 0.05em;">
                                {{ $eventItem->date }} &bull; {{ $eventItem->venue }}
                            </span>
                            <h3 style="margin-top: 4px;">{{ $eventItem->title }}</h3>
                            <p>{{ Str::limit($eventItem->description, 110) }}</p>
                        </div>
                    </article>
                @empty
                    <p style="color: var(--muted); font-style: italic;">No community events posted yet.</p>
                @endforelse
            </div>
        </section>

        <!-- Dynamic Bulletin Board / Announcements -->
        <aside class="notice-card">
            <p class="eyebrow">Latest Announcements</p>
            <h3>Barangay bulletin board</h3>
            <div class="notice-stack">
                @forelse ($announcements as $notice)
                    <div class="notice-item">
                        <strong>{{ $notice['title'] }}</strong>
                        <p style="margin: 4px 0 0; font-size: 0.88rem; line-height: 1.4;">{{ $notice['body'] }}</p>
                    </div>
                @empty
                    <p style="color: var(--muted); font-style: italic;">No current announcements posted.</p>
                @endforelse
            </div>
        </aside>
    </section>

    <section class="section">
        <div class="section-head">
            <div>
                <p class="eyebrow">Resident Services</p>
                <h2>Public access modules</h2>
            </div>
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
                <p>Residents can view document types, processing flow, and request forms.</p>
                <div class="hero-actions">
                    <a class="button secondary" href="{{ route('public.documents') }}">Request Document</a>
                </div>
            </article>
        </div>
    </section>

    <!-- Geographic Information System Map Section -->
    <section class="section">
        <div class="community-map-card">
            <div class="section-head" style="margin-bottom: 14px;">
                <div>
                    <p class="eyebrow">Spatial Reference</p>
                    <h2 style="font-size: 1.8rem;">Barangay Bagumbayan Territorial Map</h2>
                    <p class="section-copy">Interactive boundary layout of all six Puroks and key community landmarks in Daraga, Albay.</p>
                </div>
            </div>

            <div class="map-frame-wrapper">
                <div id="homepage-community-map"></div>

                <!-- Floating Map Legend -->
                <div class="map-legend-overlay">
                    <h4>Purok Directory</h4>
                    <div class="legend-grid">
                        <div class="legend-item"><span class="legend-color-box" style="background:#2563eb;"></span> Purok 1</div>
                        <div class="legend-item"><span class="legend-color-box" style="background:#16a34a;"></span> Purok 2</div>
                        <div class="legend-item"><span class="legend-color-box" style="background:#d97706;"></span> Purok 3</div>
                        <div class="legend-item"><span class="legend-color-box" style="background:#dc2626;"></span> Purok 4</div>
                        <div class="legend-item"><span class="legend-color-box" style="background:#9333ea;"></span> Purok 5</div>
                        <div class="legend-item"><span class="legend-color-box" style="background:#0891b2;"></span> Purok 6</div>
                    </div>
                    <div style="margin-top: 8px; padding-top: 6px; border-top: 1px solid var(--line); display: flex; align-items: center; gap: 6px; color: var(--muted); font-size: 0.75rem;">
                        <span style="display:inline-flex; align-items:center; justify-content:center; width: 14px; height: 14px; border-radius: 50%; background: #059669; color: #fff; font-size: 9px;">★</span>
                        Barangay Landmarks (Themed Pins)
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Dynamic Event Registration Cards -->
    <section class="section">
        <div class="section-head">
            <div>
                <p class="eyebrow">Event Registration</p>
                <h2>Join barangay activities</h2>
            </div>
            <p class="section-copy">Register online to reserve slots for upcoming barangay programs.</p>
        </div>

        <div class="service-grid">
            @forelse ($events as $event)
                <article class="service-card">
                    <h3>{{ $event->Event_Name }}</h3>
                    <p>{{ Str::limit($event->Description ?? $event->description ?? $event->Details ?? $event->details ?? 'Join us for this community activity.', 110) }}</p>
                    <div class="list-item" style="margin-top: 14px; font-size: 0.9rem;">
                        <strong>Date:</strong> {{ \Carbon\Carbon::parse($event->Event_Date)->format('M d, Y') }} at {{ \Carbon\Carbon::parse($event->Event_Date)->format('h:i A') }}<br>
                        <strong>Venue:</strong> {{ $event->Location ?? 'Barangay Hall' }}
                    </div>
                    <div class="hero-actions">
                        <a class="button secondary" href="{{ route('public.events', ['event' => $event->Event_ID]) }}">Register Now</a>
                    </div>
                </article>
            @empty
                <p style="color: var(--muted); font-style: italic;">No activities currently open for registration.</p>
            @endforelse
        </div>
    </section>

    <!-- Dynamic Community Calendar Table -->
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
                        @forelse ($events as $event)
                            <tr>
                                <td style="white-space: nowrap;">{{ \Carbon\Carbon::parse($event->Event_Date)->format('M d, Y') }}</td>
                                <td><strong>{{ $event->Event_Name }}</strong></td>
                                <td>{{ $event->Location ?? 'Barangay Hall' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; color: var(--muted); font-style: italic;">No scheduled activities on the calendar.</td>
                            </tr>
                        @endforelse
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

    <!-- Map Script -->
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const landmarkIcons = {
            gymnasium: `<svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8 0-1.85.63-3.55 1.69-4.9L16.9 18.31C15.55 19.37 13.85 20 12 20zm6.31-3.1L7.1 5.69C8.45 4.63 10.15 4 12 4c4.41 0 8 3.59 8 8 0 1.85-.63 3.55-1.69 4.9z"/></svg>`,
            hall: `<svg viewBox="0 0 24 24"><path d="M12 1L2 6v2h20V6L12 1zm-7 9v9h2v-9H5zm4 0v9h2v-9H9zm4 0v9h2v-9h-2zm4 0v9h2v-9h-2zM2 21v2h20v-2H2z"/></svg>`,
            chapel: `<svg viewBox="0 0 24 24"><path d="M11 2v4H7v2h4v14h2V8h4V6h-4V2h-2z"/></svg>`,
            daycare: `<svg viewBox="0 0 24 24"><path d="M12 2a5 5 0 1 0 5 5 5 5 0 0 0-5-5zm0 8a3 3 0 1 1 3-3 3 3 0 0 1-3 3zm9 11v-1a7 7 0 0 0-14 0v1z"/></svg>`,
            school: `<svg viewBox="0 0 24 24"><path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>`
        };

        const geoCoordinates = {
            wholeBagumbayan: [
                [13.145450, 123.713817],
                [13.145885, 123.714926],
                [13.146194, 123.716484],
                [13.140918, 123.719259],
                [13.139103, 123.716388],
                [13.139407, 123.714253],
                [13.141515, 123.715020]
            ],
            puroks: [
                { name: "Purok 1", color: "#2563eb", coords: [[13.145450, 123.713817], [13.145719, 123.714509], [13.144881, 123.714850], [13.144606, 123.714131]] },
                { name: "Purok 2", color: "#16a34a", coords: [[13.144606, 123.714131], [13.144881, 123.714850], [13.144004, 123.715183], [13.143747, 123.714472]] },
                { name: "Purok 3", color: "#d97706", coords: [[13.143747, 123.714472], [13.144004, 123.715183], [13.143668, 123.715323], [13.143389, 123.716535], [13.141811, 123.718271], [13.141515, 123.715020]] },
                { name: "Purok 4", color: "#dc2626", coords: [[13.141515, 123.715020], [13.141610, 123.716118], [13.140267, 123.717142], [13.139103, 123.716388], [13.139407, 123.714253]] },
                { name: "Purok 5", color: "#9333ea", coords: [[13.141610, 123.716118], [13.140267, 123.717142], [13.139103, 123.716388], [13.140918, 123.719259], [13.141961, 123.719005]] },
                { name: "Purok 6", color: "#0891b2", coords: [[13.145719, 123.714509], [13.145885, 123.714926], [13.146194, 123.716484], [13.143417, 123.718696], [13.141961, 123.719005], [13.141811, 123.718271], [13.143389, 123.716535], [13.143668, 123.715323], [13.144855, 123.714862]] }
            ],
            landmarks: [
                { name: "Bagumbayan Gymnasium", coords: [13.141245, 123.715429], type: "gymnasium", color: "#ea580c" },
                { name: "Barangay Hall", coords: [13.141391, 123.715200], type: "hall", color: "#059669" },
                { name: "Barangay Chapel", coords: [13.141268, 123.715183], type: "chapel", color: "#2563eb" },
                { name: "Daycare Center", coords: [13.141175, 123.715455], type: "daycare", color: "#d97706" },
                { name: "Daraga North Central School", coords: [13.144115, 123.715673], type: "school", color: "#4f46e5" }
            ]
        };

        const map = L.map('homepage-community-map', {
            zoomControl: true,
            scrollWheelZoom: false
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const bagumbayanPolygon = L.polygon(geoCoordinates.wholeBagumbayan, {
            color: "#16372a",
            weight: 3,
            opacity: 0.9,
            fillColor: "#16372a",
            fillOpacity: 0.03,
            dashArray: "6, 6"
        }).addTo(map);

        geoCoordinates.puroks.forEach(function (purok) {
            L.polygon(purok.coords, {
                color: purok.color,
                weight: 2,
                opacity: 0.9,
                fillColor: purok.color,
                fillOpacity: 0.28
            })
            .bindTooltip(purok.name, { permanent: true, direction: "center", className: "purok-map-label" })
            .bindPopup("<strong>" + purok.name + "</strong><br>Barangay Bagumbayan, Daraga, Albay")
            .addTo(map);
        });

        geoCoordinates.landmarks.forEach(function (lm) {
            const iconSvg = landmarkIcons[lm.type] || landmarkIcons.hall;
            const themedPin = L.divIcon({
                className: 'custom-landmark-div-icon',
                html: `<div class="landmark-pin-wrapper" style="background-color: ${lm.color};">${iconSvg}</div>`,
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });

            L.marker(lm.coords, { icon: themedPin })
                .bindPopup(`<strong>${lm.name}</strong><br><span style="color: #61726b; font-size: 11px;">Barangay Landmark</span>`)
                .addTo(map);
        });

        map.fitBounds(bagumbayanPolygon.getBounds(), { padding: [25, 25] });

        window.addEventListener('resize', function () {
            map.invalidateSize();
        });
        setTimeout(function () {
            map.invalidateSize();
        }, 250);
    });
    </script>
@endsection