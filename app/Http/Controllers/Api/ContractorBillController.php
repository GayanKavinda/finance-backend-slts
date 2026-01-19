<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContractorBill;

class ContractorBillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ContractorBill::with(['job.tender', 'contractor'])->latest()->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'job_id' => 'required|exists:project_jobs,id',
            'contractor_id' => 'required|exists:contractors,id',
            'bill_number' => 'required|string|max:100|unique:contractor_bills,bill_number',
            'amount' => 'required|numeric|min:0',
            'bill_date' => 'required|date',
            'document_path' => 'required|string',
        ]);

        $bill = ContractorBill::create($data);

        return response()->json($bill->load(['job', 'contractor']), 201);
    }

    /**
     * Verify a Contractor Bill
     */
    public function verify(Request $request, $id)
    {
        $bill = ContractorBill::findOrFail($id);
        
        if ($bill->status !== ContractorBill::STATUS_UPLOADED) {
            return response()->json([
                'message' => 'Only uploaded bills can be verified'
            ], 422);
        }

        $bill->update([
            'status' => ContractorBill::STATUS_VERIFIED,
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        return response()->json($bill->load(['job', 'contractor', 'verifier']));
    }

    /**
     * Approve a contractor bill
     */
    public function approve($id)
    {
        $bill = ContractorBill::findOrFail($id);

        if ($bill->status !== ContractorBill::STATUS_VERIFIED) {
            return response()->json([
                'message' => 'Bill must be verified before approval'
            ], 422);
        }

        $bill->update([
            'status' => ContractorBill::STATUS_APPROVED,
        ]);

        return response()->json($bill->load(['job', 'contractor']));
    }
}