<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InternalReceiptPdfController extends Controller
{
    public function download($id)
    {
        $invoice = Invoice::with(['customer', 'purchaseOrder'])->findOrFail($id);

        if (!$invoice->receipt_number) {
            return response()->json(['message' => 'No receipt generated for this invoice'], 404);
        }

        $pdf = Pdf::loadView('pdf.internal-receipt', compact('invoice'));

        return $pdf->download("Receipt-{$invoice->receipt_number}.pdf");
    }
}
