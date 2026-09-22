<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KP Form No. 7 - Case {{ $incident->Incident_ID }}</title>
    <style>
        :root {
            --ink: #111827;
            --blue: #3f709e;
            --line: #9ca3af;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            color: var(--ink);
            background: #e5e7eb;
            font-family: "Times New Roman", Georgia, serif;
            font-size: 10pt;
        }
        .sheet {
            width: min(8.5in, calc(100% - 24px));
            min-height: 11in;
            margin: 18px auto;
            padding: 0.28in 0.42in 0.35in;
            background: #fff;
            border-radius: 20px;
        }
        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            font-family: Arial, Helvetica, sans-serif;
        }
        .toolbar a,
        .toolbar button {
            border: 1px solid #94a3b8;
            border-radius: 5px;
            padding: 7px 12px;
            color: #172033;
            background: #fff;
            font-size: 9pt;
            text-decoration: none;
            cursor: pointer;
        }
        .toolbar button { color: #fff; background: #2563eb; border-color: #2563eb; }
        .header {
            position: relative;
            min-height: 105px;
            padding: 3px 88px 12px;
            border-bottom: 1px dashed #9ca3af;
            text-align: center;
        }
        .header-logo {
            position: absolute;
            top: 0;
            width: 58px;
            height: 58px;
            object-fit: contain;
        }
        .header-logo.left { left: 4px; }
        .header-logo.right { right: 4px; }
        .header p { margin: 1px 0; font-size: 8pt; }
        .header h1 {
            margin: 12px 0 1px;
            color: var(--blue);
            font-size: 17pt;
            letter-spacing: 0.05em;
        }
        .header .form-number { font-size: 8.5pt; font-weight: 700; }
        .case-block {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 34px;
            margin-top: 15px;
        }
        .left-block { padding-left: 2px; }
        .right-block { padding-top: 1px; }
        .field { margin-bottom: 10px; }
        .label { display: block; margin-bottom: 3px; font-size: 9pt; }
        .line-value {
            min-height: 22px;
            padding: 3px 5px;
            border-bottom: 1px solid var(--line);
            font-size: 10pt;
        }
        .against { margin: 3px 0 7px; text-align: center; font-size: 9pt; }
        .complaint-heading {
            margin: 15px 0 8px;
            color: var(--blue);
            font-size: 18pt;
            letter-spacing: 0.13em;
            text-align: center;
        }
        .instruction {
            max-width: 92%;
            margin: 0 auto 6px;
            text-align: center;
            font-size: 8.5pt;
            line-height: 1.25;
        }
        .ruled-box {
            min-height: 116px;
            padding: 3px 5px;
            border: 0;
            background: repeating-linear-gradient(
                to bottom,
                transparent 0,
                transparent 20px,
                var(--line) 20px,
                var(--line) 21px
            );
            white-space: pre-wrap;
            line-height: 21px;
        }
        .ruled-box.relief { min-height: 76px; }
        .made-row,
        .received-row {
            display: grid;
            grid-template-columns: 1fr 0.9fr;
            gap: 24px;
            align-items: end;
            margin-top: 8px;
        }
        .date-text { padding: 7px 0 2px; font-size: 9pt; }
        .signature-line { height: 25px; border-bottom: 1px solid var(--line); }
        .signature-label { padding-top: 4px; text-align: center; font-size: 9pt; }
        .bottom-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            align-items: end;
            margin-top: 30px;
        }
        .received-row { grid-template-columns: 1fr 0.9fr; margin-top: 20px; }
        .case-meta { margin-top: 7px; color: #64748b; font-family: Arial, Helvetica, sans-serif; font-size: 7.5pt; text-align: right; }
        @media print {
            body { background: #fff; }
            .sheet { width: auto; min-height: auto; margin: 0; padding: 0; border-radius: 0; }
            .toolbar { display: none; }
            @page { size: letter; margin: 0.28in 0.42in; }
        }
        @media (max-width: 640px) {
            .sheet { width: calc(100% - 12px); margin: 6px auto; padding: 18px 12px; border-radius: 12px; }
            .header { padding-left: 68px; padding-right: 68px; }
            .header-logo { width: 48px; height: 48px; }
            .case-block, .made-row, .received-row, .bottom-row { grid-template-columns: 1fr; gap: 0; }
            .right-block { margin-top: 4px; }
            .bottom-row { margin-top: 18px; }
        }
    </style>
</head>
<body>
    @php
        $complainant = trim($incident->complainant_name ?? '') ?: trim($incident->guest_name ?? '') ?: 'Unspecified';
        $respondent = trim($incident->respondent_name ?? '') ?: 'Unspecified';
        $dateFiled = $incident->Date_Filed ?? $incident->Date_Reported;
        $dateLabel = $dateFiled ? \Carbon\Carbon::parse($dateFiled) : null;
    @endphp

    <main class="sheet">
        <div class="toolbar">
            <a href="{{ route('admin.incidents.review', $incident->Incident_ID) }}">Back to Review</a>
            <button type="button" onclick="window.print()">Print Incident Report</button>
        </div>

        <header class="header">
            <img src="{{ asset('images/bagumbayan_logo.png') }}" alt="Bagumbayan seal" class="header-logo left">
            <img src="{{ asset('images/bagong_logo.png') }}" alt="Bagong Pilipinas logo" class="header-logo right">
            <p>Republic of the Philippines</p>
            <p>Region V</p>
            <p>Barangay Government of Bagumbayan</p>
            <p>Daraga, Albay</p>
            <h1>OFFICE OF THE PUNONG BARANGAY</h1>
            <p class="form-number">KP FORM NO. 7</p>
        </header>

        <section class="case-block">
            <div class="left-block">
                <div class="field">
                    <span class="label">Complainant/s</span>
                    <div class="line-value">{{ $complainant }}</div>
                </div>
                <div class="against">-Against-</div>
                <div class="field">
                    <span class="label">Respondent/s</span>
                    <div class="line-value">{{ $respondent }}</div>
                </div>
            </div>
            <div class="right-block">
                <div class="field">
                    <span class="label">Barangay Case No.</span>
                    <div class="line-value">IR-{{ str_pad($incident->Incident_ID, 5, '0', STR_PAD_LEFT) }}</div>
                </div>
                <div class="field">
                    <span class="label">For:</span>
                    <div class="line-value">{{ $incident->category_name ?? 'Complaint' }}</div>
                </div>
            </div>
        </section>

        <section>
            <h2 class="complaint-heading">COMPLAINT</h2>
            <p class="instruction">I/WE hereby complain against above named respondent/s for violating my/our rights and interest in the following manner:</p>
            <div class="ruled-box">{{ $incident->Description ?? '' }}</div>

            <p class="instruction" style="margin-top: 9px;">THEREFORE, I/WE pray that the following relief/s be granted to me/us in accordance with law<br>and / or equity:</p>
            <div class="ruled-box relief">{{ $incident->Requested_Relief ?? '' }}</div>
        </section>

        <section class="made-row">
            <div class="date-text">Made this ____ day of ______________ 20____.</div>
            <div>
                <div class="signature-line"></div>
                <div class="signature-label">Complainant/s</div>
            </div>
        </section>

        <section class="received-row">
            <div class="date-text">Received and filed this ____ day of ______________ 20____.</div>
            <div></div>
        </section>

        <section class="bottom-row">
            <div>
                <div class="signature-line"></div>
                <div class="signature-label">Brgy. Secretary / Kwd. On Duty</div>
            </div>
            <div>
                <div class="signature-line"></div>
                <div class="signature-label"><strong>HON. MARK C. MAGALONA</strong><br>Punong Barangay</div>
            </div>
        </section>

        @if ($dateLabel)
            <div class="case-meta">Filed {{ $dateLabel->format('M d, Y h:i A') }} · Status: {{ $incident->Resolution_Status ?? 'Pending' }}</div>
        @endif
    </main>
</body>
</html>
