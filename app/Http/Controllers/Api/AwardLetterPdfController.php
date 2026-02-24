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

        $pdf = Pdf::loadView('pdf.award-letter', compact('job'));

        return $pdf->download("Award-Letter-{$job->id}.pdf");
    }
}
