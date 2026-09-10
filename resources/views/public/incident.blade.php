@extends('layouts.public')

@section('content')
    <!-- Leaflet Assets -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="{{ asset('js/barangay-map-data.js') }}"></script>

    <style>
        .form-page {
            display: grid;
            gap: 20px;
        }

        .gis-grid {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .hazard-map {
            padding: 22px;
            border: 1px solid rgba(22, 48, 36, 0.12);
            border-radius: 24px;
            background: linear-gradient(180deg, #f7fbf7 0%, #edf4ed 100%);
            box-shadow: 0 18px 42px rgba(22, 48, 36, 0.1);
        }

        .gis-card {
            padding: 24px;
            border: 1px solid rgba(22, 48, 36, 0.12);
            border-radius: 24px;
            background: #fffefb;
            box-shadow: 0 18px 42px rgba(22, 48, 36, 0.1);
        }

        .gis-card h3 {
            margin-bottom: 14px;
        }

        .gis-meta {
            display: grid;
            gap: 12px;
        }

        .gis-meta .list-item {
            margin-top: 0;
        }

        .hazard-legend {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 14px;
        }

        .gis-frame {
            width: 100%;
            min-height: 420px;
            border: 0;
            border-radius: 18px;
            background: #dbe7df;
            z-index: 0;
        }

        .geo-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px 16px;
            background: #2563eb;
            color: #ffffff;
            border: 0;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .geo-btn:hover {
            background: #1d4ed8;
        }

        .geo-status-banner {
            font-size: 0.82rem;
            line-height: 1.4;
            color: #475569;
            background: #f1f5f9;
            padding: 8px 12px;
            border-radius: 8px;
            border-left: 3px solid #3b82f6;
            margin-top: 6px;
        }

        .paper-form {
            padding: 28px;
            border: 1px solid rgba(22, 48, 36, 0.12);
            border-radius: 28px;
            background: #fffefb;
            box-shadow: 0 18px 42px rgba(22, 48, 36, 0.1);
        }

        .paper-header {
            text-align: center;
            padding-bottom: 18px;
            margin-bottom: 20px;
            border-bottom: 1px dashed rgba(22, 48, 36, 0.28);
        }

        .paper-header h3 {
            margin-top: 6px;
            font-size: 1.9rem;
            letter-spacing: 0.08em;
            color: #456f8f;
        }

        .paper-header p {
            color: #2d2d2d;
            line-height: 1.5;
        }

        .paper-top {
            display: grid;
            grid-template-columns: 1fr 0.95fr;
            gap: 28px;
            margin-bottom: 18px;
        }

        .paper-block {
            display: grid;
            gap: 12px;
        }

        .line-group {
            display: grid;
            gap: 6px;
        }

        .line-group label,
        .paper-note {
            color: #37463f;
            font-weight: 700;
        }

        .line-input,
        .line-textarea {
            width: 100%;
            border: 0;
            border-bottom: 1px solid rgba(22, 48, 36, 0.46);
            padding: 10px 4px 6px;
            background: transparent;
            font: inherit;
            color: var(--text);
        }

        .line-textarea {
            min-height: 120px;
            resize: vertical;
            border: 1px solid rgba(22, 48, 36, 0.18);
            border-radius: 16px;
            padding: 12px 14px;
        }

        .paper-center {
            text-align: center;
            margin: 16px 0 12px;
        }

        .paper-center h2 {
            font-size: 2rem;
            letter-spacing: 0.22em;
            color: #527b99;
        }

        .paper-copy {
            color: #2d2d2d;
            line-height: 1.7;
            text-align: center;
            margin-bottom: 10px;
        }

        .paper-signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-top: 18px;
        }

        .signature-block {
            display: grid;
            gap: 8px;
            align-content: end;
        }

        .signature-line {
            border-bottom: 1px solid rgba(22, 48, 36, 0.46);
            min-height: 24px;
        }

        .signature-caption {
            text-align: center;
            color: #2d2d2d;
        }

        @media (max-width: 900px) {
            .gis-grid,
            .paper-top,
            .paper-signatures {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <section class="section">
        <div class="section-head">
            <div>
                <p class="eyebrow">Public Service Module</p>
                <h2>Incident Reporting Complaint Form</h2>
            </div>
            <a class="button secondary" href="{{ route('home') }}">Back to Home</a>
        </div>

        <div class="form-page">
            <div class="gis-grid">
                <article class="hazard-map">
                    <p class="eyebrow">Geographic Incident Capture</p>
                    <h3 style="margin-bottom: 14px;">Barangay Bagumbayan Interactive Map</h3>
                    <div id="incident-picker-map" class="gis-frame"></div>
                </article>

                <article class="gis-card">
                    <p class="eyebrow">Location Selection</p>
                    <h3>Incident Location Reference</h3>
                    <div class="gis-meta">
                        <div class="list-item">
                            <strong>Barangay Area:</strong>
                            Bagumbayan, Daraga, Albay, Philippines
                        </div>
                        <div class="list-item">
                            <strong>Selected Coordinates:</strong><br>
                            Latitude: <span id="display-latitude" style="font-family: monospace; font-weight: 700;">{{ old('Latitude', 'Pending selection') }}</span><br>
                            Longitude: <span id="display-longitude" style="font-family: monospace; font-weight: 700;">{{ old('Longitude', 'Pending selection') }}</span>
                        </div>
                        <div class="list-item">
                            <strong>Instruction:</strong><br>
                            <span style="font-size: 0.85rem; color: #4b5563;">
                                Click anywhere on the map or drag the pin directly onto the incident spot. If reporting from the scene, click <strong>Use My Location</strong> below.
                            </span>
                        </div>
                    </div>

                    <div style="margin-top: 16px; display: grid; gap: 8px;">
                        <button type="button" id="btn-use-my-location" class="geo-btn">
                            <span>📍</span> Use My Current Location
                        </button>
                        <div id="geo-status-banner" class="geo-status-banner">
                            Detecting location...
                        </div>
                    </div>
                </article>
            </div>

            <article class="paper-form">
                <div class="paper-header">
                    <p>Republic of the Philippines<br>Region V<br>Barangay Government of Bagumbayan<br>Daraga, Albay</p>
                    <h3>OFFICE OF THE PUNONG BARANGAY</h3>
                    <p><strong>KP FORM NO. 7</strong></p>
                </div>

                <form id="incident-report-form" method="POST" action="{{ route('public.incidents.submit') }}">
                    @csrf

                    <!-- Database Coordinates (incident_blotter: Latitude, Longitude) -->
                    <input type="hidden" name="Latitude" id="incident_latitude" value="{{ old('Latitude', '') }}" required>
                    <input type="hidden" name="Longitude" id="incident_longitude" value="{{ old('Longitude', '') }}" required>

                    @if ($errors->has('Latitude') || $errors->has('Longitude') || $errors->has('latitude') || $errors->has('longitude'))
                        <div class="list-item" style="margin-bottom: 18px; color: #b55343; border-left: 3px solid #b55343; padding-left: 10px;">
                            Please pin the incident location on the map above before submitting.
                        </div>
                    @endif

                    <div class="paper-top" style="margin-bottom: 24px;">
                        <div class="paper-block">
                            <div class="line-group">
                                <label>Complainant Type</label>
                                <select class="line-input" name="reporter_type" id="reporter_type" required>
                                    <option value="">Select type</option>
                                    <option value="resident" {{ old('reporter_type') === 'resident' ? 'selected' : '' }}>Resident</option>
                                    <option value="guest" {{ old('reporter_type') === 'guest' ? 'selected' : '' }}>Guest</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="resident_fields" class="paper-top" style="margin-bottom: 16px; display: none;">
                        <div class="paper-block">
                            <div class="line-group">
                                <label>First Name</label>
                                <input class="line-input" type="text" name="first_name" value="{{ old('first_name') }}" placeholder="Enter first name">
                            </div>
                        </div>
                        <div class="paper-block">
                            <div class="line-group">
                                <label>Middle Name</label>
                                <input class="line-input" type="text" name="middle_name" value="{{ old('middle_name') }}" placeholder="Enter middle name">
                            </div>
                        </div>
                        <div class="paper-block">
                            <div class="line-group">
                                <label>Last Name</label>
                                <input class="line-input" type="text" name="last_name" value="{{ old('last_name') }}" placeholder="Enter last name">
                            </div>
                        </div>
                        <div class="paper-block">
                            <div class="line-group">
                                <label>Birth Date</label>
                                <input class="line-input" type="date" name="date_of_birth" value="{{ old('date_of_birth') }}">
                            </div>
                        </div>
                    </div>

                    <div id="guest_fields" class="paper-top" style="margin-bottom: 16px; display: none;">
                        <div class="paper-block">
                            <div class="line-group">
                                <label>First Name</label>
                                <input class="line-input" type="text" name="guest_first_name" value="{{ old('guest_first_name') }}" placeholder="Enter first name">
                            </div>
                        </div>
                        <div class="paper-block">
                            <div class="line-group">
                                <label>Middle Name</label>
                                <input class="line-input" type="text" name="guest_middle_name" value="{{ old('guest_middle_name') }}" placeholder="Enter middle name">
                            </div>
                        </div>
                        <div class="paper-block">
                            <div class="line-group">
                                <label>Last Name</label>
                                <input class="line-input" type="text" name="guest_last_name" value="{{ old('guest_last_name') }}" placeholder="Enter last name">
                            </div>
                        </div>
                        <div class="paper-block">
                            <div class="line-group">
                                <label>Address</label>
                                <input class="line-input" type="text" name="guest_address" value="{{ old('guest_address') }}" placeholder="Enter complete address">
                            </div>
                        </div>
                        <div class="paper-block">
                            <div class="line-group">
                                <label>Contact Number</label>
                                <input class="line-input" type="text" name="guest_contact_number" value="{{ old('guest_contact_number') }}" placeholder="Enter contact number">
                            </div>
                        </div>
                    </div>

                    <div class="paper-top">
                        <div class="paper-block">
                            <div class="line-group">
                                <label>Complainant/s</label>
                                <input class="line-input" type="text" name="complainant_name" value="{{ old('complainant_name') }}" placeholder="Auto-filled from above input" readonly>
                            </div>
                            <div class="paper-note" style="text-align: center;">-Against-</div>
                            <div class="line-group">
                                <label>Respondent First Name</label>
                                <input class="line-input" type="text" name="respondent_first_name" value="{{ old('respondent_first_name') }}" placeholder="Enter first name">
                            </div>
                            <div class="line-group">
                                <label>Respondent Middle Name</label>
                                <input class="line-input" type="text" name="respondent_middle_name" value="{{ old('respondent_middle_name') }}" placeholder="Optional">
                            </div>
                            <div class="line-group">
                                <label>Respondent Last Name</label>
                                <input class="line-input" type="text" name="respondent_last_name" value="{{ old('respondent_last_name') }}" placeholder="Enter last name">
                            </div>
                            <div class="line-group">
                                <label>Respondent Birth Date</label>
                                <input class="line-input" type="date" name="respondent_date_of_birth" value="{{ old('respondent_date_of_birth') }}">
                            </div>
                        </div>

                        <div class="paper-block">
                            <div class="line-group">
                                <label>Complaint Subject</label>
                                <select class="line-input" name="category_id" required>
                                    <option value="">Select category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->Category_Id }}" {{ old('category_id') == $category->Category_Id ? 'selected' : '' }}>
                                            {{ $category->Category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="paper-center">
                        <h2>COMPLAINT</h2>
                    </div>

                    <p class="paper-copy">I/WE hereby complain against above named respondent/s for violating my/our rights and interest in the following manner:</p>
                    <div class="line-group" style="margin-bottom: 20px;">
                        <textarea class="line-textarea" name="complaint_details" placeholder="Enter the details of the complaint" required>{{ old('complaint_details') }}</textarea>
                    </div>

                    <p class="paper-copy">THEREFORE, I/WE pray that the following relief/s be granted to me/us in accordance with law and / or equity:</p>
                    <div class="line-group" style="margin-bottom: 20px;">
                        <textarea class="line-textarea" name="requested_relief" placeholder="Enter requested relief or resolution">{{ old('requested_relief') }}</textarea>
                    </div>

                    @if (session('status'))
                        <div class="list-item" style="margin-bottom: 16px; color: #1f583a;">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="list-item" style="margin-bottom: 16px; color: #b55343;">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="paper-top" style="margin-top: 8px;">
                        <div class="line-group">
                            <label>Made this</label>
                            <input class="line-input" type="text" placeholder="Day / Month / Year" readonly>
                        </div>
                        <div class="line-group">
                            <label>Complainant/s Signature</label>
                            <input class="line-input" type="text" placeholder="Signature placeholder" readonly>
                        </div>
                    </div>

                    <div class="paper-signatures">
                        <div class="signature-block">
                            <div class="signature-line"></div>
                            <div class="signature-caption">Brgy. Secretary / Kwd. On Duty</div>
                        </div>
                        <div class="signature-block">
                            <div class="signature-line"></div>
                            <div class="signature-caption">Punong Barangay</div>
                        </div>
                    </div>

                    <div class="hero-actions" style="margin-top: 20px;">
                        <button type="submit" class="button primary" style="border: 0; cursor: pointer;">Submit Incident Report</button>
                    </div>
                </form>
            </article>

            <article class="card">
                <h3>Reporting Guidelines</h3>
                <div class="list-item">Please verify the pin location accurately represents where the incident occurred.</div>
                <div class="list-item">False reporting or intentionally fabricated locations are subject to applicable barangay ordinances and laws.</div>
                <div class="list-item">Reports filed outside of regular office hours will be addressed on the following business day.</div>
            </article>
        </div>
    </section>

    <script>
        // --- Complainant and Respondent Field Logic ---
        const reporterType = document.getElementById('reporter_type');
        const residentFields = document.getElementById('resident_fields');
        const guestFields = document.getElementById('guest_fields');
        const complainantName = document.querySelector('input[name="complainant_name"]');
        const respondentFirstName = document.querySelector('input[name="respondent_first_name"]');
        const respondentMiddleName = document.querySelector('input[name="respondent_middle_name"]');
        const respondentLastName = document.querySelector('input[name="respondent_last_name"]');

        const updateComplainantName = () => {
            const nameParts = [];

            if (reporterType.value === 'resident') {
                const first = document.querySelector('input[name="first_name"]')?.value?.trim();
                const middle = document.querySelector('input[name="middle_name"]')?.value?.trim();
                const last = document.querySelector('input[name="last_name"]')?.value?.trim();

                if (first) nameParts.push(first);
                if (middle) nameParts.push(middle);
                if (last) nameParts.push(last);
            }

            if (reporterType.value === 'guest') {
                const first = document.querySelector('input[name="guest_first_name"]')?.value?.trim();
                const middle = document.querySelector('input[name="guest_middle_name"]')?.value?.trim();
                const last = document.querySelector('input[name="guest_last_name"]')?.value?.trim();

                if (first) nameParts.push(first);
                if (middle) nameParts.push(middle);
                if (last) nameParts.push(last);
            }

            complainantName.value = nameParts.join(' ');
        };

        const updateRespondentFields = () => {
            const respondentNameParts = [];
            const first = respondentFirstName?.value?.trim();
            const middle = respondentMiddleName?.value?.trim();
            const last = respondentLastName?.value?.trim();

            if (first) respondentNameParts.push(first);
            if (middle) respondentNameParts.push(middle);
            if (last) respondentNameParts.push(last);
        };

        const updateFormFields = () => {
            const type = reporterType.value;
            residentFields.style.display = type === 'resident' ? 'grid' : 'none';
            guestFields.style.display = type === 'guest' ? 'grid' : 'none';

            residentFields.querySelectorAll('input').forEach((input) => {
                if (type !== 'resident') {
                    input.removeAttribute('required');
                } else {
                    input.setAttribute('required', 'required');
                }
            });

            guestFields.querySelectorAll('input').forEach((input) => {
                if (type !== 'guest') {
                    input.removeAttribute('required');
                } else {
                    input.setAttribute('required', 'required');
                }
            });

            updateComplainantName();
        };

        reporterType.addEventListener('change', updateFormFields);
        document.querySelectorAll('input[name="first_name"], input[name="middle_name"], input[name="last_name"], input[name="guest_first_name"], input[name="guest_middle_name"], input[name="guest_last_name"], input[name="respondent_first_name"], input[name="respondent_middle_name"], input[name="respondent_last_name"]').forEach((input) => {
            input.addEventListener('input', () => {
                updateComplainantName();
                updateRespondentFields();
            });
        });

        updateFormFields();

        // --- Leaflet & OpenStreetMap Location Picker Logic ---
        document.addEventListener("DOMContentLoaded", function () {
            const geo = window.BagumbayanGeoData;
            const defaultCenter = (geo && geo.center) ? geo.center : [13.142648, 123.716538];

            const latInput = document.getElementById('incident_latitude');
            const lngInput = document.getElementById('incident_longitude');
            const displayLat = document.getElementById('display-latitude');
            const displayLng = document.getElementById('display-longitude');
            const geoStatus = document.getElementById('geo-status-banner');
            const btnMyLocation = document.getElementById('btn-use-my-location');
            const incidentForm = document.getElementById('incident-report-form');

            let initialLat = parseFloat(latInput.value);
            let initialLng = parseFloat(lngInput.value);
            const hasInitial = !isNaN(initialLat) && !isNaN(initialLng);

            const startPos = hasInitial ? [initialLat, initialLng] : defaultCenter;

            const map = L.map('incident-picker-map').setView(startPos, 16);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            if (geo && geo.puroks) {
                geo.puroks.forEach(function (purok) {
                    L.polygon(purok.coords, {
                        color: purok.color,
                        weight: 1.5,
                        opacity: 0.6,
                        fillColor: purok.color,
                        fillOpacity: 0.08,
                        dashArray: '3, 4'
                    }).bindTooltip(purok.name, { sticky: true }).addTo(map);
                });
            }

            let marker = L.marker(startPos, {
                draggable: true,
                autoPan: true
            }).addTo(map);

            marker.bindPopup("<strong>Incident Location</strong><br>Drag pin or click map to reposition.");

            function setCoordinates(lat, lng) {
                const fixedLat = parseFloat(lat).toFixed(6);
                const fixedLng = parseFloat(lng).toFixed(6);

                latInput.value = fixedLat;
                lngInput.value = fixedLng;
                displayLat.textContent = fixedLat;
                displayLng.textContent = fixedLng;

                marker.setLatLng([fixedLat, fixedLng]);
            }

            if (hasInitial) {
                setCoordinates(initialLat, initialLng);
                geoStatus.textContent = "Previously entered location restored.";
            }

            marker.on('dragend', function (e) {
                const pos = e.target.getLatLng();
                setCoordinates(pos.lat, pos.lng);
                geoStatus.textContent = "Location adjusted manually by pin drag.";
            });

            map.on('click', function (e) {
                setCoordinates(e.latlng.lat, e.latlng.lng);
                geoStatus.textContent = "Location selected by map click.";
            });

            function requestGeolocation() {
                if (!navigator.geolocation) {
                    geoStatus.textContent = "Unable to determine location. Please click or drag on the map.";
                    if (!latInput.value) setCoordinates(defaultCenter[0], defaultCenter[1]);
                    return;
                }

                geoStatus.textContent = "Requesting device location...";

                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        setCoordinates(lat, lng);
                        map.setView([lat, lng], 17);
                        marker.openPopup();
                        geoStatus.textContent = "Current location detected and set.";
                    },
                    function (error) {
                        if (!hasInitial && !latInput.value) {
                            setCoordinates(defaultCenter[0], defaultCenter[1]);
                        }
                        geoStatus.textContent = "Unable to determine your current location. Please select the incident location on the map.";
                    },
                    { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
                );
            }

            btnMyLocation.addEventListener('click', requestGeolocation);

            if (!hasInitial) {
                requestGeolocation();
            }

            // Client-side guard against empty coordinate submission
            if (incidentForm) {
                incidentForm.addEventListener('submit', function (e) {
                    const latVal = parseFloat(latInput.value);
                    const lngVal = parseFloat(lngInput.value);

                    if (isNaN(latVal) || isNaN(lngVal)) {
                        e.preventDefault();
                        alert('Please pin or select the incident location on the map before submitting.');
                        document.getElementById('incident-picker-map').scrollIntoView({ behavior: 'smooth' });
                    }
                });
            }

            setTimeout(function () {
                map.invalidateSize();
            }, 250);
        });
    </script>
@endsection