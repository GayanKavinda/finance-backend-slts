<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Letter of Award</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 40px;
            color: #333;
            line-height: 1.6;
        }

        .header {
            text-align: center;
            margin-bottom: 50px;
            border-bottom: 2px solid #004a99;
            padding-bottom: 20px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #004a99;
            margin-bottom: 10px;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 30px;
            text-align: center;
        }

        .date {
            text-align: right;
            margin-bottom: 30px;
        }

        .section {
            margin-bottom: 20px;
        }

        .label {
            font-weight: bold;
        }

        .footer {
            margin-top: 100px;
            border-top: 1px solid #ccc;
            padding-top: 20px;
            font-size: 12px;
        }

        .signature-area {
            margin-top: 50px;
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="logo">FINANCEPRO CORP (PVT) LTD</div>
        <div>No. 123, Business Plaza, Colombo 03, Sri Lanka</div>
        <div>Tel: +94 11 2345678 | Email: procurement@financepro.lk</div>
    </div>

    <div class="date">Date: {{ date('d M Y') }}</div>

    <div class="title">LETTER OF AWARD / AGREEMENT OF WORKS</div>

    <div class="section">
        <p>To,</p>
        <p><strong>{{ $job->selectedContractor->name }}</strong><br>
            {{ $job->selectedContractor->address ?? 'Contractor Address' }}
        </p>
    </div>

    <div class="section">
        <p>Dear Sir/Madam,</p>
        <p>We are pleased to inform you that following the evaluation of quotations for the project <strong>"{{ $job->tender->tender_name }}"</strong>, your firm has been selected as the successful contractor for the following job:</p>
    </div>

    <div class="section" style="background: #f9f9f9; padding: 20px; border-radius: 8px;">
        <table width="100%" cellspacing="10">
            <tr>
                <td class="label" width="30%">Job Name:</td>
                <td>{{ $job->name }}</td>
            </tr>
            <tr>
                <td class="label">Reference:</td>
                <td>{{ $job->tender->tender_number }}</td>
            </tr>
            <tr>
                <td class="label">Award Amount:</td>
                <td><strong>LKR {{ number_format($job->contractor_quote_amount, 2) }}</strong></td>
            </tr>
            <tr>
                <td class="label">Customer:</td>
                <td>{{ $job->customer->name }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <p>You are requested to commence the mobilization process and coordinate with the Site Supervisor for the official work start date. All works must be carried out in accordance with the specifications and terms discussed during the quotation phase.</p>
        <p>Please sign and return a copy of this letter as a token of your acceptance.</p>
    </div>

    <div class="signature-area">
        <div class="signature-box">
            <p>..........................................</p>
            <p><strong>Authorized Signature</strong><br>FinancePro Corp</p>
        </div>
        <div class="signature-box" style="text-align: right;">
            <p>..........................................</p>
            <p><strong>Accepted By</strong><br>{{ $job->selectedContractor->name }}</p>
        </div>
    </div>

    <div class="footer">
        <p>This is a computer-generated document. For official verification, please contact our procurement department.</p>
    </div>
</body>

</html>