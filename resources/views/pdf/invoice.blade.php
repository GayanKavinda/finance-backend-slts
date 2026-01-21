<!-- resources/views/pdf/invoice.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        .box {
            border: 1px solid #ddd;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background: #f3f4f6;
            text-align: left;
        }

        .right {
            text-align: right;
        }

        .status {
            padding: 6px 10px;
            background: #e0f2fe;
            color: #0369a1;
            border-radius: 4px;
            font-weight: bold;
            display: inline-block;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 11px;
            color: #777;
        }
    </style>
</head>
<body>

<table>
    <tr>
        <td>
            <img src="{{ public_path('icons/slt_digital_icon.png') }}" height="45">
            <p>
                Sri Lanka Telecom Services<br>
                Finance Division<br>
                Colombo, Sri Lanka
            </p>
        </td>
        <td class="right">
            <h2>INVOICE</h2>
            <span class="status">{{ $invoice->status }}</span>
        </td>
    </tr>
</table>

<div class="box">
    <p><strong>Invoice No:</strong> {{ $invoice->invoice_number }}</p>
    <p><strong>Date:</strong> {{ $invoice->invoice_date->format('Y-m-d') }}</p>
    <p><strong>Customer:</strong> {{ $invoice->customer->name }}</p>
    <p><strong>Address:</strong> {{ $invoice->customer->billing_address }}</p>

    <table>
        <thead>
        <tr>
            <th>Description</th>
            <th class="right">Amount (LKR)</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Purchase Order {{ $invoice->purchaseOrder->po_number }}</td>
            <td class="right">{{ number_format($invoice->invoice_amount, 2) }}</td>
        </tr>
        </tbody>
    </table>

    <table style="margin-top:20px">
        <tr>
            <td>Subtotal</td>
            <td class="right">{{ number_format($invoice->invoice_amount, 2) }}</td>
        </tr>
        <tr>
            <td>Tax ({{ $invoice->taxInvoice->tax_percentage }}%)</td>
            <td class="right">{{ number_format($invoice->taxInvoice->tax_amount, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Total</strong></td>
            <td class="right"><strong>{{ number_format($invoice->taxInvoice->total_amount, 2) }}</strong></td>
        </tr>
    </table>
</div>

<div class="footer">
    This is a system generated invoice
</div>

</body>
</html>
