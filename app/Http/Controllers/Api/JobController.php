<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProjectJob;
use Illuminate\Http\Request;

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
        $validated = $request->validate([
            'tender_id'     => 'required|exists:tenders,id',
            'customer_id'   => 'required|exists:customers,id',
            'name'          => 'required|string|max:255',
            'project_value' => 'nullable|numeric|min:0',
            'description'   => 'nullable|string',
            'status'        => 'nullable|in:Pending,In Progress,Completed',
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
}
