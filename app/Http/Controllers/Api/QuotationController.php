<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContractorQuotation;
use App\Models\ProjectJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\ContractorNotificationService;

class QuotationController extends Controller
{
    protected $notifications;

    public function __construct(ContractorNotificationService $notifications)
    {
        $this->notifications = $notifications;
    }

    /**
     * List quotations for a specific job
     */
    public function listByJob($jobId)
    {
        return response()->json(
            ContractorQuotation::with('contractor')
                ->where('job_id', $jobId)
                ->latest()
                ->get()
        );
    }

    /**
     * Store a new quotation
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'job_id' => 'required|exists:project_jobs,id',
            'contractor_id' => 'required|exists:contractors,id',
            'quotation_amount' => 'nullable|numeric|min:0',
            'quotation_date' => 'required|date',
            'work_scope' => 'nullable|string',
            'estimated_days' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $data['quotation_amount'] = $data['quotation_amount'] ?? 0;

        $data['entered_by'] = $request->user()->id;
        $data['status'] = ContractorQuotation::STATUS_SUBMITTED;

        $quotation = ContractorQuotation::create($data);

        return response()->json($quotation->load('contractor'), 201);
    }

    /**
     * Select/Award a quotation
     */
    public function select($id)
    {
        $quotation = ContractorQuotation::findOrFail($id);
        $job = ProjectJob::findOrFail($quotation->job_id);

        return DB::transaction(function () use ($quotation, $job) {
            // Reject all other quotations for this job
            ContractorQuotation::where('job_id', $job->id)
                ->where('id', '!=', $quotation->id)
                ->update(['status' => ContractorQuotation::STATUS_REJECTED]);

            // Update this quotation status
            $quotation->update(['status' => ContractorQuotation::STATUS_SELECTED]);

            // Update the job with the selected contractor info
            $job->update([
                'selected_contractor_id' => $quotation->contractor_id,
                'contractor_quote_amount' => $quotation->quotation_amount,
                'contractor_quote_date' => $quotation->quotation_date,
                'status' => ProjectJob::STATUS_IN_PROGRESS,
            ]);

            $this->notifications->notifyQuotationSelected($quotation, Auth::user());

            return response()->json([
                'message' => 'Quotation selected and job awarded successfully',
                'job' => $job->load('selectedContractor'),
                'quotation' => $quotation
            ]);
        });
    }
}
