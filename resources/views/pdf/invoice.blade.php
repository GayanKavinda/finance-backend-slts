<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
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
            font-size: 13px;
            line-height: 1.5;
            color: #1e293b;
            background: #f8fafc;
            padding: 0;
            margin: 0;
        }

        .invoice-wrapper {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            position: relative;
        }

        /* Watermark */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            opacity: 0.03;
            font-size: 180px;
            font-weight: 900;
            color: #004A99;
            pointer-events: none;
            z-index: 0;
            user-select: none;
        }

        /* Header Section */
        .header {
            padding: 40px 40px 30px 40px;
            border-bottom: 1px solid #e2e8f0;
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

        .company-branding {
            margin-bottom: 15px;
        }

        .logo-row {
            display: table;
            margin-bottom: 12px;
        }

        .logo-icon {
            display: table-cell;
            width: 48px;
            height: 48px;
            background: #004A99;
            border-radius: 8px;
            vertical-align: middle;
            text-align: center;
            padding-top: 8px;
        }

        .logo-icon svg {
            width: 32px;
            height: 32px;
            fill: white;
        }

        .logo-text {
            display: table-cell;
            vertical-align: middle;
            padding-left: 12px;
        }

        .company-name {
            font-size: 20px;
            font-weight: 800;
            color: #003366;
            text-transform: uppercase;
            letter-spacing: -0.5px;
            line-height: 1.2;
        }

        .company-tagline {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 600;
            margin-top: 2px;
        }

        .company-address {
            font-size: 11px;
            color: #475569;
            line-height: 1.6;
            max-width: 280px;
        }

        .company-address .label {
            color: #94a3b8;
        }

        .invoice-title {
            font-size: 36px;
            font-weight: 300;
            color: #334155;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .official-badge {
            display: inline-block;
            background: #004A99;
            color: white;
            font-size: 9px;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
        }

        .invoice-meta-grid {
            font-size: 11px;
        }

        .meta-row {
            margin-bottom: 6px;
        }

        .meta-label {
            color: #94a3b8;
            text-transform: uppercase;
            font-weight: 700;
            font-size: 9px;
            display: inline-block;
            width: 80px;
        }

        .meta-value {
            color: #1e293b;
            font-weight: 600;
        }

        .meta-value.due-date {
            color: #2563eb;
        }

        /* Billing Section */
        .billing-section {
            padding: 30px 40px;
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
            margin-bottom: 12px;
        }

        .billed-to-name {
            font-size: 15px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .billed-to-details {
            font-size: 11px;
            color: #475569;
            line-height: 1.6;
        }

        .service-details {
            font-size: 11px;
            color: #475569;
            line-height: 1.8;
        }

        .service-label {
            font-weight: 600;
            color: #1e293b;
        }

        /* Items Table */
        .items-section {
            padding: 30px 40px;
            position: relative;
            z-index: 1;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .items-table thead tr {
            border-bottom: 2px solid #1e293b;
        }

        .items-table th {
            padding: 12px 8px;
            text-align: left;
            font-size: 10px;
            font-weight: 700;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .items-table th.text-center {
            text-align: center;
        }

        .items-table th.text-right {
            text-align: right;
        }

        .items-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
        }

        .items-table td {
            padding: 20px 8px;
            vertical-align: top;
        }

        .item-name {
            font-weight: 600;
            color: #1e293b;
            font-size: 13px;
            margin-bottom: 3px;
        }

        .item-description {
            font-size: 10px;
            color: #64748b;
            line-height: 1.5;
            max-width: 400px;
        }

        .po-number {
            font-family: 'Courier New', monospace;
            color: #475569;
            font-style: italic;
            text-align: center;
            font-size: 12px;
        }

        .item-amount {
            text-align: right;
            font-weight: 600;
            color: #1e293b;
            font-size: 13px;
        }

        /* Summary Section */
        .summary-section {
            padding: 30px 40px;
            background: white;
            position: relative;
            z-index: 1;
        }

        .summary-grid {
            display: table;
            width: 100%;
        }

        .summary-left {
            display: table-cell;
            width: 50%;
            vertical-align: bottom;
            padding-right: 30px;
        }

        .summary-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .payment-info-box {
            background: #e6f0fa;
            border: 1px solid #bfdbf7;
            border-radius: 6px;
            padding: 18px;
        }

        .payment-info-title {
            font-size: 10px;
            font-weight: 700;
            color: #004A99;
            text-transform: uppercase;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        .payment-info-text {
            font-size: 10px;
            color: #475569;
            line-height: 1.6;
            font-style: italic;
        }

        .totals-table {
            width: 100%;
        }

        .totals-table tr {
            border: none;
        }

        .totals-table td {
            padding: 8px 0;
            font-size: 12px;
        }

        .totals-label {
            color: #64748b;
        }

        .totals-value {
            text-align: right;
            font-weight: 600;
            color: #1e293b;
        }

        .total-row {
            border-top: 1px solid #cbd5e1;
            padding-top: 12px !important;
        }

        .total-row td {
            padding-top: 12px;
        }

        .grand-total-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #1e293b;
        }

        .grand-total-currency {
            font-size: 10px;
            font-weight: 700;
            color: #1e293b;
        }

        .grand-total-amount {
            font-size: 24px;
            font-weight: 900;
            color: #1e293b;
        }

        /* Footer */
        .footer {
            padding: 30px 40px;
            background: #0f172a;
            color: #94a3b8;
            position: relative;
            z-index: 1;
        }

        .footer-grid {
            display: table;
            width: 100%;
        }

        .footer-left {
            display: table-cell;
            width: 60%;
            vertical-align: middle;
        }

        .footer-right {
            display: table-cell;
            width: 40%;
            text-align: right;
            vertical-align: middle;
        }

        .footer-thanks {
            font-size: 11px;
            font-weight: 600;
            color: white;
            margin-bottom: 3px;
        }

        .footer-subtitle {
            font-size: 9px;
            color: #64748b;
        }

        .hash-code {
            display: inline-block;
            padding: 6px 10px;
            border: 1px solid #334155;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 9px;
            color: #94a3b8;
            margin-right: 15px;
        }

        .qr-placeholder {
            display: inline-block;
            width: 60px;
            height: 60px;
            background: white;
            padding: 2px;
            border-radius: 3px;
            vertical-align: middle;
        }

        .qr-inner {
            width: 100%;
            height: 100%;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            color: #94a3b8;
            text-transform: uppercase;
            font-weight: 700;
            text-align: center;
        }

        .footer-legal {
            margin-top: 25px;
            padding-top: 18px;
            border-top: 1px solid #1e293b;
            text-align: center;
            font-size: 9px;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
    </style>
</head>
<body>

<div class="invoice-wrapper">
    <!-- Watermark -->
    <div class="watermark">PAID</div>

    <!-- Header Section -->
    <div class="header">
        <div class="header-content">
            <div class="header-left">
                <div class="company-branding">
                    <div class="logo-row">
                        <div class="logo-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="logo-text">
                            <div class="company-name">{{ $company['name'] }}</div>
                            <div class="company-tagline">Connectivity & Infrastructure Excellence</div>
                        </div>
                    </div>
                </div>
                <div class="company-address">
                    {{ $company['address'] }}<br>
                    <span class="label">T:</span> +94 11 232 9711<br>
                    <span class="label">E:</span> billing@sltservices.lk
                </div>
            </div>
            <div class="header-right">
                <div class="invoice-title">INVOICE</div>
                <div class="official-badge">Official Electronic Document</div>
                <div class="invoice-meta-grid">
                    <div class="meta-row">
                        <span class="meta-label">Invoice No:</span>
                        <span class="meta-value">{{ $invoice->invoice_number }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Date:</span>
                        <span class="meta-value">{{ $invoice->created_at->format('F d, Y') }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Due Date:</span>
                        <span class="meta-value due-date">{{ $invoice->created_at->addDays(14)->format('F d, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Billing Section -->
    <div class="billing-section">
        <div class="billing-grid">
            <div class="billing-col">
                <div class="section-heading">Billed To</div>
                <div class="billed-to-name">{{ $invoice->customer->name }}</div>
                <div class="billed-to-details">
                    @if($invoice->customer->department)
                        {{ $invoice->customer->department }}<br>
                    @endif
                    @if($invoice->customer->address)
                        {{ $invoice->customer->address }}<br>
                    @endif
                    @if($invoice->customer->email)
                        {{ $invoice->customer->email }}<br>
                    @endif
                    @if($invoice->customer->vat_number)
                        <span class="service-label">VAT Reg:</span> {{ $invoice->customer->vat_number }}
                    @endif
                </div>
            </div>
            <div class="billing-col">
                <div class="section-heading">Service Details</div>
                <div class="service-details">
                    @if($invoice->project_name)
                        <span class="service-label">Project:</span> {{ $invoice->project_name }}<br>
                    @endif
                    @if($invoice->purchaseOrder)
                        <span class="service-label">Contract Ref:</span> {{ $invoice->purchaseOrder->contract_reference ?? 'N/A' }}<br>
                    @endif
                    <span class="service-label">Currency:</span> LKR (Sri Lankan Rupee)
                </div>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="items-section">
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Description</th>
                    <th class="text-center">PO Number</th>
                    <th class="text-right">Amount (LKR)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="item-name">{{ $invoice->service_description ?? 'Professional Services' }}</div>
                        <div class="item-description">
                            {{ $invoice->service_details ?? 'Consulting and implementation services for the current billing period.' }}
                        </div>
                    </td>
                    <td class="po-number">
                        {{ $invoice->purchaseOrder->po_number ?? 'N/A' }}
                    </td>
                    <td class="item-amount">
                        {{ number_format($invoice->invoice_amount, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Summary Section -->
    <div class="summary-section">
        <div class="summary-grid">
            <div class="summary-left">
                <div class="payment-info-box">
                    <div class="payment-info-title">Payment Information</div>
                    <div class="payment-info-text">
                        Please include the invoice number ({{ $invoice->invoice_number }}) as a reference in all transfers. 
                        Payments are accepted via RTGS or Online Banking to Bank of Ceylon, 
                        Corporate Branch A/C 0001234567.
                    </div>
                </div>
            </div>
            <div class="summary-right">
                <table class="totals-table">
                    <tr>
                        <td class="totals-label">Subtotal</td>
                        <td class="totals-value">{{ number_format($invoice->invoice_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="totals-label">VAT ({{ optional($invoice->taxInvoice)->tax_percentage ?? 0 }}%)</td>
                        <td class="totals-value">{{ number_format(optional($invoice->taxInvoice)->tax_amount ?? 0, 2) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td class="grand-total-label">Grand Total</td>
                        <td class="totals-value">
                            <span class="grand-total-currency">LKR</span>
                            <span class="grand-total-amount">{{ number_format($invoice->total_amount, 2) }}</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-grid">
            <div class="footer-left">
                <div class="footer-thanks">Thank you for your continued business.</div>
                <div class="footer-subtitle">{{ $company['name'] }} is a subsidiary of SLT-Mobitel Group.</div>
            </div>
            <div class="footer-right">
                <span class="hash-code">HASH: {{ substr(md5($invoice->invoice_number), 0, 19) }}</span>
                <div class="qr-placeholder">
                    <div class="qr-inner">Secure<br>QR</div>
                </div>
            </div>
        </div>
        <div class="footer-legal">
            Authorized Signatory Not Required for Computer Generated Invoice
        </div>
    </div>
</div>

</body>
</html>