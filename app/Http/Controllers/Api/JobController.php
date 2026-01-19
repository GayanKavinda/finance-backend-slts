<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index()
    {
        return Job::with(['tender', 'contractor'])->latest()->get();
    }

    public function show($id)
    {
        return Job::with(['tender', 'contractor'])->findOrFail($id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tender_id' => 'nullable|exists:tenders,id',
            'contractor_id' => 'nullable|exists:contractors,id',
            'status' => 'required|in:Pending,In Progress,Completed',
        ]);

        $job = Job::create($validated);

        return response()->json($job, 201);
    }


    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tender_id' => 'nullable|exists:tenders,id',
            'contractor_id' => 'nullable|exists:contractors,id',
            'status' => 'required|string'
        ]);

        $job = Job::findOrFail($id);
        $job->update($validated);

        return response()->json($job->load(['tender', 'contractor']));
    }
}
