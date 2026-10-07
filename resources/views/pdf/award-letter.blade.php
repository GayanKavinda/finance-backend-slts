<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Letter of Award</title>
    <style>
        @page {
            margin: 0;
            padding: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #1e293b;
            background: #f8fafc;
            padding: 0;
            margin: 0;
        }

        .letter-wrapper {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            position: relative;
        }

        /* Header */
        .header {
            padding: 30px 40px 20px 40px;
            border-bottom: 2px solid #004A99;
            position: relative;
            z-index: 1;
        }

        .logo-row {
            display: table;
            margin-bottom: 8px;
        }

        .logo-icon {
            display: table-cell;
            width: 40px;
            height: 40px;
            vertical-align: middle;
            text-align: center;
            padding-top: 4px;
        }

        .logo-icon img {
            width: 32px;
            height: 32px;
            object-fit: contain;
        }

        .logo-text {
            display: table-cell;
            vertical-align: middle;
            padding-left: 12px;
        }

        .company-name {
            font-size: 16px;
            font-weight: 800;
            color: #003366;
            text-transform: uppercase;
            letter-spacing: -0.3px;
            line-height: 1.2;
        }

        .company-tagline {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            margin-top: 1px;
        }

        .company-address {
            font-size: 10px;
            color: #475569;
            line-height: 1.5;
            margin-top: 4px;
        }

        /* Content */
        .content {
            padding: 30px 40px;
            position: relative;
            z-index: 1;
        }

        .date {
            text-align: right;
            margin-bottom: 20px;
            font-size: 11px;
            color: #64748b;
        }

        .title {
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 20px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .section {
            margin-bottom: 16px;
        }

        .label {
            font-weight: 600;
            color: #334155;
        }

        .details-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 16px;
            margin: 12px 0;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table td {
            padding: 6px 0;
            font-size: 11px;
        }

        .details-label {
            color: #64748b;
            width: 35%;
            font-weight: 600;
        }

        .details-value {
            color: #1e293b;
            font-weight: 600;
        }

        .amount-box {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 14px;
            text-align: center;
            margin-top: 10px;
        }

        .amount-label {
            font-size: 9px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .amount-value {
            font-size: 18px;
            font-weight: 800;
            color: #004A99;
        }

        .signature-area {
            margin-top: 40px;
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
        }

        .signature-line {
            border-bottom: 1px solid #cbd5e1;
            margin-bottom: 6px;
            padding-top: 30px;
        }

        .signature-title {
            font-size: 10px;
            color: #475569;
            font-weight: 600;
        }

        .footer {
            margin-top: 30px;
            padding: 15px 40px;
            background: #0f172a;
            color: #94a3b8;
            position: relative;
            z-index: 1;
        }

        .footer-text {
            font-size: 9px;
            color: #64748b;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>

    <div class="letter-wrapper">
        <!-- Header -->
        <div class="header">
            <div class="logo-row">
                <div class="logo-icon">
                    <img src="{{ public_path('icons/slt_digital_icon.png') }}" alt="SLT Logo">
                </div>
                <div class="logo-text">
                    <div class="company-name">Sri Lanka Telecom Services</div>
                    <div class="company-tagline">Finance Division</div>
                </div>
            </div>
            <div class="company-address">
                Colombo, Sri Lanka | T: +94 11 232 9711 | E: billing@sltservices.lk
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="date">Date: {{ date('d M Y') }}</div>

            <div class="title">Letter of Award</div>

            <div class="section">
                <p>To,</p>
                <p>
                    <strong>{{ $job->selectedContractor->name }}</strong><br>
                    {{ $job->selectedContractor->address ?? 'Contractor Address' }}
                </p>
            </div>

            <div class="section">
                <p>Dear Sir/Madam,</p>
                <p>We are pleased to inform you that following the evaluation of quotations for the project <strong>"{{ $job->tender->tender_name }}"</strong>, your firm has been selected as the successful contractor for the following job:</p>
            </div>

            <div class="details-box">
                <table class="details-table">
                    <tr>
                        <td class="details-label">Job Name:</td>
                        <td class="details-value">{{ $job->name }}</td>
                    </tr>
                    <tr>
                        <td class="details-label">Reference:</td>
                        <td class="details-value">{{ $job->tender->tender_number }}</td>
                    </tr>
                    <tr>
                        <td class="details-label">Award Amount:</td>
                        <td class="details-value"><strong>LKR {{ number_format($job->contractor_quote_amount, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <td class="details-label">Customer:</td>
                        <td class="details-value">{{ $job->customer->name }}</td>
                    </tr>
                </table>
            </div>

            <div class="section">
                <p>You are requested to commence the mobilization process and coordinate with the Site Supervisor for the official work start date. All works must be carried out in accordance with the specifications and terms discussed during the quotation phase.</p>
                <p>Please sign and return a copy of this letter as a token of your acceptance.</p>
            </div>

            <div class="signature-area">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-title">Authorized Signature<br>SLT Services</div>
                </div>
                <div class="signature-box" style="text-align: right;">
                    <div class="signature-line"></div>
                    <div class="signature-title">Accepted By<br>{{ $job->selectedContractor->name }}</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p class="footer-text">This is a computer-generated document. For official verification, please contact our procurement department.</p>
        </div>
    </div>

</body>
</html>
