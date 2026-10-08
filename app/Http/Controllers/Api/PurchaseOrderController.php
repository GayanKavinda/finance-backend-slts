<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderController extends Controller
{
    // List purchase orders
    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['customer', 'tender', 'job']);

        if ($request->job_id) {
            $query->where('job_id', $request->job_id);
        }

        if ($request->tender_id) {
            $query->where('tender_id', $request->tender_id);
        }

        if ($request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where('po_number', 'like', "%{$request->search}%");
        }

        return $query->latest()->paginate(15);
    }

    // Procurement summary statistics
    public function stats(Request $request)
    {
        $query = PurchaseOrder::query();

        if ($request->job_id) {
            $query->where('job_id', $request->job_id);
        }

        $total = (clone $query)->count();
        $committed = (clone $query)->sum('po_amount');

        $byStatus = (clone $query)
            ->selectRaw('status, COUNT(*) as count, SUM(po_amount) as amount')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn ($row) => [
                $row->status => [
                    'count' => (int) $row->count,
                    'amount' => (float) $row->amount,
                ],
            ]);

        return response()->json([
            'total' => $total,
            'committed_value' => (float) $committed,
            'by_status' => $byStatus,
        ]);
    }

    // Create purchase order
    public function store(Request $request)
    {
        $data = $request->validate([
            'po_number' => 'required|string|unique:purchase_orders,po_number',
            'po_date' => 'required|date',
            'po_description' => 'nullable|string',
            'po_amount' => 'nullable|numeric|min:0',
            'billing_address' => 'required|string',
            'job_id' => 'required|exists:project_jobs,id',
            'tender_id' => 'required|exists:tenders,id',
            'customer_id' => 'required|exists:customers,id',
            'status' => 'nullable|in:' . implode(',', PurchaseOrder::statusList()),
        ]);

        $data['po_amount'] = $data['po_amount'] ?? 0;

        $data['status'] = $data['status'] ?? PurchaseOrder::STATUS_DRAFT;

        $po = PurchaseOrder::create($data);

        $this->logStatusChange($po, null, $po->status, 'Purchase order created');

        return response()->json(
            $po->load(['customer', 'tender', 'job']),
            201
        );
    }

    // Show single PO
    public function show($id)
    {
        return PurchaseOrder::with(['customer', 'tender', 'job', 'invoice'])->findOrFail($id);
    }

    // Update PO
    public function update(Request $request, $id)
    {
        $po = PurchaseOrder::findOrFail($id);

        // Can't edit if invoices exist
        if ($po->invoice()->count() > 0) {
            return response()->json([
                'message' => 'Cannot edit PO with existing invoices'
            ], 422);
        }

        $data = $request->validate([
            'po_date' => 'required|date',
            'po_description' => 'nullable|string',
            'po_amount' => 'nullable|numeric|min:0',
            'billing_address' => 'required|string',
            'status' => 'nullable|in:' . implode(',', PurchaseOrder::statusList()),
            'reason' => 'nullable|string|max:500',
        ]);

        $newStatus = $data['status'] ?? $po->status;

        if (!$po->canTransitionTo($newStatus)) {
            return response()->json([
                'message' => "Invalid status transition: a {$po->status} purchase order cannot move to {$newStatus}."
            ], 422);
        }

        $oldStatus = $po->status;

        $po->update($data);

        if ($oldStatus !== $po->status) {
            $this->logStatusChange($po, $oldStatus, $po->status, $data['reason'] ?? null);
        }

        return response()->json($po->load(['customer', 'tender', 'job']));
    }

    // Delete PO
    public function destroy($id)
    {
        $po = PurchaseOrder::findOrFail($id);

        if ($po->invoice()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete PO with existing invoices'
            ], 422);
        }

        $po->delete();

        return response()->json(['message' => 'Purchase order deleted']);
    }

    // Audit trail (status lifecycle history)
    public function getAuditTrail($id)
    {
        $po = PurchaseOrder::findOrFail($id);

        $history = $po->statusHistory()
            ->with('user:id,name')
            ->get();

        return response()->json($history);
    }

    protected function logStatusChange(PurchaseOrder $po, ?string $oldStatus, string $newStatus, ?string $reason = null): void
    {
        PurchaseOrderStatusHistory::create([
            'purchase_order_id' => $po->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by' => Auth::id(),
            'reason' => $reason,
        ]);
    }
}
