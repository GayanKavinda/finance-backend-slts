<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;

class InvoiceController extends Controller
{
    // List invoices with filters
    public function index(Request $request)
    {
        $query = Invoice::with(['purchaseOrder', 'customer', 'taxInvoice'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $allowedStatuses = [
                Invoice::STATUS_DRAFT,
                Invoice::STATUS_TAX_GENERATED,
                Invoice::STATUS_SUBMITTED,
                Invoice::STATUS_PAID,
            ];

            if (in_array($request->status, $allowedStatuses)) {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        return response()->json(
            $query->paginate(10)
        );
    }

    // Create invoice (any invoice-authorized user)
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
            'status' => Invoice::STATUS_DRAFT,
        ]);

        return response()->json(
            $invoice->load(['purchaseOrder', 'customer']),
            201
        );
    }

    // Submit invoice to finance
    public function submitToFinance($id)
    {
        $invoice = Invoice::findOrFail($id);

        if ($invoice->status !== Invoice::STATUS_TAX_GENERATED) {
            return response()->json([
                'message' => 'Invoice must have tax generated first'
            ], 422);
        }

        $invoice->update([
            'status' => Invoice::STATUS_SUBMITTED,
        ]);

        return response()->json($invoice);
    }

    // Mark invoice as paid (Finance only later via middleware)
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
