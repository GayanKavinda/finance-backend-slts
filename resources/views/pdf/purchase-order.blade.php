<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Purchase Order {{ $po->po_number }}</title>
    <style>
        /* Editorial single-page A4 purchase order.
           Layout: solid SLT-blue spine (left) + main column. No absolute or fixed elements.
           Font: Helvetica (built into DomPDF).

           SLT palette: blue #0072BC | deep #005A96 | green #8CC63F | green text #5f9a1e
           tint #EAF4FB | hairline #d6e6f3 | ink #1f2937 | muted #6b7280 */

        @page { margin: 0; size: A4 portrait; }

        html, body { margin: 0; padding: 0; }

        body {
            font-family: "Helvetica", Arial, sans-serif;
            font-size: 9.5px;
            line-height: 1.5;
            color: #374151;
            background: #ffffff;
        }

        table { border-collapse: collapse; width: 100%; }
        td, th { vertical-align: top; }

        /* ── Page layout: spine + main ───────────── */
        /* If your renderer ignores the height, the spine simply ends with the content. */
        .layout { height: 1058px; }
        .spine {
            width: 190px; background: #0072BC; border-right: 6px solid #8CC63F;
            padding: 36px 22px 26px 36px; color: #ffffff;
        }
        .main { padding: 36px 44px 18px 36px; }

        /* ── Spine ───────────────────────────────── */
        .logo-tile { background: #ffffff; width: 46px; height: 46px; padding: 5px; margin-bottom: 13px; }
        .logo-tile img { width: 46px; height: 46px; }
        .sp-brand { font-size: 11px; font-weight: bold; color: #ffffff; line-height: 1.3; }
        .sp-meta  { font-size: 7px; color: #cfe6f7; margin-top: 6px; line-height: 1.7; }

        .sp-gap { height: 32px; }
        .sp-kicker { font-size: 6.5px; font-weight: bold; letter-spacing: 3px; text-transform: uppercase; color: #8CC63F; }
        .sp-title  { font-size: 25px; font-weight: bold; color: #ffffff; letter-spacing: 1px; line-height: 1.1; margin-top: 7px; text-transform: uppercase; }
        .sp-ref    { font-size: 10.5px; font-weight: bold; color: #ffffff; letter-spacing: 1px; margin-top: 13px; }
        .sp-status { font-size: 7px; font-weight: bold; letter-spacing: 1.8px; text-transform: uppercase; margin-top: 3px; }

        .sp-rule  { height: 1px; background: #3f95cf; margin: 24px 0 18px 0; font-size: 1px; line-height: 1px; }
        .sp-label { font-size: 6.2px; font-weight: bold; letter-spacing: 1.8px; text-transform: uppercase; color: #8CC63F; margin-bottom: 2px; }
        .sp-value { font-size: 9.2px; font-weight: bold; color: #ffffff; margin-bottom: 14px; line-height: 1.35; }
        .sp-small { font-size: 7.2px; color: #cfe6f7; letter-spacing: 0.8px; line-height: 1.6; }

        .stats td { width: 50%; }
        .st-num { font-size: 34px; font-weight: bold; color: #ffffff; line-height: 1.05; letter-spacing: 0.5px; }

        /* ── Main: top line + segmented bar ──────── */
        .eyebrow { font-size: 7.5px; font-weight: bold; letter-spacing: 3.5px; text-transform: uppercase; color: #0072BC; }
        .issued  { font-size: 7.5px; color: #9aa3af; letter-spacing: 1px; text-align: right; text-transform: uppercase; }
        .seg { width: auto; margin: 8px 0 22px 0; }
        .seg td { height: 4px; padding: 0; font-size: 1px; line-height: 1px; }

        /* ── Award ───────────────────────────────── */
        .lead { font-size: 7px; font-weight: bold; letter-spacing: 2.2px; text-transform: uppercase; color: #8a94a3; }
        .name { font-size: 27px; font-weight: bold; color: #005A96; line-height: 1.2; margin-top: 5px; }
        .name-rule { width: 42px; height: 4px; background: #8CC63F; margin: 11px 0 11px 0; font-size: 1px; line-height: 1px; }
        .for-lead { font-size: 8.5px; color: #8a94a3; }
        .for-name { font-size: 11.5px; font-weight: bold; color: #1f2937; margin-top: 2px; line-height: 1.4; }

        /* ── Value block ─────────────────────────── */
        .value { margin: 22px 0 24px 0; }
        .v-bar  { width: 6px; background: #0072BC; }
        .v-body { background: #EAF4FB; padding: 14px 20px 14px 20px; }
        .v-label { font-size: 6.8px; font-weight: bold; letter-spacing: 2.8px; text-transform: uppercase; color: #0072BC; }
        .v-amt   { font-size: 34px; font-weight: bold; color: #0072BC; line-height: 1.15; margin-top: 4px; letter-spacing: 0.3px; }
        .v-ccy   { font-size: 12px; font-weight: bold; color: #5f9a1e; letter-spacing: 2px; }
        .v-words { font-size: 8px; color: #6b7280; margin-top: 5px; }

        /* ── Section heading ─────────────────────── */
        .sec { margin-bottom: 10px; padding-bottom: 5px; border-bottom: 1px solid #d6e6f3; font-size: 7.5px; font-weight: bold; letter-spacing: 2.8px; text-transform: uppercase; color: #005A96; }
        .sec .n { color: #8CC63F; margin-right: 9px; letter-spacing: 1px; }

        /* ── Parties ─────────────────────────────── */
        .parties { margin-bottom: 24px; }
        .party-cell { width: 50%; }
        .party-cell.l { padding-right: 18px; }
        .party-cell.r { padding-left: 18px; border-left: 1px solid #d6e6f3; }
        .p-label { font-size: 6.6px; font-weight: bold; letter-spacing: 1.8px; text-transform: uppercase; color: #0072BC; margin-bottom: 4px; }
        .p-name  { font-size: 11px; font-weight: bold; color: #1f2937; margin-bottom: 4px; }
        .p-line  { font-size: 8.6px; color: #6b7280; line-height: 1.65; }

        /* ── Scope table ─────────────────────────── */
        .scope { margin-bottom: 24px; }
        .scope th {
            text-align: left; font-size: 6.6px; font-weight: bold; letter-spacing: 1.8px; text-transform: uppercase;
            color: #0072BC; padding: 0 8px 7px 0; border-bottom: 2px solid #0072BC;
        }
        .scope td { padding: 10px 8px 10px 0; border-bottom: 1px solid #e3eef7; font-size: 9.2px; color: #374151; }
        .scope .tar { text-align: right; padding-right: 0; }
        .scope .no  { width: 7%; font-weight: bold; color: #8CC63F; }
        .scope .amt { font-size: 12px; font-weight: bold; color: #005A96; white-space: nowrap; }
        .scope .more { font-size: 7.6px; color: #8a94a3; }

        /* ── Terms ───────────────────────────────── */
        .terms { margin-bottom: 24px; }
        .terms td { width: 50%; }
        .terms td.l { padding-right: 14px; }
        .terms td.r { padding-left: 14px; }
        .term { font-size: 7.4px; color: #6b7280; line-height: 1.5; margin-bottom: 5px; padding-left: 17px; }
        .term b { font-weight: bold; color: #0072BC; margin-left: -17px; display: inline-block; width: 17px; }

        /* ── Signatures + seal ───────────────────── */
        .sign { margin-bottom: 24px; }
        .sign td { vertical-align: bottom; }
        .sign .s { width: 24%; padding-right: 16px; }
        .sig-line { border-bottom: 1px solid #9fb3c6; height: 34px; }
        .sig-role { font-size: 6.6px; font-weight: bold; letter-spacing: 1.5px; text-transform: uppercase; color: #005A96; margin-top: 7px; }
        .sig-sub  { font-size: 7.5px; color: #8a94a3; margin-top: 1px; }
        .sig-date { font-size: 6.3px; color: #a9b4c0; margin-top: 9px; padding-bottom: 1px; border-bottom: 1px dotted #c3d3e1; letter-spacing: 1.2px; text-transform: uppercase; }

        .seal-cell { width: 28%; text-align: right; }

        /* Seal geometry (content-box): outer 74 + 2*3 padding + 2*2 border = 84 total, radius 42.
           Inner 72 + 2*1 border = 74 = outer content box, radius 37. Pixel radii, not %. */
        .seal-out {
            display: inline-block; width: 74px; height: 74px; padding: 3px;
            border: 2px solid #8CC63F; border-radius: 42px; background: #ffffff;
        }
        .seal-in  { width: 72px; height: 72px; border: 1px solid #0072BC; border-radius: 37px; background: #0072BC; }
        .seal-tbl { width: 72px; height: 72px; border-collapse: collapse; }
        .seal-tbl td { width: 72px; height: 72px; padding: 0; text-align: center; vertical-align: middle; }
        .seal-top { font-size: 5.2px; font-weight: bold; line-height: 7px; letter-spacing: 0.8px; color: #d8f0b5; }
        .seal-mid { font-size: 8.6px; font-weight: bold; line-height: 12px; letter-spacing: 1.1px; color: #ffffff; }
        .seal-bar { width: 24px; height: 1px; background: #8CC63F; margin: 2px auto 2px auto; font-size: 1px; line-height: 1px; }

        /* ── Supplier acknowledgement ────────────── */
        .ack-text { font-size: 7.8px; color: #6b7280; line-height: 1.6; margin-bottom: 4px; }
        .ack-f td { padding-right: 12px; vertical-align: bottom; }
        .ack-line { border-bottom: 1px solid #9fb3c6; height: 24px; }
        .ack-lab  { font-size: 6.2px; font-weight: bold; letter-spacing: 1.5px; text-transform: uppercase; color: #5b8db8; margin-top: 4px; }
        .stamp {
            border: 1px dashed #9fb3c6; height: 62px; text-align: center;
            padding-top: 26px; font-size: 6.3px; letter-spacing: 2px; text-transform: uppercase; color: #a9b4c0;
        }

        /* ── Footer ──────────────────────────────── */
        .footer td { font-size: 6.6px; color: #9aa3af; letter-spacing: 0.5px; vertical-align: middle; padding: 8px 0; }
        .footer td.fl { padding-left: 236px; }
        .footer td.fr { padding-right: 44px; text-align: right; }
        .footer .g { color: #0072BC; font-weight: bold; }
        .stripe td { height: 5px; padding: 0; font-size: 1px; line-height: 1px; }
        .stripe .b { width: 72%; background: #0072BC; }
        .stripe .g2 { width: 28%; background: #8CC63F; }
    </style>
</head>
<body>
    @php
        // Light variants so status stays readable on the blue spine.
        $statusColors = [
            'Draft'     => '#dbe7f2',
            'Approved'  => '#c5f0a4',
            'Sent'      => '#bfe0ff',
            'Received'  => '#d6d9ff',
            'Cancelled' => '#ffc9c9',
        ];
        $statusColor = $statusColors[$po->status] ?? $statusColors['Draft'];

        $issueDate = \Carbon\Carbon::parse($po->po_date)->format('d F Y');
        $validity  = \Carbon\Carbon::parse($po->po_date)->addDays(30)->format('d F Y');

        $lines = collect(preg_split('/\R/', (string) $po->po_description))
            ->map(fn ($l) => trim((string) $l))
            ->filter(fn ($l) => $l !== '')
            ->values();

        if ($lines->isEmpty()) {
            $lines = collect(['Procurement of goods and services as per agreed terms, specifications, and tender conditions.']);
        }

        // Page-fit controls. Keeps the document on a single page.
        $maxLines = 5;
        $shown    = $lines->take($maxLines);
        $extra    = $lines->count() - $shown->count();
        $rowspan  = $shown->count() + ($extra > 0 ? 1 : 0);

        // The acknowledgement block only prints when the scope is short enough to leave room.
        // Set to false to always hide it, or true to always show it.
        $showAck  = $shown->count() <= 2;

        $supplier = $po->customer;
        $tender   = $po->tender;
        $job      = $po->job;

        $fingerprint = strtoupper(substr(md5($po->po_number . $po->id), 0, 16));
    @endphp

    <table class="layout">
        <tr>
            {{-- ───────── Spine ───────── --}}
            <td class="spine">
                <div class="logo-tile"><img src="{{ $company['logo'] }}" alt="SLT"></div>
                <div class="sp-brand">Sri Lanka Telecom Services</div>
                <div class="sp-meta">
                    Commercial &amp; Finance Division<br>
                    Procurement<br>
                    Lotus Road, P.O. Box 503<br>
                    Colombo 01, Sri Lanka<br>
                    Tel: +94 11 232 9711<br>
                    VAT Reg: 114002847-7000
                </div>

                <div class="sp-gap"></div>

                <div class="sp-kicker">Document</div>
                <div class="sp-title">Purchase<br>Order</div>
                <div class="sp-ref">{{ $po->po_number }}</div>
                <div class="sp-status" style="color: {{ $statusColor }};">&bull; {{ $po->status }}</div>

                <div class="sp-rule"></div>

                <div class="sp-label">Date of Issue</div>
                <div class="sp-value">{{ $issueDate }}</div>

                <div class="sp-label">Valid Until</div>
                <div class="sp-value">{{ $validity }}</div>

                <div class="sp-label">Tender Reference</div>
                <div class="sp-value">{{ $tender->tender_number ?? 'N/A' }}</div>

                <div class="sp-label">Payment Terms</div>
                <div class="sp-value">Net 30 days<br>from invoice</div>

                <div class="sp-label">Currency</div>
                <div class="sp-value">LKR, Sri Lankan Rupees</div>

                <div class="sp-rule"></div>

                <table class="stats">
                    <tr>
                        <td>
                            <div class="st-num">30</div>
                            <div class="sp-label">Days valid</div>
                        </td>
                        <td>
                            <div class="st-num">{{ str_pad($lines->count(), 2, '0', STR_PAD_LEFT) }}</div>
                            <div class="sp-label">Line items</div>
                        </td>
                    </tr>
                </table>

                <div class="sp-rule"></div>

                <div class="sp-label">Verification</div>
                <div class="sp-small">{{ $fingerprint }}<br>PO-{{ str_pad($po->id, 6, '0', STR_PAD_LEFT) }}</div>
            </td>

            {{-- ───────── Main ───────── --}}
            <td class="main">
                <table>
                    <tr>
                        <td class="eyebrow">Certificate of Award</td>
                        <td class="issued">Issued {{ $issueDate }}</td>
                    </tr>
                </table>
                <table class="seg">
                    <tr>
                        <td style="width: 46px; background: #0072BC;"></td>
                        <td style="width: 22px; background: #8CC63F;"></td>
                        <td style="width: 10px; background: #cfe2f1;"></td>
                    </tr>
                </table>

                <div class="lead">Awarded to</div>
                <div class="name">{{ $supplier->name ?? 'Valued Supplier' }}</div>
                <div class="name-rule"></div>
                @if($tender && $tender->name)
                    <div class="for-lead">in recognition of the successful tender for</div>
                    <div class="for-name">{{ $tender->name }}</div>
                @endif

                <table class="value">
                    <tr>
                        <td class="v-bar"></td>
                        <td class="v-body">
                            <div class="v-label">Total Order Value</div>
                            <div class="v-amt"><span class="v-ccy">LKR</span> {{ number_format($po->po_amount, 2) }}</div>
                            <div class="v-words">Rupees {{ $amountWords }} Only &nbsp;|&nbsp; VAT and levies as per tax invoice</div>
                        </td>
                    </tr>
                </table>

                {{-- 01 Parties --}}
                <div class="sec"><span class="n">01</span>Parties</div>
                <table class="parties">
                    <tr>
                        <td class="party-cell l">
                            <div class="p-label">Supplier</div>
                            <div class="p-name">{{ $supplier->name ?? 'Valued Supplier' }}</div>
                            <div class="p-line">
                                @if($supplier && $supplier->contact_person)
                                    Attn: {{ $supplier->contact_person }}<br>
                                @endif
                                @if($supplier && $supplier->billing_address)
                                    {!! nl2br(e($supplier->billing_address)) !!}
                                @else
                                    Address on file
                                @endif
                                @if($supplier && $supplier->tax_number)
                                    <br>VAT / Tax ID: {{ $supplier->tax_number }}
                                @endif
                                @if($supplier && ($supplier->phone || $supplier->email))
                                    <br>{{ $supplier->phone ?: '-' }} &nbsp;|&nbsp; {{ $supplier->email ?: '-' }}
                                @endif
                            </div>
                        </td>
                        <td class="party-cell r">
                            <div class="p-label">Purchaser / Deliver To</div>
                            <div class="p-name">Sri Lanka Telecom Services Limited</div>
                            <div class="p-line">
                                Procurement Department<br>
                                Lotus Road, P.O. Box 503, Colombo 01
                                @if($job && $job->name)
                                    <br>Project: {{ $job->name }}
                                @endif
                                @if($po->billing_address)
                                    <br>Delivery / Site: {!! nl2br(e($po->billing_address)) !!}
                                @endif
                            </div>
                        </td>
                    </tr>
                </table>

                {{-- 02 Scope --}}
                <div class="sec"><span class="n">02</span>Scope of Supply</div>
                <table class="scope">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th style="width: 68%;">Description</th>
                            <th class="tar">Amount (LKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($shown as $index => $line)
                            <tr>
                                <td class="no">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $line }}</td>
                                @if ($index === 0)
                                    <td class="tar amt" rowspan="{{ $rowspan }}">{{ number_format($po->po_amount, 2) }}</td>
                                @endif
                            </tr>
                        @endforeach
                        @if ($extra > 0)
                            <tr>
                                <td></td>
                                <td class="more">+ {{ $extra }} more line item{{ $extra > 1 ? 's' : '' }} recorded in the purchase order</td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                {{-- 03 Terms --}}
                <div class="sec"><span class="n">03</span>Terms &amp; Conditions</div>
                <table class="terms">
                    <tr>
                        <td class="l">
                            <div class="term"><b>01</b>Prices exclude VAT and levies unless stated.</div>
                            <div class="term"><b>02</b>Deliver to the nominated address on schedule.</div>
                            <div class="term"><b>03</b>Payment Net 30 days from a valid tax invoice.</div>
                            <div class="term"><b>04</b>Valid until the date shown, then lapses.</div>
                        </td>
                        <td class="r">
                            <div class="term"><b>05</b>Supplier to acknowledge in writing and confirm delivery.</div>
                            <div class="term"><b>06</b>Supplies must meet tender specs and SL Standards.</div>
                            <div class="term"><b>07</b>Purchaser may terminate for default on notice.</div>
                            <div class="term"><b>08</b>Governed by the laws of Sri Lanka.</div>
                        </td>
                    </tr>
                </table>

                {{-- 04 Authorisation --}}
                <div class="sec"><span class="n">04</span>Authorisation</div>
                <table class="sign">
                    <tr>
                        <td class="s">
                            <div class="sig-line"></div>
                            <div class="sig-role">Prepared By</div>
                            <div class="sig-sub">Procurement Officer</div>
                            <div class="sig-date">Date</div>
                        </td>
                        <td class="s">
                            <div class="sig-line"></div>
                            <div class="sig-role">Reviewed By</div>
                            <div class="sig-sub">Finance Controller</div>
                            <div class="sig-date">Date</div>
                        </td>
                        <td class="s">
                            <div class="sig-line"></div>
                            <div class="sig-role">Approved By</div>
                            <div class="sig-sub">Authorised Signatory</div>
                            <div class="sig-date">Date</div>
                        </td>
                        <td class="seal-cell">
                            <div class="seal-out">
                                <div class="seal-in">
                                    <table class="seal-tbl">
                                        <tr>
                                            <td>
                                                <div class="seal-top">SLT PROCUREX</div>
                                                <div class="seal-mid">AWARDED</div>
                                                <div class="seal-bar"></div>
                                                <div class="seal-top">OFFICIAL</div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>

                {{-- 05 Supplier acknowledgement (prints only when there is room) --}}
                @if ($showAck)
                    <div class="sec"><span class="n">05</span>Supplier Acknowledgement</div>
                    <table>
                        <tr>
                            <td style="width: 66%; padding-right: 16px;">
                                <div class="ack-text">
                                    We acknowledge receipt of this Purchase Order and accept the terms stated above.
                                </div>
                                <table class="ack-f">
                                    <tr>
                                        <td style="width: 38%;"><div class="ack-line"></div><div class="ack-lab">Name</div></td>
                                        <td style="width: 38%;"><div class="ack-line"></div><div class="ack-lab">Signature</div></td>
                                        <td><div class="ack-line"></div><div class="ack-lab">Date</div></td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width: 34%;">
                                <div class="stamp">Company stamp</div>
                            </td>
                        </tr>
                    </table>
                @endif
            </td>
        </tr>
    </table>

    {{-- Footer --}}
    <table class="footer">
        <tr>
            <td class="fl">
                <span class="g">SLT PROCUREX</span> &nbsp;|&nbsp; Procurement Division &nbsp;|&nbsp; Generated {{ now()->format('d M Y, H:i') }}
            </td>
            <td class="fr">{{ $po->po_number }}</td>
        </tr>
    </table>
    <table class="stripe"><tr><td class="b"></td><td class="g2"></td></tr></table>
</body>
</html>