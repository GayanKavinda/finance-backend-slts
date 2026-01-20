<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;

class PurchaseOrderController extends Controller
{
    // List purchase orders
    public function index()
    {
        return PurchaseOrder::with(['customer', 'tender'])
            ->latest()
            ->get();
    }

    // Create purchase order
    public function store(Request $request)
    {
        $data = $request->validate([
            'po_number' => 'required|string|unique:purchase_orders,po_number',
            'po_description' => 'nullable|string',
            'po_amount' => 'required|numeric|min:0',
            'billing_address' => 'nullable|string',
            'tender_id' => 'nullable|exists:tenders,id',
            'customer_id' => 'required|exists:customers,id',
        ]);

        $po = PurchaseOrder::create([
            ...$data,
            'status' => PurchaseOrder::STATUS_APPROVED,
        ]);

        return response()->json(
            $po->load(['customer', 'tender']),
            201
        );
    }

    // Show single PO
    public function show($id)
    {
        return PurchaseOrder::with(['customer', 'tender', 'invoice'])
            ->findOrFail($id);
    }
}
