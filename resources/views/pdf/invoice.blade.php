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
            size: A4 portrait;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 11px;
            line-height: 1.45;
            color: #0f172a;
            background: #ffffff;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }

        .page-container {
            width: 100%;
            height: 100%;
            min-height: 297mm;
            padding: 36px 42px;
            background: #ffffff;
            position: relative;
        }

        /* Top Brand Strip */
        .top-brand-bar {
            height: 4px;
            background: #0f172a;
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
        }

        /* Header Section */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 28px;
        }

        .header-table td {
            vertical-align: top;
        }

        .brand-title {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.2px;
            text-transform: uppercase;
        }

        .brand-subtitle {
            font-size: 10px;
            font-weight: 500;
            color: #64748b;
            margin-top: 1px;
        }

        .brand-address {
            font-size: 9.5px;
            color: #475569;
            margin-top: 6px;
            line-height: 1.4;
        }

        .doc-title {
            text-align: right;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #0f172a;
            text-transform: uppercase;
        }

        .doc-badge {
            text-align: right;
            margin-top: 2px;
        }

        .badge-pill {
            display: inline-block;
            padding: 2px 7px;
            font-size: 8.5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 4px;
            background: #f1f5f9;
            color: #334155;
            border: 1px solid #e2e8f0;
        }

        /* Metadata & Customer Block */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }

        .info-table td {
            padding: 14px 16px;
            vertical-align: top;
            width: 50%;
        }

        .block-label {
            font-size: 8.5px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 4px;
        }

        .client-name {
            font-size: 12px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 3px;
        }

        .client-text {
            font-size: 9.5px;
            color: #475569;
            line-height: 1.4;
        }

        .meta-line {
            font-size: 9.5px;
            margin-bottom: 3px;
            display: table;
            width: 100%;
        }

        .meta-line-label {
            display: table-cell;
            color: #64748b;
            width: 95px;
        }

        .meta-line-val {
            display: table-cell;
            color: #0f172a;
            font-weight: 600;
        }

        /* Items Ledger Table */
        .ledger-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        .ledger-table th {
            background: #0f172a;
            color: #ffffff;
            font-size: 8.5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            padding: 8px 10px;
            text-align: left;
        }

        .ledger-table th.tar,
        .ledger-table td.tar {
            text-align: right;
        }

        .ledger-table td {
            padding: 10px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
            color: #1e293b;
        }

        .item-primary {
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .item-subtext {
            font-size: 8.5px;
            color: #64748b;
        }

        /* Summary / Total Section */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 28px;
        }

        .summary-table td {
            vertical-align: top;
        }

        .bank-details-box {
            width: 58%;
            padding-right: 20px;
        }

        .bank-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 14px;
        }

        .bank-card-title {
            font-size: 8.5px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 5px;
        }

        .bank-card-text {
            font-size: 9px;
            color: #475569;
            line-height: 1.45;
        }

        .totals-box {
            width: 42%;
        }

        .totals-subtable {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-subtable td {
            padding: 4px 0;
            font-size: 9.5px;
        }

        .totals-label {
            color: #64748b;
        }

        .totals-val {
            text-align: right;
            font-weight: 600;
            color: #0f172a;
        }

        .grand-total-row td {
            padding-top: 8px;
            border-top: 1px solid #0f172a;
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
        }

        /* Footer */
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px solid #f1f5f9;
        }

        .footer-table td {
            vertical-align: middle;
            font-size: 8.5px;
            color: #94a3b8;
        }

        .auth-notice {
            text-align: right;
            font-style: italic;
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
                    <th style="width: 58%;">Description / Scope of Work</th>
                    <th style="width: 17%;">PO Reference</th>
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
                    <td class="tar font-medium">
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
                            Reference: <strong>{{ $invoice->invoice_number }}</strong>
                        </div>
                    </div>
                </td>
                <td class="totals-box">
                    <table class="totals-subtable">
                        <tr>
                            <td class="totals-label">Subtotal (Net)</td>
                            <td class="totals-val">{{ number_format($invoice->invoice_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="totals-label">Value Added Tax ({{ $taxPercentage ?? 18 }}%)</td>
                            <td class="totals-val">{{ number_format($taxAmount ?? 0, 2) }}</td>
                        </tr>
                        <tr class="grand-total-row">
                            <td>Grand Total (LKR)</td>
                            <td class="tar">{{ number_format($totalAmount ?? $invoice->invoice_amount, 2) }}</td>
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
