<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tender;
use App\Models\Customer;

class TenderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Tender::with('customer')->withCount('jobs');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('tender_number', 'like', "%{$request->search}%")
                    ->orWhere('name', 'like', "%{$request->search}%");
            });
        }

        return response()->json($query->latest()->paginate(15));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'tender_number' => 'required|string|max:255|unique:tenders,tender_number',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'customer_id' => 'required|exists:customers,id',
            'awarded_amount' => 'required|numeric|min:0',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|in:Open,In Progress,Closed',
        ]);

        $data['status'] = $data['status'] ?? Tender::STATUS_OPEN;

        $tender = Tender::create($data);

        return response()->json($tender->load('customer'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return response()->json(Tender::with(['customer', 'jobs.customer'])->findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $tender = Tender::findOrFail($id);
        $data = $request->validate([
            'tender_number' => 'required|string|max:255|unique:tenders,tender_number,' . $tender->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'customer_id' => 'required|exists:customers,id',
            'awarded_amount' => 'required|numeric|min:0',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|in:Open,In Progress,Closed',
        ]);

        $tender->update($data);

        return response()->json($tender->load('customer'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $tender = Tender::findOrFail($id);

        if ($tender->jobs()->count() > 0) {
            return response()->json(['message' => 'Cannot delete tender with active jobs'], 422);
        }

        $tender->delete();

        return response()->json(['message' => 'Tender deleted']);
    }
}
