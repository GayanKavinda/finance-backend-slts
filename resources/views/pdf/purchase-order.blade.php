<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order - {{ $po->po_number }}</title>
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
            line-height: 1.5;
            color: #1e293b;
            background: #f8fafc;
            padding: 0;
            margin: 0;
        }

        .po-wrapper {
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

        .header-content {
            display: table;
            width: 100%;
        }

        .header-left,
        .header-right {
            display: table-cell;
            vertical-align: top;
        }

        .header-left {
            width: 55%;
        }

        .header-right {
            width: 45%;
            text-align: right;
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

        .po-title {
            font-size: 24px;
            font-weight: 300;
            color: #334155;
            margin-bottom: 6px;
            letter-spacing: -0.3px;
        }

        .official-badge {
            display: inline-block;
            background: #004A99;
            color: white;
            font-size: 9px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }

        .meta-row {
            margin-bottom: 5px;
            font-size: 10px;
        }

        .meta-label {
            color: #94a3b8;
            text-transform: uppercase;
            font-weight: 700;
            font-size: 9px;
            display: inline-block;
            width: 65px;
        }

        .meta-value {
            color: #1e293b;
            font-weight: 600;
        }

        /* Billing Section */
        .billing-section {
            padding: 20px 40px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            position: relative;
            z-index: 1;
        }

        .billing-grid {
            display: table;
            width: 100%;
        }

        .billing-col {
            display: table-cell;
            vertical-align: top;
            width: 50%;
        }

        .billing-col:first-child {
            padding-right: 30px;
        }

        .section-heading {
            font-size: 9px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }

        .billing-name {
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .billing-details {
            font-size: 10px;
            color: #475569;
            line-height: 1.6;
        }

        /* PO Details */
        .po-details {
            padding: 20px 40px;
            position: relative;
            z-index: 1;
        }

        .po-table {
            width: 100%;
            border-collapse: collapse;
        }

        .po-table thead tr {
            border-bottom: 2px solid #1e293b;
        }

        .po-table th {
            padding: 10px 8px;
            text-align: left;
            font-size: 10px;
            font-weight: 700;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .po-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
        }

        .po-table td {
            padding: 14px 8px;
            vertical-align: top;
            font-size: 11px;
        }

        .po-label {
            font-weight: 600;
            color: #334155;
        }

        /* Footer */
        .footer {
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

    <div class="po-wrapper">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="header-left">
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
                        Colombo, Sri Lanka<br>
                        T: +94 11 232 9711<br>
                        E: billing@sltservices.lk
                    </div>
                </div>
                <div class="header-right">
                    <div class="po-title">PURCHASE ORDER</div>
                    <div class="official-badge">Official Document</div>
                    <div class="meta-row">
                        <span class="meta-label">PO No:</span>
                        <span class="meta-value">{{ $po->po_number }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Billing Section -->
        <div class="billing-section">
            <div class="billing-grid">
                <div class="billing-col">
                    <div class="section-heading">Bill To</div>
                    <div class="billing-name">{{ config('app.name') }}</div>
                    <div class="billing-details">
                        Finance Department<br>
                        Colombo, Sri Lanka
                    </div>
                </div>
                <div class="billing-col">
                    <div class="section-heading">Vendor</div>
                    <div class="billing-name">{{ $po->customer->name }}</div>
                    <div class="billing-details">
                        {{ $po->customer->billing_address ?? 'Address on file' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- PO Details -->
        <div class="po-details">
            <table class="po-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Reference</th>
                        <th style="width: 50%;">Description</th>
                        <th style="width: 25%; text-align: right;">Amount (LKR)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="po-label">Tender: {{ $po->job->tender->tender_number }}</td>
                        <td>{{ $po->po_description ?: 'General Services' }}</td>
                        <td style="text-align: right; font-weight: 600;">{{ number_format($po->po_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="po-label">Job: {{ $po->job->name }}</td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr style="border-top: 2px solid #1e293b;">
                        <td colspan="2" style="text-align: right; padding-top: 12px;"><strong>Total:</strong></td>
                        <td style="text-align: right; padding-top: 12px; font-weight: 700; font-size: 13px;">{{ number_format($po->po_amount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p class="footer-text">Please reference the PO number on all invoices. Generated on {{ date('Y-m-d H:i:s') }}</p>
        </div>
    </div>

</body>
</html>
