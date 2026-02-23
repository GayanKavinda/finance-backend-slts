<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Invoice;
use App\Services\InvoiceWorkflowService;

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
            'purchaseOrder.tender',
            'customer',
            'submittedBy',
            'approvedBy'
        ]);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->search) {
            $query->where('invoice_number', 'like', "%{$request->search}%");
        }

        return $query->latest()->paginate(15);
    }

    // Show single invoice
    public function show($id)
    {
        return Invoice::with([
            'purchaseOrder.tender',
            'customer',
            'statusHistory.user',
            'submittedBy',
            'approvedBy',
            'rejectedBy'
        ])->findOrFail($id);
    }

    // Store new invoice
    public function store(Request $request)
    {
        $validated = $request->validate([
            'po_id' => 'required|exists:purchase_orders,id',
            'customer_id' => 'required|exists:customers,id',
            'invoice_number' => 'required|string|unique:invoices,invoice_number',
            'invoice_amount' => 'nullable|numeric|min:0',
            'invoice_date' => 'required|date',
        ]);

        $validated['invoice_amount'] = $validated['invoice_amount'] ?? 0;

        $invoice = Invoice::create([
            ...$validated,
            'status' => Invoice::STATUS_DRAFT,
            'submitted_by' => Auth::id(),
            'submitted_at' => now(),
        ]);

        return response()->json($invoice, 201);
    }

    // Update invoice
    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        if ($invoice->status !== Invoice::STATUS_DRAFT && $invoice->status !== Invoice::STATUS_REJECTED) {
            return response()->json(['message' => 'Only draft or rejected invoices can be edited'], 422);
        }

        $validated = $request->validate([
            'invoice_amount' => 'nullable|numeric|min:0',
            'invoice_date' => 'required|date',
        ]);

        $invoice->update($validated);

        return response()->json($invoice);
    }

    // Submit to finance
    public function submitToFinance($id)
    {
        $invoice = Invoice::findOrFail($id);
        $this->workflow->transitionTo($invoice, Invoice::STATUS_SUBMITTED, Auth::user());
        return response()->json(['message' => 'Invoice submitted to finance']);
    }

    // Approve
    public function approveInvoice($id)
    {
        $invoice = Invoice::findOrFail($id);
        $this->workflow->transitionTo($invoice, Invoice::STATUS_APPROVED, Auth::user());
        return response()->json(['message' => 'Invoice approved']);
    }

    // Reject
    public function rejectInvoice(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        $request->validate(['reason' => 'required|string']);
        $this->workflow->transitionTo($invoice, Invoice::STATUS_REJECTED, Auth::user(), $request->reason);
        return response()->json(['message' => 'Invoice rejected']);
    }

    public function monthlyTrend()
    {
        $bankedStatus = Invoice::STATUS_BANKED;

        $data = Invoice::selectRaw("
                DATE_FORMAT(invoice_date, '%b') as month,
                SUM(COALESCE(invoice_amount, 0)) as total_amount,
                SUM(CASE WHEN status = '$bankedStatus' THEN COALESCE(invoice_amount, 0) ELSE 0 END) as paid_amount,
                SUM(CASE WHEN status != '$bankedStatus' THEN COALESCE(invoice_amount, 0) ELSE 0 END) as pending_amount
            ")
            ->groupByRaw("MONTH(invoice_date), DATE_FORMAT(invoice_date, '%b')")
            ->orderByRaw("MONTH(invoice_date)")
            ->get(); // Removed toBase() for easier debugging if needed

        return response()->json($data);
    }

    public function statusBreakdown()
    {
        $breakdown = Invoice::selectRaw('status, count(*) as count, sum(invoice_amount) as value')
            ->groupBy('status')
            ->get();

        return response()->json($breakdown);
    }

    /**
     * Dashboard metrics
     */
    public function executiveSummary()
    {
        return response()->json([
            'total_tender_value' => \App\Models\Tender::sum('budget') ?: \App\Models\Tender::sum('awarded_amount'),
            'total_po_value' => \App\Models\PurchaseOrder::sum('po_amount'),
            'total_invoices' => Invoice::count(),
            'pending_approval_count' => Invoice::where('status', Invoice::STATUS_SUBMITTED)->count(),
            'payment_received_count' => Invoice::where('status', Invoice::STATUS_PAYMENT_RECEIVED)->count(),
            'paid_count' => Invoice::where('status', Invoice::STATUS_BANKED)->count(),
            'rejected_count' => Invoice::where('status', Invoice::STATUS_REJECTED)->count(),
            'gross_amount' => Invoice::sum('invoice_amount'),
            'banked_amount' => Invoice::where('status', Invoice::STATUS_BANKED)->sum('payment_amount'),
            'pending_amount' => Invoice::whereNotIn('status', [Invoice::STATUS_BANKED, Invoice::STATUS_REJECTED])->sum('invoice_amount'),
            'avg_approval_time_hours' => 0, // Temporarily disabled due to missing updated_at column in history table
        ]);
    }

    /**
     * Finance records cheque details when customer brings payment
     */
    public function recordPayment(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        if ($invoice->status !== Invoice::STATUS_APPROVED) {
            return response()->json([
                'message' => 'Only approved invoices can receive payment'
            ], 422);
        }

        $validated = $request->validate([
            'cheque_number' => 'required|string|max:50',
            'bank_name' => 'required|string|max:100',
            'payment_amount' => 'required|numeric|min:0',
            'payment_received_date' => 'required|date',
        ]);

        // Generate receipt number: RCP-YYYY-XXXX
        $receiptNumber = 'RCP-' . date('Y') . '-' . str_pad(
            Invoice::where('receipt_number', 'like', 'RCP-' . date('Y') . '-%')->count() + 1,
            4,
            '0',
            STR_PAD_LEFT
        );

        $invoice->update([
            'cheque_number' => $validated['cheque_number'],
            'bank_name' => $validated['bank_name'],
            'payment_amount' => $validated['payment_amount'],
            'payment_received_date' => $validated['payment_received_date'],
            'receipt_number' => $receiptNumber,
            'recorded_by' => Auth::id(),
        ]);

        $this->workflow->transitionTo(
            $invoice,
            Invoice::STATUS_PAYMENT_RECEIVED,
            Auth::user(),
            "Payment received: Cheque {$validated['cheque_number']} from {$validated['bank_name']}"
        );

        return response()->json([
            'message' => 'Payment recorded. Internal receipt generated.',
            'receipt_number' => $invoice->receipt_number,
            'invoice' => $invoice->load(['customer', 'purchaseOrder'])
        ]);
    }

    /**
     * Finance marks as banked after depositing cheque
     */
    public function markAsBanked(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        if ($invoice->status !== Invoice::STATUS_PAYMENT_RECEIVED) {
            return response()->json([
                'message' => 'Only payment-received invoices can be marked as banked'
            ], 422);
        }

        $validated = $request->validate([
            'banked_at' => 'required|date',
            'bank_reference' => 'nullable|string|max:100',
        ]);

        $invoice->update([
            'is_banked' => true,
            'banked_at' => $validated['banked_at'],
            'bank_reference' => $validated['bank_reference'] ?? null,
        ]);

        $this->workflow->transitionTo(
            $invoice,
            Invoice::STATUS_BANKED,
            Auth::user(),
            "Marked as banked on {$validated['banked_at']}"
        );

        return response()->json([
            'message' => 'Invoice marked as banked. Transaction complete.',
            'invoice' => $invoice->load(['customer', 'purchaseOrder'])
        ]);
    }

    /**
     * Get the audit trail (status history) for an invoice
     */
    public function getAuditTrail($id)
    {
        $invoice = Invoice::findOrFail($id);

        $history = $invoice->statusHistory()
            ->with('user:id,name')
            ->get();

        return response()->json($history);
    }
}
