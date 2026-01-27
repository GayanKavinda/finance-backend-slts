<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;

class InvoiceSummaryController extends Controller
{
    public function index()
    {
        $paidInvoices = Invoice::with('taxInvoice')
            ->where('status', Invoice::STATUS_PAID)
            ->get();

        return response()->json([
            'total_invoices' => Invoice::count(),
            'paid_invoices' => $paidInvoices->count(),
            'pending_invoices' => Invoice::whereIn('status', [
                Invoice::STATUS_TAX_GENERATED,
                Invoice::STATUS_SUBMITTED,
            ])->count(),

            'paid_amount' => $paidInvoices->sum(fn($i) => $i->total_amount),
        ]);
    }
}
