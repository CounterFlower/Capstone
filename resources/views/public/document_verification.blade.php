<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Bagumbayan - Document Authentication</title>
    <style>
        body { font-family: "Trebuchet MS", "Segoe UI", system-ui, -apple-system, sans-serif; background: #F7FAFC; color: #2D3748; margin: 0; padding: 24px; }
        .container { max-width: 680px; margin: 0 auto; background: #FFFFFF; border-radius: 16px; box-shadow: 0 10px 30px rgba(26, 54, 93, 0.08); overflow: hidden; border: 1px solid rgba(45, 55, 72, 0.12); }
        .badge-header { padding: 22px; text-align: center; font-weight: 700; font-size: 1.15rem; letter-spacing: 0.02em; }
        .valid { background: #1A365D; color: #FFFFFF; border-bottom: 3px solid #D69E2E; }
        .invalid { background: #C53030; color: #FFFFFF; border-bottom: 3px solid #9B2C2C; }
        .content { padding: 28px; }
        .details-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .details-table td { padding: 12px 10px; border-bottom: 1px solid #EDF2F7; font-size: 0.95rem; }
        .details-table td:first-child { color: #718096; font-weight: 600; width: 38%; }
        .seal { text-align: center; margin-top: 24px; font-size: 0.85rem; color: #718096; font-style: italic; }
    </style>
</head>
<body>
    <div class="container">
        @if ($isValid)
            <div class="badge-header valid">
                ✓ Officially Verified &amp; Authentic Barangay Record
            </div>
            <div class="content">
                <p style="margin-top: 0; line-height: 1.6; color: #2D3748;">
                    Barangay Bagumbayan, Daraga, Albay certifies that the document presented matches official local records:
                </p>

                <table class="details-table">
                    <tr>
                        <td>Document Type</td>
                        <td><strong style="color: #1A365D;">{{ $document->Document_Type }}</strong></td>
                    </tr>
                    <tr>
                        <td>Control / Req ID</td>
                        <td>#DOC-{{ str_pad($document->Request_ID, 3, '0', STR_PAD_LEFT) }}</td>
                    </tr>
                    <tr>
                        <td>Resident Name</td>
                        <td><strong style="color: #1A365D;">{{ strtoupper($document->resident_name) }}</strong></td>
                    </tr>
                    <tr>
                        <td>Civil Status</td>
                        <td>{{ ucfirst($document->Civil_Status ?? 'Single') }}</td>
                    </tr>
                    <tr>
                        <td>Jurisdiction / Address</td>
                        <td>{{ $document->Zone_Purok ?? 'Purok 4' }}, Bagumbayan, Daraga, Albay</td>
                    </tr>
                    <tr>
                        <td>Stated Purpose</td>
                        <td>{{ $document->Purpose }}</td>
                    </tr>
                    <tr>
                        <td>Issuance Status</td>
                        <td><strong style="color: #2B6CB0;">{{ $document->Status }}</strong></td>
                    </tr>
                    <tr>
                        <td>Authorizing Signatory</td>
                        <td>HON. {{ $captainName }} (Punong Barangay)</td>
                    </tr>
                </table>

                <div class="seal">
                    Digitally validated via Barangay Bagumbayan Local Registry System.
                </div>
            </div>
        @else
            <div class="badge-header invalid">
                ⚠️ Verification Failed
            </div>
            <div class="content" style="text-align: center;">
                <p style="color: #C53030; font-size: 1.05rem; font-weight: 700;">{{ $message }}</p>
                <p style="font-size: 0.9rem; color: #718096;">If you believe this is an error, contact the Barangay Bagumbayan Hall secretariat.</p>
            </div>
        @endif
    </div>
</body>
</html>