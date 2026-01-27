<!-- finance-backend/resources/views/pdf/invoice.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #0f172a;
        }

        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .company {
            font-size: 14px;
            font-weight: bold;
        }

        .company small {
            display: block;
            font-size: 11px;
            color: #64748b;
        }

        .invoice-box {
            border: 1px solid #e5e7eb;
            padding: 20px;
            border-radius: 8px;
        }

        .meta {
            margin-bottom: 20px;
        }

        .meta div {
            margin-bottom: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table thead {
            background: #f1f5f9;
        }

        table th,
        table td {
            padding: 10px;
            border: 1px solid #e5e7eb;
            text-align: left;
        }

        .total {
            margin-top: 20px;
            text-align: right;
        }

        .total strong {
            font-size: 14px;
        }

        .footer {
            margin-top: 40px;
            font-size: 10px;
            color: #64748b;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="header">
    <div>
        <img src="{{ $company['logo'] }}" height="40" />
        <div class="company">
            {{ $company['name'] }}
            <small>{{ $company['division'] }}</small>
            <small>{{ $company['address'] }}</small>
        </div>
    </div>

    <div>
        <strong>INVOICE</strong><br>
        #{{ $invoice->invoice_number }}<br>
        Date: {{ $invoice->created_at->format('Y-m-d') }}
    </div>
</div>

<div class="invoice-box">
    <div class="meta">
        <div><strong>Billed To:</strong></div>
        <div>{{ $invoice->customer->name }}</div>
        <div>{{ $invoice->customer->email ?? '' }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>PO Number</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Project Services</td>
                <td>{{ $invoice->purchaseOrder->po_number ?? '-' }}</td>
                <td>LKR {{ number_format($invoice->invoice_amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="total">
        <p>Subtotal: LKR {{ number_format($invoice->invoice_amount, 2) }}</p>
        <p>
            Tax: LKR {{ number_format(optional($invoice->taxInvoice)->tax_amount ?? 0, 2) }}
        </p>
        <strong>Total: LKR {{ number_format($invoice->total_amount, 2) }}</strong>
    </div>
</div>

<div class="footer">
    This is a system generated invoice. No signature required.
</div>

</body>
</html>
