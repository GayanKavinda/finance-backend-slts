<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProjectJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = ProjectJob::with(['tender', 'customer', 'selectedContractor'])->withCount('purchaseOrders');

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
            $query->where('name', 'like', "%{$request->search}%");
        }

        return response()->json($query->latest()->paginate(15));
    }

    public function show($id)
    {
        return ProjectJob::with(['tender', 'customer', 'selectedContractor', 'purchaseOrders'])->findOrFail($id);
    }

    public function store(Request $request)
    {
        // Note: selected_contractor_id is intentionally not accepted on Job creation/update.
        // It is assigned exclusively through the quotation selection workflow (QuotationController::select).
        $validated = $request->validate([
            'tender_id'     => 'required|exists:tenders,id',
            'customer_id'   => 'required|exists:customers,id',
            'name'          => 'required|string|max:255',
            'project_value' => 'nullable|numeric|min:0',
            'description'   => 'nullable|string',
            'status'        => 'nullable|in:Pending,In Progress,Completed',
            'work_start_date'      => 'nullable|date',
            'work_completion_date' => 'nullable|date|after_or_equal:work_start_date',
        ]);

        $validated['project_value'] = $validated['project_value'] ?? 0;

        $validated['status'] = $validated['status'] ?? ProjectJob::STATUS_PENDING;

        $job = ProjectJob::create($validated);

        return response()->json($job, 201);
    }

    public function update(Request $request, $id)
    {
        $job = ProjectJob::findOrFail($id);
        $validated = $request->validate([
            'tender_id'     => 'required|exists:tenders,id',
            'customer_id'   => 'required|exists:customers,id',
            'name'          => 'required|string|max:255',
            'project_value' => 'nullable|numeric|min:0',
            'description'   => 'nullable|string',
            'status'        => 'required|string',
            'work_start_date'      => 'nullable|date',
            'work_completion_date' => 'nullable|date|after_or_equal:work_start_date',
        ]);

        $job->update($validated);

        return response()->json($job->load(['tender', 'customer', 'selectedContractor']));
    }

    public function destroy($id)
    {
        $job = ProjectJob::findOrFail($id);

        if ($job->purchaseOrders()->count() > 0) {
            return response()->json(['message' => 'Cannot delete job with active purchase orders'], 422);
        }

        $job->delete();

        return response()->json(['message' => 'Job deleted']);
    }

    public function export(Request $request)
    {
        $query = ProjectJob::with(['tender', 'customer', 'selectedContractor'])->withCount('purchaseOrders');

        if ($request->tender_id) {
            $query->where('tender_id', $request->tender_id);
        }

        if ($request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $jobs = $query->latest()->get();

        $headers = [
            'ID',
            'Name',
            'Status',
            'Customer',
            'Tender',
            'Selected Contractor',
            'Project Value (LKR)',
            'Work Start Date',
            'Work Completion Date',
            'Purchase Orders Count',
            'Description',
            'Created At',
            'Updated At',
        ];

        $callback = function () use ($jobs, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);

            foreach ($jobs as $job) {
                fputcsv($file, [
                    $job->id,
                    $job->name,
                    $job->status,
                    $job->customer?->name ?? 'N/A',
                    $job->tender?->tender_number ?? 'N/A',
                    $job->selectedContractor?->name ?? 'N/A',
                    $job->project_value ?? 0,
                    $job->work_start_date ?? 'N/A',
                    $job->work_completion_date ?? 'N/A',
                    $job->purchase_orders_count ?? 0,
                    $job->description ?? 'N/A',
                    $job->created_at?->format('Y-m-d H:i:s') ?? 'N/A',
                    $job->updated_at?->format('Y-m-d H:i:s') ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        $filename = 'jobs-export-' . now()->format('Y-m-d-H-i-s') . '.csv';

        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
