<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Letter of Award</title>
    <style>
        @page {
            margin: 0;
            padding: 0;
            size: A4;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11.5px;
            line-height: 1.6;
            color: #1e293b;
            background: white;
            padding: 0;
            margin: 0;
        }

        .letter-wrapper {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
        }

        /* ── Header ─────────────────────────────────────────────── */
        .header {
            padding: 28px 40px 18px 40px;
            border-bottom: 3px solid #004A99;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .logo-cell {
            width: 44px;
            vertical-align: middle;
        }

        .logo-cell img {
            width: 36px;
            height: 36px;
            object-fit: contain;
        }

        .name-cell {
            vertical-align: middle;
            padding-left: 10px;
        }

        .company-name {
            font-size: 15px;
            font-weight: 800;
            color: #003366;
            text-transform: uppercase;
            letter-spacing: -0.2px;
            line-height: 1.2;
        }

        .company-tagline {
            font-size: 8.5px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            margin-top: 1px;
        }

        .ref-cell {
            text-align: right;
            vertical-align: middle;
        }

        .ref-box {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 6px 10px;
            display: inline-block;
        }

        .ref-label {
            font-size: 7.5px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .ref-value {
            font-size: 11px;
            font-weight: 800;
            color: #004A99;
        }

        .company-address {
            font-size: 9px;
            color: #64748b;
            margin-top: 6px;
            padding-top: 6px;
            border-top: 1px solid #e2e8f0;
        }

        /* ── Content ──────────────────────────────────────────────── */
        .content {
            padding: 28px 40px 20px 40px;
        }

        .meta-row {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        .doc-title-cell {
            vertical-align: bottom;
        }

        .doc-title {
            font-size: 15px;
            font-weight: 800;
            color: #003366;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #004A99;
            padding-bottom: 4px;
            display: inline-block;
        }

        .date-cell {
            text-align: right;
            vertical-align: bottom;
            font-size: 10px;
            color: #64748b;
        }

        .section {
            margin-bottom: 14px;
        }

        .section p {
            margin-bottom: 6px;
        }

        /* ── Details Box ──────────────────────────────────────────── */
        .details-box {
            background: #f8fafc;
            border-left: 4px solid #004A99;
            border-top: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
            border-radius: 0 6px 6px 0;
            padding: 14px 16px;
            margin: 14px 0;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table td {
            padding: 5px 0;
            font-size: 10.5px;
            vertical-align: top;
        }

        .details-label {
            color: #64748b;
            width: 32%;
            font-weight: 600;
        }

        .details-sep {
            width: 16px;
            color: #cbd5e1;
        }

        .details-value {
            color: #0f172a;
            font-weight: 700;
        }

        /* ── Amount highlight ─────────────────────────────────────── */
        .amount-row {
            background: #eef2ff;
            border: 1px solid #c7d2fe;
            border-radius: 6px;
            padding: 10px 14px;
            margin: 4px 0;
        }

        .amount-label {
            font-size: 8px;
            font-weight: 700;
            color: #6366f1;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .amount-value {
            font-size: 20px;
            font-weight: 900;
            color: #1e40af;
            line-height: 1.1;
        }

        /* ── Body text ────────────────────────────────────────────── */
        .body-text {
            font-size: 11px;
            color: #334155;
            line-height: 1.65;
            margin-bottom: 10px;
        }

        /* ── Signature + Seal row ─────────────────────────────────── */
        .bottom-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 32px;
        }

        .sig-cell {
            vertical-align: bottom;
            width: 55%;
            padding-right: 20px;
        }

        .sig-block {
            margin-bottom: 24px;
        }

        .sig-line {
            border-bottom: 1px solid #94a3b8;
            padding-top: 36px;
            margin-bottom: 5px;
        }

        .sig-name {
            font-size: 10px;
            font-weight: 700;
            color: #0f172a;
        }

        .sig-title {
            font-size: 9px;
            color: #64748b;
        }

        /* ── Seal — DomPDF-compatible, table-based ──────────────────
           DomPDF does NOT support display:flex or display:grid.
           We use position:absolute inside a fixed-size position:relative
           container. Pixel values only — no translate() or %.
        ─────────────────────────────────────────────────────────────── */
        .seal-cell {
            vertical-align: bottom;
            text-align: right;
            width: 45%;
        }

        .seal-outer {
            display: inline-block;
            position: relative;
            width: 200px;
            height: 200px;
        }

        .seal-outer img {
            position: absolute;
            top: 0;
            left: 0;
            width: 200px;
            height: 200px;
        }

        /* Text sits in the center of the seal ring.
           The seal PNG has a hollow center roughly at top:70px height:80px.
           Adjust top/height if the seal image changes. */
        .seal-text-block {
            position: absolute;
            top: 72px;
            left: 20px;
            width: 160px;
            height: 56px;
            text-align: center;
        }

        .seal-code {
            font-size: 14px;
            font-weight: 900;
            color: #1a1a1a;
            letter-spacing: 0.5px;
            line-height: 1.3;
        }

        .seal-ref {
            font-size: 10px;
            font-weight: 700;
            color: #1a1a1a;
            letter-spacing: 0.3px;
            line-height: 1.3;
            margin-top: 2px;
        }

        /* ── Footer ──────────────────────────────────────────────── */
        .footer {
            margin-top: 28px;
            padding: 12px 40px;
            background: #0f172a;
        }

        .footer-text {
            font-size: 8.5px;
            color: #94a3b8;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

    </style>
</head>
<body>

    <div class="letter-wrapper">

        <!-- Header -->
        <div class="header">
            <table class="header-table">
                <tr>
                    <td class="logo-cell">
                        <img src="{{ public_path('icons/slt_digital_icon.png') }}" alt="SLT">
                    </td>
                    <td class="name-cell">
                        <div class="company-name">Sri Lanka Telecom Services</div>
                        <div class="company-tagline">Finance Division</div>
                    </td>
                    <td class="ref-cell">
                        <div class="ref-box">
                            <div class="ref-label">Ref. No.</div>
                            <div class="ref-value">{{ $job->tender->tender_number }}</div>
                        </div>
                    </td>
                </tr>
            </table>
            <div class="company-address">
                Lotus Road, Colombo 01, Sri Lanka &nbsp;|&nbsp; T: +94 11 232 9711 &nbsp;|&nbsp; E: procurement@slt.lk
            </div>
        </div>

        <!-- Content -->
        <div class="content">

            <!-- Title + Date row -->
            <table class="meta-row">
                <tr>
                    <td class="doc-title-cell">
                        <span class="doc-title">Letter of Award</span>
                    </td>
                    <td class="date-cell">
                        Date: {{ date('d F Y') }}
                    </td>
                </tr>
            </table>

            <!-- Addressee -->
            <div class="section">
                <p class="body-text">
                    To,<br>
                    <strong>{{ $job->selectedContractor->name }}</strong><br>
                    {{ $job->selectedContractor->address ?? 'Contractor Address' }}
                </p>
            </div>

            <!-- Salutation -->
            <div class="section">
                <p class="body-text">Dear Sir / Madam,</p>
                <p class="body-text">
                    We are pleased to inform you that, following the evaluation of quotations received for the
                    project <strong>"{{ $job->tender->tender_name }}"</strong>, your firm has been selected as
                    the successful contractor for the scope of work detailed below.
                </p>
            </div>

            <!-- Details Box -->
            <div class="details-box">
                <table class="details-table">
                    <tr>
                        <td class="details-label">Contractor</td>
                        <td class="details-sep">:</td>
                        <td class="details-value">{{ $job->selectedContractor->name }}</td>
                    </tr>
                    <tr>
                        <td class="details-label">Contractor Code</td>
                        <td class="details-sep">:</td>
                        <td class="details-value">{{ $job->selectedContractor->contractor_code }}</td>
                    </tr>
                    <tr>
                        <td class="details-label">Job / Work Scope</td>
                        <td class="details-sep">:</td>
                        <td class="details-value">{{ $job->name }}</td>
                    </tr>
                    <tr>
                        <td class="details-label">Customer</td>
                        <td class="details-sep">:</td>
                        <td class="details-value">{{ $job->customer->name }}</td>
                    </tr>
                    @if($job->work_start_date)
                    <tr>
                        <td class="details-label">Commencement Date</td>
                        <td class="details-sep">:</td>
                        <td class="details-value">{{ $job->work_start_date->format('d F Y') }}</td>
                    </tr>
                    @endif
                    @if($job->work_completion_date)
                    <tr>
                        <td class="details-label">Completion Date</td>
                        <td class="details-sep">:</td>
                        <td class="details-value">{{ $job->work_completion_date->format('d F Y') }}</td>
                    </tr>
                    @endif
                </table>

                <!-- Award Amount -->
                <div class="amount-row" style="margin-top: 10px;">
                    <div class="amount-label">Contract / Award Amount</div>
                    <div class="amount-value">LKR {{ number_format($job->contractor_quote_amount, 2) }}</div>
                </div>
            </div>

            <!-- Body paragraphs -->
            <div class="section">
                <p class="body-text">
                    You are hereby requested to commence the mobilization process and coordinate with the
                    designated Site Supervisor to confirm the official work start date. All works must be
                    executed strictly in accordance with the specifications, drawings, and terms agreed upon
                    during the quotation phase.
                </p>
                <p class="body-text">
                    Please sign and return one copy of this letter as acknowledgement of your acceptance of
                    the award and the terms herein.
                </p>
            </div>

            <!-- Signatures + Seal -->
            <table class="bottom-table">
                <tr>
                    <td class="sig-cell">
                        <div class="sig-block">
                            <div class="sig-line"></div>
                            <div class="sig-name">Authorized Signatory</div>
                            <div class="sig-title">Sri Lanka Telecom Services</div>
                        </div>
                        <div class="sig-block">
                            <div class="sig-line"></div>
                            <div class="sig-name">Accepted By — {{ $job->selectedContractor->name }}</div>
                            <div class="sig-title">Date: ___________________</div>
                        </div>
                    </td>
                    <td class="seal-cell">
                        <div class="seal-outer">
                            <img src="{{ public_path('assets/images/tender-award-seal.png') }}"
                                 alt="Official Tender Award Seal">
                            <div class="seal-text-block">
                                <div class="seal-code">{{ $job->selectedContractor->contractor_code }}</div>
                                <div class="seal-ref">{{ $job->tender->tender_number }}</div>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>

        </div>

        <!-- Footer -->
        <div class="footer">
            <p class="footer-text">
                This is a computer-generated document. For official verification contact the Procurement Department.
            </p>
        </div>

    </div>

</body>
</html>
