<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProjectJob;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class AwardLetterPdfController extends Controller
{
    public function download($id)
    {
        $job = ProjectJob::with(['tender', 'customer', 'selectedContractor'])->findOrFail($id);

        if (!$job->selected_contractor_id) {
            return response()->json(['message' => 'No contractor selected for this job yet'], 422);
        }

        if (!$job->selectedContractor->contractor_code) {
            return response()->json(['message' => 'Contractor code is missing for this contractor.'], 422);
        }

        if (!$job->tender || !$job->tender->tender_number) {
            return response()->json(['message' => 'Tender number is missing for this job.'], 422);
        }

        $pdf = Pdf::loadView('pdf.award-letter', compact('job'));

        return $pdf->download("Award-Letter-{$job->id}.pdf");
    }
}
