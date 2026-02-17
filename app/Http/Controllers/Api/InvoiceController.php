<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Services\InvoiceWorkflowService;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    protected $workflow;

    public function __construct(InvoiceWorkflowService $workflow)
    {
        $this->workflow = $workflow;
    }

    // List invoices with filters
    public function index(Request $request)
    {
        $query = Invoice::with([
            'purchaseOrder',
            'customer',
            'taxInvoice',
            'submitter:id,name',
            'approver:id,name',
            'rejecter:id,name',
            'recordedBy:id,name'
        ])->orderByDesc('created_at');

        if ($request->filled('search')) {
            $query->where('invoice_number', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        return response()->json(
            $query->paginate(10)
        );
    }

    // Create invoice (any invoice-authorized user)
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

        $invoice = DB::transaction(function () use ($data) {
            $invoice = Invoice::create([
                ...$data,
                'status' => Invoice::STATUS_DRAFT,
            ]);

            \App\Models\InvoiceStatusHistory::create([
                'invoice_id' => $invoice->id,
                'old_status' => null,
                'new_status' => Invoice::STATUS_DRAFT,
                'changed_by' => auth()->id(),
                'metadata' => [
                    'action' => 'created',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->header('User-Agent'),
                ],
            ]);

            return $invoice;
        });

        return response()->json(
            $invoice->load(['purchaseOrder', 'customer']),
            201
        );
    }

    // Update invoice (any invoice-authorized user)
    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        if ($invoice->status !== Invoice::STATUS_DRAFT) {
            return response()->json([
                'message' => 'Invoice must be in draft status to update'
            ], 403);
        }

        $data = $request->validate([
            'po_id' => 'required|exists:purchase_orders,id',
            'customer_id' => 'required|exists:customers,id',
            'invoice_amount' => 'required|numeric|min:0',
            'invoice_date' => 'required|date',
        ]);

        $invoice->update($data);

        return response()->json(
            $invoice->load(['customer', 'purchaseOrder']),
            200
        );
    }

    // Submit invoice to finance
    public function submitToFinance($id)
    {
        $invoice = Invoice::findOrFail($id);
        $this->workflow->submit($invoice);

        return response()->json($invoice->fresh()->load([
            'submitter',
            'customer',
            'purchaseOrder',
            'taxInvoice'
        ]));
    }

    // New: Approve invoice (Finance role)
    public function approveInvoice($id)
    {
        $invoice = Invoice::findOrFail($id);
        $this->workflow->approve($invoice);

        return response()->json($invoice->fresh()->load([
            'approver',
            'submitter',
            'customer',
            'purchaseOrder',
            'taxInvoice'
        ]));
    }

    // New: Reject invoice (Finance role)
    public function rejectInvoice(Request $request, $id)
    {
        $data = $request->validate([
            'rejection_reason' => 'required|string|min:10|max:1000',
        ]);

        $invoice = Invoice::findOrFail($id);
        $this->workflow->reject($invoice, $data['rejection_reason']);

        return response()->json($invoice->fresh()->load([
            'rejecter',
            'submitter',
            'customer',
            'purchaseOrder',
            'taxInvoice'
        ]));
    }

    // Mark invoice as paid (Finance only)
    public function markPaid(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        $data = $request->validate([
            'payment_reference' => 'required|string|max:255',
            'payment_method' => 'required|string|max:100',
            'payment_notes' => 'nullable|string',
        ]);

        $this->workflow->markPaid($invoice, $data);

        return response()->json(
            $invoice->fresh()->load([
                'recordedBy',
                'approver',
                'submitter',
                'customer',
                'purchaseOrder',
                'taxInvoice'
            ])
        );
    }

    // New: Get audit trail for an invoice
    public function getAuditTrail($id)
    {
        $invoice = Invoice::findOrFail($id);

        $history = $invoice->statusHistory()
            ->with('user:id,name,email')
            ->get();

        return response()->json($history);
    }

    public function monthlyTrend()
    {
        $paidStatus = Invoice::STATUS_PAID;

        $data = Invoice::selectRaw("
                DATE_FORMAT(invoice_date, '%b') as month,
                SUM(COALESCE(invoice_amount, 0)) as total_amount,
                SUM(CASE WHEN status = '$paidStatus' THEN COALESCE(invoice_amount, 0) ELSE 0 END) as paid_amount,
                SUM(CASE WHEN status != '$paidStatus' THEN COALESCE(invoice_amount, 0) ELSE 0 END) as pending_amount
            ")
            ->groupByRaw("MONTH(invoice_date), DATE_FORMAT(invoice_date, '%b')")
            ->orderByRaw("MONTH(invoice_date)")
            ->toBase()
            ->get();

        return response()->json($data);
    }

    public function summary()
    {
        $paidStatus = Invoice::STATUS_PAID;
        $submittedStatus = Invoice::STATUS_SUBMITTED;
        $rejectedStatus = Invoice::STATUS_REJECTED;

        $data = Invoice::selectRaw("
            COUNT(*) as total_invoices,
            SUM(CASE WHEN status = '$paidStatus' THEN 1 ELSE 0 END) as paid_invoices,
            SUM(CASE WHEN status = '$submittedStatus' THEN 1 ELSE 0 END) as pending_approval_count,
            SUM(CASE WHEN status = '$rejectedStatus' THEN 1 ELSE 0 END) as rejected_count,
            SUM(COALESCE(invoice_amount, 0)) as gross_amount,
            SUM(CASE WHEN status = '$paidStatus' THEN COALESCE(invoice_amount, 0) ELSE 0 END) as paid_amount,
            SUM(CASE WHEN status != '$paidStatus' THEN COALESCE(invoice_amount, 0) ELSE 0 END) as pending_amount
        ")
            ->toBase()
            ->first();

        return response()->json($data);
    }

    public function statusBreakdown()
    {
        $data = Invoice::selectRaw('status, COUNT(*) as count')->groupBy('status')->get();

        return response()->json($data);
    }
}
