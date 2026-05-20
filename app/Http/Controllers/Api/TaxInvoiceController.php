<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TaxInvoice;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

use App\Services\InvoiceWorkflowService;
use Illuminate\Support\Facades\Auth;

class TaxInvoiceController extends Controller
{
    protected $workflow;

    public function __construct(InvoiceWorkflowService $workflow)
    {
        $this->workflow = $workflow;
    }

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

        if ($invoice->status !== Invoice::STATUS_DRAFT) {
            return response()->json([
                'message' => 'Invoice must be in Draft before generating tax invoice'
            ], 422);
        }

        if ($invoice->taxInvoice) {
            return response()->json([
                'message' => 'Tax invoice already exists for this invoice'
            ], 422);
        }

        $taxInvoice = DB::transaction(function () use ($data, $invoice) {
            $taxInvoice = TaxInvoice::create([
                ...$data,
                'locked' => true,
            ]);

            $this->workflow->transitionTo(
                $invoice,
                Invoice::STATUS_TAX_GENERATED,
                Auth::user(),
                "Tax Invoice #{$taxInvoice->tax_invoice_number} generated"
            );

            return $taxInvoice;
        });

        return response()->json(
            $taxInvoice->load('invoice'),
            201
        );
    }
}
