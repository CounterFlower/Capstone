<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Bagumbayan - Document Authentication</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; background: #f8fafc; color: #1e293b; margin: 0; padding: 24px; }
        .container { max-width: 680px; margin: 0 auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.06); overflow: hidden; border: 1px solid #e2e8f0; }
        .badge-header { padding: 20px; text-align: center; font-weight: 700; font-size: 1.15rem; }
        .valid { background: #dcfce7; color: #166534; border-bottom: 2px solid #86efac; }
        .invalid { background: #fee2e2; color: #991b1b; border-bottom: 2px solid #fca5a5; }
        .content { padding: 28px; }
        .details-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .details-table td { padding: 10px 8px; border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; }
        .details-table td:first-child { color: #64748b; font-weight: 600; width: 38%; }
        .seal { text-align: center; margin-top: 24px; font-size: 0.85rem; color: #64748b; }
    </style>
</head>
<body>
    <div class="container">
        @if ($isValid)
            <div class="badge-header valid">
                ✓ Officially Verified & Authentic Barangay Record
            </div>
            <div class="content">
                <p style="margin-top: 0; line-height: 1.6;">
                    Barangay Bagumbayan, Daraga, Albay certifies that the document presented matches official local records:
                </p>

                <table class="details-table">
                    <tr>
                        <td>Document Type</td>
                        <td><strong>{{ $document->Document_Type }}</strong></td>
                    </tr>
                    <tr>
                        <td>Control / Req ID</td>
                        <td>#DOC-{{ str_pad($document->Request_ID, 3, '0', STR_PAD_LEFT) }}</td>
                    </tr>
                    <tr>
                        <td>Resident Name</td>
                        <td><strong>{{ strtoupper($document->resident_name) }}</strong></td>
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
                        <td><strong style="color: #16a34a;">{{ $document->Status }}</strong></td>
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
                <p style="color: #991b1b; font-size: 1.05rem;">{{ $message }}</p>
                <p style="font-size: 0.9rem; color: #64748b;">If you believe this is an error, contact the Barangay Bagumbayan Hall secretariat.</p>
            </div>
        @endif
    </div>
</body>
</html>