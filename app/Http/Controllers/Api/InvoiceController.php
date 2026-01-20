<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\PurchaseOrder;

class InvoiceController extends Controller
{
    // Invoices
    public function index()
    {
        return Invoice::with(['purchaseOrder', 'customer', 'taxInvoice'])
            ->latest()
            ->get();
    }

    //create invoice purchase order
    public function store(Request $request)
    {
        $data = $request->validate([
            'po_id' => 'required|exists:purchase_orders,id',
            'customer_id' => 'required|exists:customers,id',
            'invoice_number' => 'required|string|unique:invoices,invoice_number',
            'invoice_amount' => 'required|numeric|min:0',
            'invoice_date' => 'required|date',
        ]);

        $invoice = Invoice::create([
            ...$data,
            'status' => Invoice::STATUS_SENT,
        ]);

        return response()->json(
            $invoice->load(['purchaseOrder', 'customer']),
            201
        );
    }

    //Submit invoice to finance
    public function submitToFinance($id)
    {
        $invoice = Invoice::findOrFail($id);

        if ($invoice->status !== Invoice::STATUS_TAX_GENERATED) {
            return response()->json([
                'message' => 'Tax invoice must be generated first'
            ], 422);
        }

        $invoice->update([
            'status' => Invoice::STATUS_SUBMITTED,
        ]);

        return response()->json($invoice);
    }

    //Mark invoice as Paid
    public function markPaid($id)
    {
        $invoice = Invoice::findOrFail($id);

        if ($invoice->status !== Invoice::STATUS_SUBMITTED) {
            return response()->json([
                'message' => 'Invoice must be submitted to finance first'
            ], 422);
        }

        $invoice->update([
            'status' => Invoice::STATUS_PAID,
        ]);

        return response()->json($invoice);
    }
}
