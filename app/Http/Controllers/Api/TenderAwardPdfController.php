<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tender;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class TenderAwardPdfController extends Controller
{
    public function download($id)
    {
        $tender = Tender::with(['customer', 'jobs'])->findOrFail($id);

        if ($tender->status === 'Open' && $tender->awarded_amount <= 0) {
            return response()->json(['message' => 'Tender has not been awarded yet'], 422);
        }

        $pdf = Pdf::loadView('pdf.tender-award-customer', compact('tender'));

        return $pdf->download("Award-Confirmation-{$tender->tender_number}.pdf");
    }
}
