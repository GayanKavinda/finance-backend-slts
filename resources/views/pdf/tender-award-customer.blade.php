<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tender Award Confirmation</title>
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

            <div class="title">Tender Award Confirmation</div>

            <div class="section">
                <p>To,</p>
                <p>
                    <strong>{{ $tender->customer->name }}</strong><br>
                    {{ $tender->customer->address ?? 'Customer Billing Address' }}
                </p>
            </div>

            <div class="section">
                <p>Dear Valued Partner,</p>
                <p>We are formally acknowledging the award of the contract for the project referenced below. We appreciate the opportunity to work with you and are committed to delivering excellence throughout the project duration.</p>
            </div>

            <table class="details-box" style="border: none; background: transparent; padding: 0;">
                <tr>
                    <td class="details-label">Tender Number:</td>
                    <td class="details-value">{{ $tender->tender_number }}</td>
                </tr>
                <tr>
                    <td class="details-label">Project Name:</td>
                    <td class="details-value">{{ $tender->name }}</td>
                </tr>
                <tr>
                    <td class="details-label">Project Duration:</td>
                    <td class="details-value">{{ \Carbon\Carbon::parse($tender->start_date)->format('d M Y') }} to {{ $tender->end_date ? \Carbon\Carbon::parse($tender->end_date)->format('d M Y') : 'Completion' }}</td>
                </tr>
            </table>

            <div class="section">
                <p>The total awarded value for this contract, as per the finalized agreement, is:</p>
                <div class="amount-box">
                    <div class="amount-label">Awarded Value (Inclusive of all applicable taxes and levies)</div>
                    <div class="amount-value">LKR {{ number_format($tender->awarded_amount, 2) }}</div>
                </div>
            </div>

            <div class="section">
                <p>Our team is currently preparing the necessary project execution plans and mobilizing resources. A formal Tax Invoice for the awarded amount will be submitted to your finance division as per the agreed schedule.</p>
                <p>Should you have any immediate clarifications, please do not hesitate to contact our project management office.</p>
            </div>

            <div class="signature-area">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-title">Director - Procurement<br>SLT Services</div>
                </div>
                <div class="signature-box" style="text-align: right;">
                    <div class="signature-line"></div>
                    <div class="signature-title">Accepted By<br>{{ $tender->customer->name }}</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p class="footer-text">This document is electronically generated and remains valid without a physical signature. Ref: {{ $tender->tender_number }}/{{ $tender->id }}</p>
        </div>
    </div>

</body>
</html>
