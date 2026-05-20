<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Award Confirmation</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            padding: 40px;
            color: #2c3e50;
            line-height: 1.6;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 3px solid #1abc9c;
            padding-bottom: 20px;
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #16a085;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }

        .company-info {
            font-size: 11px;
            color: #7f8c8d;
        }

        .title {
            font-size: 22px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 30px;
            text-align: center;
            color: #2c3e50;
        }

        .date {
            text-align: right;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .section {
            margin-bottom: 25px;
        }

        .label {
            font-weight: bold;
            color: #34495e;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: #fdfdfd;
        }

        .details-table td {
            padding: 12px;
            border: 1px solid #ecf0f1;
        }

        .amount-box {
            background: #f1f8f7;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
            text-align: center;
        }

        .amount-value {
            font-size: 20px;
            font-weight: bold;
            color: #16a085;
        }

        .signature-area {
            margin-top: 60px;
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
        }

        .footer {
            position: fixed;
            bottom: 40px;
            left: 40px;
            right: 40px;
            border-top: 1px solid #ecf0f1;
            padding-top: 15px;
            font-size: 10px;
            color: #bdc3c7;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="logo">FINANCEPRO TENDER SYSTEMS</div>
        <div class="company-info">
            Strategic Procurement Solutions & Financial Management<br>
            No. 45, Enterprise Square, Colombo 01 | +94 11 999 8888 | contact@financepro.lk
        </div>
    </div>

    <div class="date">Date: {{ date('d M Y') }}</div>

    <div class="title">Tender Award Confirmation</div>

    <div class="section">
        <p>To,</p>
        <p><strong>{{ $tender->customer->name }}</strong><br>
            {{ $tender->customer->address ?? 'Customer Billing Address' }}
        </p>
    </div>

    <div class="section">
        <p>Dear Valued Partner,</p>
        <p>We are formally acknowledging the award of the contract for the project referenced below. We appreciate the opportunity to work with you and are committed to delivering excellence throughout the project duration.</p>
    </div>

    <table class="details-table">
        <tr>
            <td class="label" width="30%">Tender Number:</td>
            <td>{{ $tender->tender_number }}</td>
        </tr>
        <tr>
            <td class="label">Project Name:</td>
            <td>{{ $tender->name }}</td>
        </tr>
        <tr>
            <td class="label">Project Duration:</td>
            <td>{{ \Carbon\Carbon::parse($tender->start_date)->format('d M Y') }} to {{ $tender->end_date ? \Carbon\Carbon::parse($tender->end_date)->format('d M Y') : 'Completion' }}</td>
        </tr>
    </table>

    <div class="section">
        <p>The total awarded value for this contract, as per the finalized agreement, is:</p>
        <div class="amount-box">
            <div class="amount-value">LKR {{ number_format($tender->awarded_amount, 2) }}</div>
            <div style="font-size: 10px; color: #7f8c8d; margin-top: 5px;">(Inclusive of all applicable taxes and levies)</div>
        </div>
    </div>

    <div class="section">
        <p>Our team is currently preparing the necessary project execution plans and mobilizing resources. A formal Tax Invoice for the awarded amount will be submitted to your finance division as per the agreed schedule.</p>
        <p>Should you have any immediate clarifications, please do not hesitate to contact our project management office.</p>
    </div>

    <div class="signature-area">
        <div class="signature-box">
            <p>..........................................</p>
            <p><strong>Director - Procurement</strong><br>FinancePro Tender Systems</p>
        </div>
        <div class="signature-box" style="text-align: right;">
            <p>..........................................</p>
            <p><strong>Accepted By</strong><br>{{ $tender->customer->name }}</p>
        </div>
    </div>

    <div class="footer">
        <p>This document is electronically generated and remains valid without a physical signature. Ref: {{ $tender->tender_number }}/{{ $tender->id }}</p>
    </div>
</body>

</html>
