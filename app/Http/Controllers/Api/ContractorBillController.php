<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContractorBill;
use App\Models\ContractorBillDocument;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Services\ContractorNotificationService;

class ContractorBillController extends Controller
{
    protected $notifications;

    public function __construct(ContractorNotificationService $notifications)
    {
        $this->notifications = $notifications;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ContractorBill::with(['job', 'contractor', 'documents'])->latest()->get();
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
            'amount' => 'nullable|numeric|min:0',
            'bill_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $data['amount'] = $data['amount'] ?? 0;

        $data['status'] = ContractorBill::STATUS_DRAFT;
        $data['document_path'] = ''; // Legacy field compatibility

        $bill = ContractorBill::create($data);

        return response()->json($bill->load(['job', 'contractor']), 201);
    }

    /**
     * Upload a document to a bill
     */
    public function uploadDocument(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB
            'document_type' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $bill = ContractorBill::findOrFail($id);

        $file = $request->file('file');
        $path = $file->store("contractor_bills/{$bill->id}", 'public');

        $document = ContractorBillDocument::create([
            'contractor_bill_id' => $bill->id,
            'document_type' => $request->document_type,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'uploaded_by' => $request->user()->id,
            'description' => $request->description,
        ]);

        return response()->json($document, 201);
    }

    /**
     * Delete a document
     */
    public function deleteDocument($id)
    {
        $document = ContractorBillDocument::findOrFail($id);
        $bill = $document->bill;

        if ($bill->status !== ContractorBill::STATUS_DRAFT) {
            return response()->json(['message' => 'Cannot delete documents after verification'], 422);
        }

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return response()->json(['message' => 'Document deleted successfully']);
    }

    /**
     * Verify a Contractor Bill
     */
    public function verify(Request $request, $id)
    {
        $bill = ContractorBill::findOrFail($id);

        if ($bill->status !== ContractorBill::STATUS_DRAFT) {
            return response()->json([
                'message' => 'Only draft bills can be verified'
            ], 422);
        }

        if ($bill->job->status !== \App\Models\ProjectJob::STATUS_COMPLETED) {
            return response()->json([
                'message' => "Job '{$bill->job->name}' must be marked as Completed before verifying contractor bills."
            ], 422);
        }

        if ($bill->documents()->count() === 0) {
            return response()->json([
                'message' => 'At least one document (e.g., Contractor Bill) is required'
            ], 422);
        }

        $bill->update([
            'status' => ContractorBill::STATUS_VERIFIED,
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        $this->notifications->notifyBillStatusChanged($bill, ContractorBill::STATUS_VERIFIED, $request->user());

        return response()->json($bill->load(['job', 'contractor', 'verifier', 'documents']));
    }

    /**
     * Submit a Contractor Bill to Finance
     */
    public function submit(Request $request, $id)
    {
        $bill = ContractorBill::findOrFail($id);

        if ($bill->status !== ContractorBill::STATUS_VERIFIED) {
            return response()->json([
                'message' => 'Only verified bills can be submitted to finance'
            ], 422);
        }

        $bill->update([
            'status' => ContractorBill::STATUS_SUBMITTED,
            'submitted_by' => $request->user()->id,
            'submitted_at' => now(),
        ]);

        $this->notifications->notifyBillStatusChanged($bill, ContractorBill::STATUS_SUBMITTED, $request->user());

        return response()->json($bill->load(['job', 'contractor', 'submitter', 'documents']));
    }

    /**
     * Approve a contractor bill (Finance)
     */
    public function approve(Request $request, $id)
    {
        $bill = ContractorBill::findOrFail($id);

        if ($bill->status !== ContractorBill::STATUS_SUBMITTED) {
            return response()->json([
                'message' => 'Bill must be submitted to finance before approval'
            ], 422);
        }

        $bill->update([
            'status' => ContractorBill::STATUS_APPROVED,
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        $this->notifications->notifyBillStatusChanged($bill, ContractorBill::STATUS_APPROVED, $request->user());

        return response()->json($bill->load(['job', 'contractor', 'approver', 'documents']));
    }

    /**
     * Reject a contractor bill
     */
    public function reject(Request $request, $id)
    {
        $bill = ContractorBill::findOrFail($id);

        $allowed = [
            ContractorBill::STATUS_SUBMITTED,
            ContractorBill::STATUS_APPROVED
        ];

        if (!in_array($bill->status, $allowed)) {
            return response()->json([
                'message' => 'Only submitted or approved bills can be rejected'
            ], 422);
        }

        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $bill->update([
            'status' => ContractorBill::STATUS_REJECTED,
            'rejected_by' => $request->user()->id,
            'rejected_at' => now(),
            'rejection_reason' => $request->reason,
        ]);

        $this->notifications->notifyBillStatusChanged($bill, ContractorBill::STATUS_REJECTED, $request->user());

        return response()->json($bill->load(['job', 'contractor', 'rejecter', 'documents']));
    }

    /**
     * Mark a contractor bill as Paid (Finance)
     */
    public function pay(Request $request, $id)
    {
        $bill = ContractorBill::findOrFail($id);

        if ($bill->status !== ContractorBill::STATUS_APPROVED) {
            return response()->json([
                'message' => 'Bill must be approved before payment'
            ], 422);
        }

        $request->validate([
            'payment_reference' => 'required|string|max:255',
            'paid_at' => 'required|date',
        ]);

        $bill->update([
            'status' => ContractorBill::STATUS_PAID,
            'payment_reference' => $request->payment_reference,
            'paid_at' => $request->paid_at,
        ]);

        $this->notifications->notifyBillStatusChanged($bill, ContractorBill::STATUS_PAID, $request->user());

        return response()->json($bill->load(['job', 'contractor', 'documents']));
    }
}
