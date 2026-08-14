@extends('layouts.public')

@section('content')
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
                    <p class="eyebrow">GIS Prototype</p>
                    <h3 style="margin-bottom: 14px;">Bagumbayan map reference</h3>
                    <iframe
                        class="gis-frame"
                        title="Bagumbayan GIS prototype map"
                        src="https://www.openstreetmap.org/export/embed.html?bbox=123.7056%2C13.1381%2C123.7256%2C13.1481&layer=mapnik&marker=13.1431%2C123.7156">
                    </iframe>
                </article>

                <article class="gis-card">
                    <p class="eyebrow">GIS Prototype</p>
                    <h3>Incident location reference</h3>
                    <div class="gis-meta">
                        <div class="list-item">
                            <strong>Default location</strong>
                            Bagumbayan, Daraga, Albay, Philippines
                        </div>
                        <div class="list-item">
                            <strong>Map center</strong>
                            <strong>Latitude: 13.1431<br></strong>
                            <strong>Longitude: 123.7156</strong>
                            
                            
                        </div>
                    </div>

                    <div class="hero-actions">
                        <a class="button secondary" href="https://www.openstreetmap.org/?mlat=13.1431&mlon=123.7156#map=16/13.1431/123.7156" target="_blank" rel="noopener noreferrer">Open Full Map</a>
                    </div>
                </article>
            </div>

            <article class="paper-form">
                <div class="paper-header">
                    <p>Republic of the Philippines<br>Region V<br>Barangay Government of Bagumbayan<br>Daraga, Albay</p>
                    <h3>OFFICE OF THE PUNONG BARANGAY</h3>
                    <p><strong>KP FORM NO. 7</strong></p>
                </div>

                <div class="paper-top">
                    <div class="paper-block">
                        <div class="line-group">
                            <label>Complainant/s</label>
                            <input class="line-input" type="text" placeholder="Enter complainant name">
                        </div>
                        <div class="paper-note" style="text-align: center;">-Against-</div>
                        <div class="line-group">
                            <label>Respondent/s</label>
                            <input class="line-input" type="text" placeholder="Enter respondent name">
                        </div>
                    </div>

                    <div class="paper-block">
                        <div class="line-group">
                            <label>For:</label>
                            <input class="line-input" type="text" placeholder="Complaint subject">
                        </div>
                    </div>
                </div>

                <div class="paper-center">
                    <h2>COMPLAINT</h2>
                </div>

                <p class="paper-copy">I/WE hereby complain against above named respondent/s for violating my/our rights and interest in the following manner:</p>
                <div class="line-group" style="margin-bottom: 20px;">
                    <textarea class="line-textarea" placeholder="Enter the details of the complaint"></textarea>
                </div>

                <p class="paper-copy">THEREFORE, I/WE pray that the following relief/s be granted to me/us in accordance with law and / or equity:</p>
                <div class="line-group" style="margin-bottom: 20px;">
                    <textarea class="line-textarea" placeholder="Enter requested relief or resolution"></textarea>
                </div>

                <div class="paper-top" style="margin-top: 8px;">
                    <div class="line-group">
                        <label>Made this</label>
                        <input class="line-input" type="text" placeholder="Day / Month / Year">
                    </div>
                    <div class="line-group">
                        <label>Complainant/s Signature</label>
                        <input class="line-input" type="text" placeholder="Signature placeholder">
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
            </article>

            <article class="card">
                <h3>Prototype notes</h3>
                <div class="list-item">This page now follows the complaint form structure from the attached image.</div>
                <div class="list-item">The layout is still a prototype and does not submit data to the database.</div>
                <div class="list-item">If needed, this exact form can be connected later to a controller and PDF print layout.</div>
            </article>
        </div>
    </section>
@endsection
