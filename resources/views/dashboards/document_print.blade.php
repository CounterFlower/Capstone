<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print {{ $document->Document_Type }} - #DOC-{{ str_pad($document->Request_ID, 3, '0', STR_PAD_LEFT) }}</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            color: #111827;
            background: #ffffff;
            margin: 0;
            padding: 48px;
            font-size: 12pt;
        }
        .header-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 2px solid #000;
    padding-bottom: 12px;
    margin-bottom: 30px;
}

.header-logo {
    width: 90px;
    height: 90px;
    object-fit: contain;
}

.header-text {
    flex: 1;
    text-align: center;
    padding: 0 10px;
}

.header-text h4 {
    margin: 2px 0;
    font-size: 11pt;
    font-weight: 600;
}

.header-text h3 {
    margin: 4px 0;
    font-size: 14pt;
    font-weight: 700;
}

.header-text p {
    margin: 2px 0;
    font-size: 10pt;
    font-style: italic;
}

.doc-title {
    margin-top: 14px;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    font-size: 17pt;
    font-weight: 800;
    text-decoration: underline;

        }
        .salutation {
            font-weight: bold;
            font-size: 12.5pt;
            margin-bottom: 24px;
        }
        .body-text {
            font-size: 12pt;
            line-height: 1.9;
            text-indent: 48px;
            text-align: justify;
            margin: 20px 0;
        }
        .date-issued {
            margin-top: 36px;
            font-size: 12pt;
            line-height: 1.8;
            text-align: justify;
        }
        .signatures-container {
            margin-top: 70px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            page-break-inside: avoid;
        }
        .signature-block {
            text-align: center;
            width: 280px;
        }
        .signature-line {
            border-top: 1.5px solid #000;
            margin-top: 55px;
            padding-top: 6px;
            font-weight: 700;
            font-size: 12pt;
            text-transform: uppercase;
        }
        .signature-title {
            font-size: 10.5pt;
            color: #374151;
            font-weight: 500;
            display: block;
            margin-top: 2px;
        }
        .no-print {
            margin-bottom: 24px;
            padding: 12px 18px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            display: flex;
            gap: 12px;
            align-items: center;
        }
@media print {
    @page {
        margin: 0; /* Suppresses browser URL, title, and date headers/footers */
        size: auto;
    }

    body {
        margin: 0;
        padding: 20mm 25mm; /* Moves document spacing inside body padding instead */
    }

    .no-print {
        display: none !important;
    }
    .header-logo {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}
    </style>
</head>
<body>
    <!-- Action controls (hidden when printing) -->
    <div class="no-print">
        <button onclick="window.print()" style="padding: 8px 18px; background: #0284c7; color: #fff; border: 0; border-radius: 6px; cursor: pointer; font-weight: 700; font-size: 13px;">
            🖨️ Print Document
        </button>
        <button onclick="window.close()" style="padding: 8px 16px; background: #64748b; color: #fff; border: 0; border-radius: 6px; cursor: pointer; font-size: 13px;">
            Close
        </button>
        <span style="font-size: 12px; color: #64748b; margin-left: auto;">
            Prepared by Staff: <strong>{{ $staffName }}</strong>
        </span>
    </div>

    <!-- Official Header -->
<div class="header-container">
    <!-- Left Logo (Bagumbayan) -->
    <img src="{{ asset('images/bagumbayan_logo.png') }}" alt="Daraga Seal" class="header-logo">

    <!-- Center Text -->
    <div class="header-text">
        <h4>REPUBLIC OF THE PHILIPPINES</h4>
        <h4>PROVINCE OF ALBAY • MUNICIPALITY OF DARAGA</h4>
        <h3>BARANGAY BAGUMBAYAN</h3>
        <p>Office of the Punong Barangay</p>
        <div class="doc-title">{{ $document->Document_Type ?? 'BARANGAY CLEARANCE' }}</div>
    </div>

    <!-- Right Logo (Bagong Pilipinas) -->
    <img src="{{ asset('images/bagong_logo.png') }}" alt="Bagong Pilipinas Seal" class="header-logo">
</div>

    <div class="salutation">TO WHOM IT MAY CONCERN:</div>

    <p class="body-text">
        This is to certify that <strong>{{ strtoupper($document->resident_name ?? 'N/A') }}</strong>, of legal age, 
        {{ !empty($document->Civil_Status) ? strtolower($document->Civil_Status) . ',' : '' }} Filipino citizen, 
        is a permanent resident of <strong>{{ $document->Zone_Purok ?? 'Purok ' . ($document->purok ?? '4') }}, Barangay Bagumbayan, Daraga, Albay</strong>.
    </p>

    <p class="body-text">
        According to the records of this office, the subject individual has maintained a reputable character in the community, 
        has shown exemplary conduct as a law-abiding citizen, and has no pending derogatory case or record of misconduct on file in this Barangay.
    </p>

    <p class="body-text">
        This certification is issued upon request of the interested party for the purpose of: 
        <strong><u>{{ strtoupper($document->Purpose ?? 'GENERAL REQUIREMENTS') }}</u></strong>.
    </p>

    <p class="date-issued">
        Issued this <strong>{{ now()->format('jS') }}</strong> day of <strong>{{ now()->format('F, Y') }}</strong> at the Barangay Hall of Bagumbayan, Daraga, Albay, Philippines.
    </p>

    <!-- Signatories Section -->
    <div class="signatures-container">
        <!-- Left: Current Logged In Admin / Staff -->
        <div class="signature-block">
            <div class="signature-line">
                {{ $staffName }}
            </div>
            <span class="signature-title">{{ $staffRole }} / Preparing Officer</span>
        </div>

        <!-- Right: Punong Barangay -->
        <div class="signature-block">
            <div class="signature-line">
                HON. {{ $captainName }}
            </div>
            <span class="signature-title">Punong Barangay</span>
        </div>
    </div>

    <script>
        // Trigger print dialog on load
        window.addEventListener('load', function () {
            window.print();
        });
    </script>
</body>
</html>