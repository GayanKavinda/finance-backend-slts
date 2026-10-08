<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Purchase Order #{{ $po->po_number }}</title>
    <style>
        @page {
            margin: 20mm 18mm 22mm 18mm;
            size: A4 portrait;
            @bottom-left {
                content: "SLT ProcureX ERP · Procurement Division";
                font-family: "DejaVu Sans", "Helvetica Neue", Arial, sans-serif;
                font-size: 7.5px;
                color: #94a3b8;
            }
            @bottom-right {
                content: "Page " counter(page) " of " counter(pages);
                font-family: "DejaVu Sans", "Helvetica Neue", Arial, sans-serif;
                font-size: 7.5px;
                color: #94a3b8;
            }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "DejaVu Sans", "Helvetica Neue", Arial, sans-serif;
            font-size: 10px;
            line-height: 1.45;
            color: #1e293b;
            background: #ffffff;
        }

        /* ── Brand Accent ─────────────────────────────── */
        .top-brand-bar {
            height: 5px;
            background: #004A99;
            width: 100%;
            margin-bottom: 18px;
        }

        /* ── Header ───────────────────────────────────── */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .header-table > tbody > tr > td {
            vertical-align: middle;
        }

        .brand-title {
            font-size: 17px;
            font-weight: 800;
            color: #003366;
            letter-spacing: -0.2px;
            text-transform: uppercase;
        .brand-logo {
            width: 56px;
            height: 56px;
            object-fit: contain;
            display: block;
            margin-bottom: 8px;
        }
        }

        .brand-subtitle {
            font-size: 10px;
            font-weight: 700;
            color: #004A99;
            margin-top: 6px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .brand-address {
            font-size: 8.5px;
            color: #475569;
            margin-top: 7px;
            line-height: 1.5;
        }

        .doc-title {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 1.2px;
            color: #0f172a;
            text-transform: uppercase;
            text-align: right;
            line-height: 1.1;
        }

        .doc-title-rule {
            height: 2px;
            background: #003366;
            margin: 6px 0 8px;
        }

        .po-number-panel {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 3px solid #004A99;
            padding: 10px 16px;
            text-align: right;
        }

        .po-number-label {
            font-size: 7.5px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .po-number-value {
            font-size: 15px;
            font-weight: 800;
            color: #003366;
            letter-spacing: 0.3px;
            margin-top: 1px;
        }

        .badge-pill {
            display: inline-block;
            margin-top: 7px;
            padding: 5px 14px;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.9px;
            border: 1px solid;
        }

        /* ── Meta Strip ───────────────────────────────── */
        .meta-strip {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .meta-strip td {
            width: 25%;
            border: 1px solid #e2e8f0;
            border-top: 2px solid #004A99;
            background: #f8fafc;
            padding: 12px 18px;
            vertical-align: middle;
        }

        .meta-strip-label {
            font-size: 7.5px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            margin-bottom: 2px;
        }

        .meta-strip-value {
            font-size: 10px;
            font-weight: 700;
            color: #0f172a;
        }

        /* ── Party Cards ──────────────────────────────── */
        .party-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px 0;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .party-card {
            width: 50%;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .party-card-head {
            background: #003366;
            color: #ffffff;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.9px;
            padding: 9px 16px;
        }

        .party-card-body {
            padding: 12px 16px;
        }

        .party-name {
            font-size: 12px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .party-line {
            font-size: 10px;
            color: #475569;
            line-height: 1.55;
        }

        .party-line strong {
            color: #0f172a;
            font-weight: 700;
        }

        /* ── Section Titles ───────────────────────────── */
        .section-title {
            font-size: 10px;
            font-weight: 800;
            color: #003366;
            text-transform: uppercase;
            letter-spacing: 0.9px;
            margin: 0 0 6px;
            padding-bottom: 3px;
            border-bottom: 1.5px solid #003366;
        }

        /* ── Reference Grid ───────────────────────────── */
        .ref-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .ref-grid td {
            border: 1px solid #e2e8f0;
            padding: 9px 14px;
            vertical-align: middle;
        }

        .ref-label {
            font-size: 7.5px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }

        .ref-value {
            font-size: 10px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 1px;
        }

        /* ── Items Table ──────────────────────────────── */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .items-table th {
            background: #003366;
            color: #ffffff;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            padding: 8px 10px;
            text-align: left;
            border: 1px solid #003366;
        }

        .items-table td {
            border: 1px solid #e2e8f0;
            padding: 11px 14px;
            font-size: 10px;
            color: #1e293b;
            vertical-align: middle;
        }

        .items-table td.tar,
        .items-table th.tar {
            text-align: right;
        }

        .items-table td.ctr,
        .items-table th.ctr {
            text-align: center;
            width: 8%;
        }

        .item-no {
            font-weight: 800;
            color: #004A99;
        }

        .item-text {
            font-weight: 600;
            color: #0f172a;
        }

        .row-total {
            font-size: 11px;
            font-weight: 800;
            color: #003366;
            white-space: nowrap;
        }

        /* ── Bottom Split: Terms + Totals ─────────────── */
        .bottom-split {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px 0;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .terms-box {
            width: 52%;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .terms-box-head {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            font-size: 8px;
            font-weight: 800;
            color: #003366;
            text-transform: uppercase;
            letter-spacing: 0.9px;
            padding: 9px 16px;
        }

        .terms-list {
            padding: 9px 12px 9px 24px;
        }

        .terms-list li {
            font-size: 8.5px;
            color: #475569;
            line-height: 1.5;
            margin-bottom: 4px;
        }

        .totals-box {
            width: 48%;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .totals-box-head {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            font-size: 8px;
            font-weight: 800;
            color: #003366;
            text-transform: uppercase;
            letter-spacing: 0.9px;
            padding: 9px 16px;
        }

        .totals-body {
            padding: 12px 16px;
        }

        .totals-row {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-row td {
            padding: 4px 0;
            font-size: 10px;
            vertical-align: middle;
        }

        .totals-label {
            color: #64748b;
            font-weight: 500;
        }

        .totals-val {
            text-align: right;
            font-weight: 700;
            color: #0f172a;
            white-space: nowrap;
        }

        .grand-total-row td {
            padding-top: 8px;
            margin-top: 12px;
            border-top: 2px solid #003366;
            font-size: 11px;
            font-weight: 800;
            color: #003366;
        }

        .grand-total-row .totals-val {
            font-size: 14px;
        }

        .words-box {
            margin-top: 12px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            padding: 12px 18px;
            font-size: 8.5px;
            color: #475569;
            line-height: 1.5;
        }

        .words-box strong {
            color: #0f172a;
        }

        /* ── Authorization ────────────────────────────── */
        .auth-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 14px 0;
            margin-top: 10px;
            page-break-inside: avoid;
        }

        .auth-col {
            width: 33.33%;
            vertical-align: bottom;
        }

        .auth-sig-line {
            border-bottom: 1px solid #475569;
            height: 34px;
        }

        .auth-role {
            font-size: 8px;
            font-weight: 800;
            color: #003366;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-top: 12px;
        }

        .auth-sub {
            font-size: 8px;
            color: #64748b;
            margin-top: 1px;
        }

        /* ── Doc Control Footer ───────────────────────── */
        .doc-control {
            margin-top: 22px;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
            font-size: 7.5px;
            color: #94a3b8;
        }

        .doc-control td {
            vertical-align: middle;
        }

        .auth-notice {
            text-align: right;
            font-style: italic;
        }
    </style>
</head>
<body>
    @php
        $statusStyles = [
            'Draft'     => ['bg' => '#f1f5f9', 'fg' => '#475569', 'bd' => '#cbd5e1'],
            'Approved'  => ['bg' => '#f0fdf4', 'fg' => '#15803d', 'bd' => '#86efac'],
            'Sent'      => ['bg' => '#eff6ff', 'fg' => '#1d4ed8', 'bd' => '#93c5fd'],
            'Received'  => ['bg' => '#eef2ff', 'fg' => '#4338ca', 'bd' => '#a5b4fc'],
            'Cancelled' => ['bg' => '#fef2f2', 'fg' => '#b91c1c', 'bd' => '#fca5a5'],
        ];
        $s = $statusStyles[$po->status] ?? $statusStyles['Draft'];

        $issueDate = \Carbon\Carbon::parse($po->po_date)->format('d F Y');
        $validity  = \Carbon\Carbon::parse($po->po_date)->addDays(30)->format('d M Y');

        $lines = collect(preg_split('/\R/', (string) $po->po_description))
            ->map(fn ($l) => trim((string) $l))
            ->filter(fn ($l) => $l !== '')
            ->values();

        if ($lines->isEmpty()) {
            $lines = collect(['Procurement of goods and services as per agreed terms, specifications, and tender conditions.']);
        }

        $supplier = $po->customer;
        $tender   = $po->tender;
        $job      = $po->job;
    @endphp

    <div class="top-brand-bar"></div>

    {{-- ── Letterhead & Document Identity ─────────────--}}
    <table class="header-table">
        <tr>
            <td style="width: 55%;">
                <img src="{{ $company['logo'] }}" alt="SLT" class="brand-logo">
                <div class="brand-title">Sri Lanka Telecom Services</div>
                <div class="brand-subtitle">Commercial &amp; Finance Division · Procurement</div>
                <div class="brand-address">
                    Lotus Road, P.O. Box 503, Colombo 01, Sri Lanka<br>
                    Tel: +94 11 232 9711 &nbsp;·&nbsp; VAT Reg: 114002847-7000
                </div>
            </td>
            <td style="width: 45%; text-align: right;">
                <div class="doc-title">Purchase Order</div>
                <div class="doc-title-rule"></div>
                <div class="po-number-panel">
                    <div class="po-number-label">Order Reference</div>
                    <div class="po-number-value">{{ $po->po_number }}</div>
                    <span class="badge-pill" style="background: {{ $s['bg'] }}; color: {{ $s['fg'] }}; border-color: {{ $s['bd'] }};">
                        {{ $po->status }}
                    </span>
                </div>
            </td>
        </tr>
    </table>

    {{-- ── Key Details Strip ──────────────────────────--}}
    <table class="meta-strip">
        <tr>
            <td>
                <div class="meta-strip-label">Date of Issue</div>
                <div class="meta-strip-value">{{ $issueDate }}</div>
            </td>
            <td>
                <div class="meta-strip-label">Order Validity</div>
                <div class="meta-strip-value">Valid until {{ $validity }}</div>
            </td>
            <td>
                <div class="meta-strip-label">Currency</div>
                <div class="meta-strip-value">LKR — Sri Lankan Rupees</div>
            </td>
            <td>
                <div class="meta-strip-label">Payment Terms</div>
                <div class="meta-strip-value">Net 30 days from invoice</div>
            </td>
        </tr>
    </table>

    {{-- ── Supplier & Purchaser ───────────────────────--}}
    <table class="party-table">
        <tr>
            <td class="party-card">
                <div class="party-card-head">Supplier</div>
                <div class="party-card-body">
                    <div class="party-name">{{ $supplier->name ?? 'Valued Supplier' }}</div>
                    <div class="party-line">
                        @if($supplier && $supplier->contact_person)
                            <strong>Contact:</strong> {{ $supplier->contact_person }}<br>
                        @endif
                        @if($supplier && $supplier->billing_address)
                            {!! nl2br(e($supplier->billing_address)) !!}
                        @else
                            Address on file
                        @endif
                        @if($supplier && $supplier->tax_number)
                            <br><strong>VAT / Tax ID:</strong> {{ $supplier->tax_number }}
                        @endif
                        @if($supplier && ($supplier->phone || $supplier->email))
                            <br><strong>Tel:</strong> {{ $supplier->phone ?: '—' }} &nbsp;·&nbsp; <strong>Email:</strong> {{ $supplier->email ?: '—' }}
                        @endif
                    </div>
                </div>
            </td>
            <td class="party-card">
                <div class="party-card-head">Purchaser / Deliver To</div>
                <div class="party-card-body">
                    <div class="party-name">Sri Lanka Telecom Services Limited</div>
                    <div class="party-line">
                        Procurement Department<br>
                        Lotus Road, P.O. Box 503, Colombo 01, Sri Lanka
                    </div>
                    @if($po->billing_address)
                        <div class="party-line" style="margin-top: 10px;">
                            <strong>Delivery / Site Address:</strong><br>
                            {!! nl2br(e($po->billing_address)) !!}
                        </div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    {{-- ── Reference Grid ─────────────────────────────--}}
    <div class="section-title">Order References</div>
    <table class="ref-grid">
        <tr>
            <td style="width: 25%;">
                <div class="ref-label">PO Reference</div>
                <div class="ref-value">{{ $po->po_number }}</div>
            </td>
            <td style="width: 25%;">
                <div class="ref-label">Tender Reference</div>
                <div class="ref-value">{{ $tender->tender_number ?? 'N/A' }}</div>
            </td>
            <td style="width: 25%;">
                <div class="ref-label">Tender Title</div>
                <div class="ref-value">{{ $tender->name ?? 'N/A' }}</div>
            </td>
            <td style="width: 25%;">
                <div class="ref-label">Job / Project</div>
                <div class="ref-value">{{ $job->name ?? 'N/A' }}</div>
            </td>
        </tr>
    </table>

    {{-- ── Order Summary ──────────────────────────────--}}
    <div class="section-title">Order Summary</div>
    <table class="items-table">
        <thead>
            <tr>
                <th class="ctr">No.</th>
                <th style="width: 58%;">Description / Scope of Supply</th>
                <th style="width: 22%;">Tender Reference</th>
                <th class="tar" style="width: 20%;">Amount (LKR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($lines as $index => $line)
                <tr>
                    <td class="ctr item-no">{{ $index + 1 }}</td>
                    <td class="item-text">{{ $line }}</td>
                    <td>{{ $tender->tender_number ?? 'N/A' }}</td>
                    @if ($index === 0)
                        <td class="tar row-total" rowspan="{{ $lines->count() }}">
                            {{ number_format($po->po_amount, 2) }}
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ── Terms & Totals ─────────────────────────────--}}
    <table class="bottom-split">
        <tr>
            <td class="terms-box">
                <div class="terms-box-head">Terms &amp; Conditions</div>
                <ol class="terms-list">
                    <li>Prices are exclusive of VAT and other government levies unless expressly stated herein.</li>
                    <li>Delivery shall be made to the Purchaser's nominated delivery address within the agreed schedule.</li>
                    <li>Payment will be effected Net 30 days from receipt of a valid tax invoice quoting this Purchase Order reference.</li>
                    <li>This Purchase Order is valid until the validity date shown above; unfulfilled quantities lapse thereafter.</li>
                    <li>The Supplier must acknowledge this order in writing and confirm the committed delivery date.</li>
                    <li>All supplies must conform to the tender specifications and applicable Sri Lanka Standards.</li>
                    <li>This order may be terminated for default upon written notice by the Purchaser.</li>
                    <li>Governed by the laws of the Democratic Socialist Republic of Sri Lanka.</li>
                </ol>
            </td>
            <td class="totals-box">
                <div class="totals-box-head">Order Total</div>
                <div class="totals-body">
                    <table class="totals-row">
                        <tr>
                            <td class="totals-label">Subtotal (Net)</td>
                            <td class="totals-val">{{ number_format($po->po_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="totals-label">VAT / Levies</td>
                            <td class="totals-val">As per tax invoice</td>
                        </tr>
                        <tr class="grand-total-row">
                            <td>PO Total (LKR)</td>
                            <td class="totals-val tar">{{ number_format($po->po_amount, 2) }}</td>
                        </tr>
                    </table>
                    <div class="words-box">
                        <strong>Total in Words:</strong> Rupees {{ $amountWords }} Only
                    </div>
                </div>
            </td>
        </tr>
    </table>

    {{-- ── Authorization Signatures ───────────────────--}}
    <table class="auth-table">
        <tr>
            <td class="auth-col">
                <div class="auth-sig-line"></div>
                <div class="auth-role">Prepared By</div>
                <div class="auth-sub">Procurement Officer</div>
            </td>
            <td class="auth-col">
                <div class="auth-sig-line"></div>
                <div class="auth-role">Reviewed By</div>
                <div class="auth-sub">Finance Controller</div>
            </td>
            <td class="auth-col">
                <div class="auth-sig-line"></div>
                <div class="auth-role">Approved By</div>
                <div class="auth-sub">Authorised Signatory</div>
            </td>
        </tr>
    </table>

    {{-- ── Document Control ───────────────────────────--}}
    <table class="doc-control">
        <tr>
            <td>
                Document ID: PO-{{ str_pad($po->id, 6, '0', STR_PAD_LEFT) }} &nbsp;·&nbsp;
                Generated {{ now()->format('d M Y, H:i') }} via SLT ProcureX ERP
            </td>
            <td class="auth-notice">
                Verification Hash: {{ strtoupper(substr(md5($po->po_number . $po->id), 0, 16)) }}
            </td>
        </tr>
    </table>
</body>
</html>
