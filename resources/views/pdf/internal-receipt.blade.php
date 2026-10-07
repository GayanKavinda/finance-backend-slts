<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internal Receipt - {{ $invoice->receipt_number }}</title>
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

        .receipt-wrapper {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            position: relative;
        }

        /* Header */
        .header {
            padding: 30px 40px;
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
            margin-bottom: 10px;
        }

        .logo-icon {
            display: table-cell;
            width: 44px;
            height: 44px;
            vertical-align: middle;
            text-align: center;
            padding-top: 6px;
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
            margin-top: 6px;
        }

        .receipt-title {
            font-size: 28px;
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
            margin-bottom: 12px;
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
            width: 70px;
        }

        .meta-value {
            color: #1e293b;
            font-weight: 600;
        }

        .meta-value.highlight {
            color: #004A99;
        }

        /* Info Section */
        .info-section {
            padding: 25px 40px;
            border-bottom: 1px solid #e2e8f0;
            position: relative;
            z-index: 1;
        }

        .info-grid {
            display: table;
            width: 100%;
        }

        .info-col {
            display: table-cell;
            vertical-align: top;
            width: 50%;
        }

        .info-col:first-child {
            padding-right: 30px;
        }

        .section-heading {
            font-size: 9px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .info-name {
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .info-details {
            font-size: 10px;
            color: #475569;
            line-height: 1.6;
        }

        /* Payment Details */
        .payment-section {
            padding: 25px 40px;
            position: relative;
            z-index: 1;
        }

        .payment-box {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 20px;
        }

        .payment-box-title {
            font-size: 10px;
            font-weight: 700;
            color: #004A99;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        .payment-table {
            width: 100%;
        }

        .payment-table td {
            padding: 8px 0;
            font-size: 11px;
        }

        .payment-label {
            color: #64748b;
            width: 45%;
        }

        .payment-value {
            color: #1e293b;
            font-weight: 600;
            text-align: right;
        }

        .payment-amount-row {
            border-top: 2px solid #cbd5e1;
            margin-top: 8px;
            padding-top: 12px !important;
        }

        .payment-amount-row td {
            padding-top: 12px;
        }

        .payment-amount-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #1e293b;
        }

        .payment-amount-value {
            font-size: 18px;
            font-weight: 800;
            color: #004A99;
            text-align: right;
        }

        /* Footer */
        .footer {
            padding: 20px 40px;
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
            font-size: 10px;
            font-weight: 600;
            color: white;
            margin-bottom: 2px;
        }

        .footer-subtitle {
            font-size: 9px;
            color: #64748b;
        }

        .hash-code {
            display: inline-block;
            padding: 5px 8px;
            border: 1px solid #334155;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 8px;
            color: #94a3b8;
            margin-right: 12px;
        }

        .qr-placeholder {
            display: inline-block;
            width: 44px;
            height: 44px;
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
            font-size: 7px;
            color: #94a3b8;
            text-transform: uppercase;
            font-weight: 700;
            text-align: center;
        }

        .footer-legal {
            margin-top: 15px;
            padding-top: 12px;
            border-top: 1px solid #1e293b;
            text-align: center;
            font-size: 8px;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>

    <div class="receipt-wrapper">
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
                    <div class="receipt-title">RECEIPT</div>
                    <div class="official-badge">Official Document</div>
                    <div class="meta-row">
                        <span class="meta-label">Receipt No:</span>
                        <span class="meta-value highlight">{{ $invoice->receipt_number }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Date:</span>
                        <span class="meta-value">{{ $invoice->payment_received_date }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Billing Section -->
        <div class="info-section">
            <div class="info-grid">
                <div class="info-col">
                    <div class="section-heading">Received From</div>
                    <div class="info-name">{{ $invoice->customer->name }}</div>
                    <div class="info-details">
                        @if($invoice->billing_address)
                            {!! nl2br(e($invoice->billing_address)) !!}
                        @else
                            @if($invoice->customer->department)
                                {{ $invoice->customer->department }}<br>
                            @endif
                            @if($invoice->customer->address)
                                {{ $invoice->customer->address }}<br>
                            @endif
                        @endif
                    </div>
                </div>
                <div class="info-col">
                    <div class="section-heading">Related Invoice</div>
                    <div class="info-details">
                        <strong>Invoice:</strong> {{ $invoice->invoice_number }}<br>
                        <strong>PO Number:</strong> {{ $invoice->purchaseOrder->po_number ?? 'N/A' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Details -->
        <div class="payment-section">
            <div class="payment-box">
                <div class="payment-box-title">Payment Details</div>
                <table class="payment-table">
                    <tr>
                        <td class="payment-label">Cheque Number</td>
                        <td class="payment-value">{{ $invoice->cheque_number ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="payment-label">Issuing Bank</td>
                        <td class="payment-value">{{ $invoice->bank_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="payment-label">Payment Method</td>
                        <td class="payment-value">Cheque / Bank Transfer</td>
                    </tr>
                    <tr class="payment-amount-row">
                        <td class="payment-amount-label">Amount Received</td>
                        <td class="payment-amount-value">LKR {{ number_format($invoice->payment_amount, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-grid">
                <div class="footer-left">
                    <div class="footer-thanks">Thank you for your payment.</div>
                    <div class="footer-subtitle">Sri Lanka Telecom Services is a subsidiary of SLT-Mobitel Group.</div>
                </div>
                <div class="footer-right">
                    <span class="hash-code">HASH: {{ substr(md5($invoice->receipt_number), 0, 19) }}</span>
                    <div class="qr-placeholder">
                        <div class="qr-inner">Secure<br>QR</div>
                    </div>
                </div>
            </div>
            <div class="footer-legal">
                Authorized Signatory Not Required for Computer Generated Receipt
            </div>
        </div>
    </div>

</body>
</html>
