<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Tax Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        @page {
            margin: 15mm 15mm 15mm 15mm;
            size: A4 portrait;
        }

        @page :first {
            margin: 15mm 15mm 15mm 15mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "DejaVu Sans", "Helvetica Neue", Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #1e293b;
            background: #ffffff;
            margin: 0;
            padding: 0;
        }

        .page-container {
            padding: 0;
        }

        /* Top Brand Accent Bar */
        .top-brand-bar {
            height: 4px;
            background: #004A99;
            width: 100%;
            margin-bottom: 20px;
        }

        /* Header Table */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .header-table td {
            vertical-align: top;
        }

        .brand-title {
            font-size: 16px;
            font-weight: 800;
            color: #003366;
            letter-spacing: -0.2px;
            text-transform: uppercase;
        }

        .brand-subtitle {
            font-size: 9.5px;
            font-weight: 600;
            color: #64748b;
            margin-top: 2px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .brand-address {
            font-size: 9px;
            color: #475569;
            margin-top: 6px;
            line-height: 1.4;
        }

        .doc-title {
            text-align: right;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 0.8px;
            color: #003366;
            text-transform: uppercase;
        }

        .doc-badge {
            text-align: right;
            margin-top: 4px;
        }

        .badge-pill {
            display: inline-block;
            padding: 3px 9px;
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }

        /* Info Grid Table */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            page-break-inside: avoid;
        }

        .info-table td {
            padding: 12px 16px;
            vertical-align: top;
            width: 50%;
        }

        .block-label {
            font-size: 8.5px;
            font-weight: 700;
            color: #004A99;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 5px;
        }

        .client-name {
            font-size: 12.5px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 3px;
        }

        .client-text {
            font-size: 9.5px;
            color: #475569;
            line-height: 1.45;
        }

        .meta-line {
            font-size: 9.5px;
            margin-bottom: 4px;
        }

        .meta-line-label {
            display: inline-block;
            color: #64748b;
            width: 110px;
        }

        .meta-line-val {
            display: inline-block;
            color: #0f172a;
            font-weight: 700;
        }

        /* Items Ledger Table */
        .ledger-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .ledger-table th {
            background: #003366;
            color: #ffffff;
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            padding: 9px 12px;
            text-align: left;
        }

        .ledger-table th.tar,
        .ledger-table td.tar {
            text-align: right;
        }

        .ledger-table td {
            padding: 12px 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
            color: #1e293b;
            vertical-align: top;
        }

        .item-primary {
            font-weight: 700;
            color: #0f172a;
            font-size: 11px;
            margin-bottom: 3px;
        }

        .item-subtext {
            font-size: 9px;
            color: #64748b;
            line-height: 1.4;
        }

        /* Summary / Total Section */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
            page-break-inside: avoid;
        }

        .summary-table td {
            vertical-align: top;
        }

        .bank-details-box {
            width: 55%;
            padding-right: 20px;
        }

        .bank-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 12px 14px;
        }

        .bank-card-title {
            font-size: 8.5px;
            font-weight: 700;
            color: #004A99;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 6px;
        }

        .bank-card-text {
            font-size: 9px;
            color: #475569;
            line-height: 1.5;
        }

        .totals-box {
            width: 45%;
        }

        .totals-subtable {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-subtable td {
            padding: 5px 0;
            font-size: 10px;
        }

        .totals-label {
            color: #64748b;
            font-weight: 500;
        }

        .totals-val {
            text-align: right;
            font-weight: 700;
            color: #0f172a;
        }

        .grand-total-row td {
            padding-top: 10px;
            padding-bottom: 4px;
            border-top: 2px solid #003366;
            font-size: 12.5px;
            font-weight: 800;
            color: #003366;
        }

        /* Footer */
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            padding-top: 14px;
            border-top: 1px solid #e2e8f0;
            page-break-before: avoid;
        }

        .footer-table td {
            vertical-align: middle;
            font-size: 8.5px;
            color: #64748b;
        }

        .auth-notice {
            text-align: right;
            font-style: italic;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="top-brand-bar"></div>

        <!-- Header -->
        <table class="header-table">
            <tr>
                <td>
                    <div class="brand-title">Sri Lanka Telecom Services</div>
                    <div class="brand-subtitle">Commercial & Finance Division</div>
                    <div class="brand-address">
                        Lotus Road, P.O. Box 503, Colombo 01, Sri Lanka<br>
                        Tel: +94 11 232 9711 &nbsp;·&nbsp; VAT Reg: 114002847-7000
                    </div>
                </td>
                <td style="text-align: right;">
                    <div class="doc-title">TAX INVOICE</div>
                    <div class="doc-badge">
                        <span class="badge-pill">{{ $invoice->status }}</span>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Client & Metadata Grid -->
        <table class="info-table">
            <tr>
                <td>
                    <div class="block-label">Billed Recipient</div>
                    <div class="client-name">{{ $invoice->customer->name ?? 'Valued Customer' }}</div>
                    <div class="client-text">
                        @if($invoice->billing_address)
                            {!! nl2br(e($invoice->billing_address)) !!}
                        @elseif($invoice->customer && $invoice->customer->billing_address)
                            {!! nl2br(e($invoice->customer->billing_address)) !!}
                        @else
                            Official Client Address On Record
                        @endif
                        @if($invoice->customer && $invoice->customer->tax_number)
                            <br><strong>VAT/Tax ID:</strong> {{ $invoice->customer->tax_number }}
                        @endif
                    </div>
                </td>
                <td>
                    <div class="block-label">Invoice Specification</div>
                    <div class="meta-line">
                        <span class="meta-line-label">Invoice Reference:</span>
                        <span class="meta-line-val">{{ $invoice->invoice_number }}</span>
                    </div>
                    <div class="meta-line">
                        <span class="meta-line-label">Tax Invoice No:</span>
                        <span class="meta-line-val">{{ $taxInvoiceNumber ?? ('TAX-' . $invoice->invoice_number) }}</span>
                    </div>
                    <div class="meta-line">
                        <span class="meta-line-label">Issue Date:</span>
                        <span class="meta-line-val">{{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('d F Y') : now()->format('d F Y') }}</span>
                    </div>
                    <div class="meta-line">
                        <span class="meta-line-label">Purchase Order:</span>
                        <span class="meta-line-val">{{ $invoice->customer_po_number ?? ($invoice->purchaseOrder ? $invoice->purchaseOrder->po_number : 'N/A') }}</span>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Line Item Ledger -->
        <table class="ledger-table">
            <thead>
                <tr>
                    <th style="width: 55%;">Description / Scope of Work</th>
                    <th style="width: 20%;">PO Reference</th>
                    <th style="width: 25%;" class="tar">Amount (LKR)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="item-primary">
                            {{ $invoice->customer_po_description ?? 'Professional Telecommunication & Engineering Deliverables' }}
                        </div>
                        <div class="item-subtext">
                            Rendered under contract and procurement schedule specifications.
                        </div>
                    </td>
                    <td>
                        {{ $invoice->customer_po_number ?? ($invoice->purchaseOrder ? $invoice->purchaseOrder->po_number : 'Direct Assignment') }}
                    </td>
                    <td class="tar" style="font-weight: 700; font-size: 11px;">
                        {{ number_format($invoice->invoice_amount, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Totals & Payment Settlement Details -->
        <table class="summary-table">
            <tr>
                <td class="bank-details-box">
                    <div class="bank-card">
                        <div class="bank-card-title">Settlement Instructions</div>
                        <div class="bank-card-text">
                            Beneficiary: <strong>Sri Lanka Telecom Services Limited</strong><br>
                            Bank: Bank of Ceylon &nbsp;·&nbsp; Corporate Branch<br>
                            Account No: <strong>0001234567</strong><br>
                            Payment Reference: <strong>{{ $invoice->invoice_number }}</strong>
                        </div>
                    </div>
                </td>
                <td class="totals-box">
                    <table class="totals-subtable">
                        <tr>
                            <td class="totals-label">Subtotal (Net)</td>
                            <td class="totals-val">LKR {{ number_format($invoice->invoice_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="totals-label">Value Added Tax ({{ $taxPercentage ?? 18 }}%)</td>
                            <td class="totals-val">LKR {{ number_format($taxAmount ?? 0, 2) }}</td>
                        </tr>
                        <tr class="grand-total-row">
                            <td>Grand Total (LKR)</td>
                            <td class="tar">LKR {{ number_format($totalAmount ?? $invoice->invoice_amount, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Bottom Signature / System Authentication -->
        <table class="footer-table">
            <tr>
                <td>
                    This is an electronically validated commercial document generated via SLT ProcureX ERP.
                </td>
                <td class="auth-notice">
                    Authentication Hash: {{ strtoupper(substr(md5($invoice->invoice_number . $invoice->id), 0, 16)) }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
