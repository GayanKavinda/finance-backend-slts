<!DOCTYPE html>
<html>

<head>
    <title>Purchase Order - {{ $po->po_number }}</title>
    <style>
        body {
            font-family: sans-serif;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .details {
            margin-bottom: 20px;
        }

        .footer {
            margin-top: 50px;
            font-size: 12px;
            text-align: center;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        .amount {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>PURCHASE ORDER</h1>
        <p>PO Number: {{ $po->po_number }}</p>
    </div>

    <div class="details">
        <div style="float: left; width: 50%;">
            <h3>Bill To:</h3>
            <p>{{ config('app.name') }}<br>Finance Department</p>
        </div>
        <div style="float: right; width: 50%;">
            <h3>To:</h3>
            <p>{{ $po->customer->name }}<br>{{ $po->customer->billing_address }}</p>
        </div>
        <div style="clear: both;"></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Reference</th>
                <th>Description</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Tender: {{ $po->job->tender->tender_number }}</td>
                <td>{{ $po->po_description ?: 'General Services' }}</td>
                <td class="amount">${{ number_format($po->po_amount, 2) }}</td>
            </tr>
            <tr>
                <td>Job: {{ $po->job->name }}</td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" style="text-align: right;"><strong>Total:</strong></td>
                <td class="amount">${{ number_format($po->po_amount, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="details" style="margin-top: 30px;">
        <p><strong>Note:</strong> Please reference the PO number on all invoices.</p>
    </div>

    <div class="footer">
        <p>Generated on {{ date('Y-m-d H:i:s') }}</p>
    </div>
</body>

</html>