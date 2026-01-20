<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TaxInvoice;
use App\Models\Invoice;

class TaxInvoiceController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'tax_invoice_number' => 'required|string|unique:tax_invoices,tax_invoice_number',
            'tax_percentage' => 'required|numeric|min:0',
            'tax_amount' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $invoice = Invoice::findOrFail($data['invoice_id']);

        if ($invoice->status !== Invoice::STATUS_SENT) {
            return response()->json([
                'message' => 'Invoice must be Sent before generating tax invoice'
            ], 422);
        }

        if ($invoice->taxInvoice) {
            return response()->json([
                'message' => 'Tax invoice already exists for this invoice'
            ], 422);
        }

        $taxInvoice = TaxInvoice::create([
            ...$data,
            'locked' => true,
        ]);

        $invoice->update([
            'status' => Invoice::STATUS_TAX_GENERATED,
        ]);

        return response()->json(
            $taxInvoice->load('invoice'),
            201
        );
    }
}
