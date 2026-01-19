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
    public function index()
    {
        return response()->json(Tender::with('customer')->latest()->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'tender_number' => 'required|string|max:255|unique:tenders,tender_number',
            'customer_id' => 'required|exists:customers,id',
            'awarded_amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|in:Awarded,In Progress,Completed',
        ]);

        $tender = Tender::create($data);

        return response()->json($tender->load('customer'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tender $tender)
    {
        return response()->json($tender->load('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tender $tender)
    {
        $data = $request->validate([
            'tender_number' => 'required|string|max:255|unique:tenders,tender_number,' . $tender->id,
            'customer_id' => 'required|exists:customers,id',
            'awarded_amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|in:Awarded,In Progress,Completed',
        ]);

        $tender->update($data);

        return response()->json($tender->load('customer'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tender $tender)
    {
        $tender->delete();

        return response()->json(['message' => 'Tender deleted']);
    }
}
