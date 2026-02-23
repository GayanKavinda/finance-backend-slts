<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contractor;

class ContractorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Contractor::latest()->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:100',
            'tax_id' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'contact' => 'nullable|string|max:100',
            'bank_details' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:Active,Blacklisted',
            'rating' => 'nullable|integer|min:0|max:5',
            'notes' => 'nullable|string',
        ]);

        $contractor = Contractor::create($data);

        return response()->json($contractor, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Contractor $contractor)
    {
        return response()->json($contractor->load(['jobs', 'bills', 'quotations']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contractor $contractor)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:100',
            'tax_id' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'contact' => 'nullable|string|max:100',
            'bank_details' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:Active,Blacklisted',
            'rating' => 'nullable|integer|min:0|max:5',
            'notes' => 'nullable|string',
        ]);

        $contractor->update($data);

        return response()->json($contractor);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contractor $contractor)
    {
        $contractor->delete();

        return response()->json(['message' => 'Contractor deleted successfully']);
    }
}
