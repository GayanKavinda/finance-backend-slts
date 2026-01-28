<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;

class InvoiceSummaryController extends Controller
{
    public function index()
    {
        return response()->json([
            'total_invoices' => Invoice::count(),

            'paid_invoices' => Invoice::where('status', Invoice::STATUS_PAID)->count(),

            'pending_invoices' => Invoice::whereIn('status', [
                Invoice::STATUS_TAX_GENERATED,
                Invoice::STATUS_SUBMITTED,
            ])->count(),

            'gross_amount' => Invoice::sum('invoice_amount'),

            'paid_amount' => Invoice::where('status', Invoice::STATUS_PAID)
                ->sum('invoice_amount'),
        ]);
    }
}
