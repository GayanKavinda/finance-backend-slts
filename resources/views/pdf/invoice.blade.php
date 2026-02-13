<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        /* Modern Clean Aesthetic */
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 13px;
            line-height: 1.4;
            color: #334155;
            margin: 0;
            padding: 0;
        }

        .invoice-container {
            max-width: 800px;
            margin: auto;
            padding: 30px;
        }

        /* Header Section */
        .header-table {
            width: 100%;
            margin-bottom: 40px;
            border-spacing: 0;
        }

        .header-table td {
            vertical-align: top;
        }

        .brand-section {
            width: 50%;
        }

        .logo {
            height: 45px;
            margin-bottom: 10px;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #1e293b;
            margin: 0;
        }

        .company-details {
            font-size: 11px;
            color: #64748b;
            line-height: 1.5;
        }

        .invoice-details {
            text-align: right;
            width: 50%;
        }

        .invoice-label {
            font-size: 24px;
            font-weight: 800;
            color: #2563eb; /* Modern Blue */
            text-transform: uppercase;
            margin: 0;
            letter-spacing: 1px;
        }

        .invoice-meta {
            font-size: 12px;
            margin-top: 5px;
            color: #475569;
        }

        /* Billing Section */
        .billing-table {
            width: 100%;
            margin-bottom: 40px;
        }

        .billing-title {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: bold;
            color: #94a3b8;
            margin-bottom: 5px;
        }

        .billing-info {
            font-size: 13px;
            color: #1e293b;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table th {
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            padding: 12px 10px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            color: #64748b;
        }

        .items-table td {
            padding: 15px 10px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .item-desc {
            font-weight: 600;
            color: #1e293b;
            display: block;
        }

        .item-subtext {
            font-size: 11px;
            color: #64748b;
        }

        /* Calculations */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-wrapper {
            width: 40%;
            float: right;
        }

        .summary-row td {
            padding: 5px 0;
            font-size: 13px;
        }

        .summary-label {
            color: #64748b;
            text-align: left;
        }

        .summary-value {
            text-align: right;
            font-weight: 600;
            color: #1e293b;
        }

        .total-row td {
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
        }

        .total-amount {
            font-size: 18px;
            color: #2563eb;
            font-weight: 800;
        }

        /* Footer */
        .footer {
            margin-top: 100px;
            padding-top: 20px;
            border-top: 1px solid #f1f5f9;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            background: #dcfce7;
            color: #15803d;
            margin-bottom: 10px;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>

<div class="invoice-container">
    <!-- Header -->
    <table class="header-table">
        <tr>
            <td class="brand-section">
                @if($company['logo'])
                    <img src="{{ $company['logo'] }}" class="logo" />
                @endif
                <p class="company-name">{{ $company['name'] }}</p>
                <div class="company-details">
                    {{ $company['division'] }}<br>
                    {{ $company['address'] }}
                </div>
            </td>
            <td class="invoice-details">
                <div class="status-badge">Official Invoice</div>
                <h1 class="invoice-label">Invoice</h1>
                <div class="invoice-meta">
                    <strong>No:</strong> #{{ $invoice->invoice_number }}<br>
                    <strong>Date:</strong> {{ $invoice->created_at->format('M d, Y') }}<br>
                    <strong>Due:</strong> {{ $invoice->created_at->addDays(14)->format('M d, Y') }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Billing Info -->
    <table class="billing-table">
        <tr>
            <td width="50%">
                <div class="billing-title">Billed To</div>
                <div class="billing-info">
                    <strong>{{ $invoice->customer->name }}</strong><br>
                    {{ $invoice->customer->email ?? '' }}<br>
                    {{ $invoice->customer->address ?? '' }}
                </div>
            </td>
            <td width="50%">
                <!-- Optional: Add Payment Method or Shipping Info here -->
            </td>
        </tr>
    </table>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th>Description</th>
                <th>PO Number</th>
                <th style="text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <span class="item-desc">Project Services</span>
                    <span class="item-subtext">Consulting and implementation for the current quarter.</span>
                </td>
                <td>{{ $invoice->purchaseOrder->po_number ?? 'N/A' }}</td>
                <td style="text-align: right; font-weight: 600;">LKR {{ number_format($invoice->invoice_amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Calculations -->
    <div class="clearfix">
        <div class="summary-wrapper">
            <table class="summary-table">
                <tr class="summary-row">
                    <td class="summary-label">Subtotal</td>
                    <td class="summary-value">LKR {{ number_format($invoice->invoice_amount, 2) }}</td>
                </tr>
                <tr class="summary-row">
                    <td class="summary-label">Tax ({{ optional($invoice->taxInvoice)->tax_percentage ?? 0 }}%)</td>
                    <td class="summary-value">LKR {{ number_format(optional($invoice->taxInvoice)->tax_amount ?? 0, 2) }}</td>
                </tr>
                <tr class="summary-row total-row">
                    <td class="summary-label"><strong>Total Amount</strong></td>
                    <td class="summary-value total-amount">LKR {{ number_format($invoice->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Thank you for your business!</p>
        <p>This is a system-generated document. Payment is due within 14 days of receipt.</p>
        <p><strong>{{ $company['name'] }}</strong> • {{ $company['address'] }}</p>
    </div>
</div>

</body>
</html>