<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;

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

    // Create purchase order
    public function store(Request $request)
    {
        $data = $request->validate([
            'po_number' => 'required|string|unique:purchase_orders,po_number',
            'po_description' => 'nullable|string',
            'po_amount' => 'required|numeric|min:0',
            'billing_address' => 'required|string',
            'job_id' => 'required|exists:project_jobs,id',
            'tender_id' => 'required|exists:tenders,id',
            'customer_id' => 'required|exists:customers,id',
            'status' => 'nullable|in:Draft,Approved',
        ]);

        $data['status'] = $data['status'] ?? PurchaseOrder::STATUS_DRAFT;

        $po = PurchaseOrder::create($data);

        return response()->json(
            $po->load(['customer', 'tender', 'job']),
            201
        );
    }

    // Show single PO
    public function show($id)
    {
        return PurchaseOrder::with(['customer', 'tender', 'job', 'invoice'])
            ->findOrFail($id);
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
            'po_description' => 'nullable|string',
            'po_amount' => 'required|numeric|min:0',
            'billing_address' => 'required|string',
            'status' => 'nullable|in:Draft,Approved',
        ]);

        $po->update($data);

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
}
