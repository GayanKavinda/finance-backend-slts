<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoicePdfController extends Controller
{
    public function download($id)
    {
        $invoice = Invoice::with([
            'customer',
            'purchaseOrder',
            'taxInvoice',
        ])->findOrFail($id);

        if (!$invoice->taxInvoice) {
            return response()->json([
                'message' => 'Tax invoice not generated yet'
            ], 422);
        }

        if (!in_array($invoice->status, ['Tax Generated', 'Submitted to Finance', 'Paid'])) {
            return response()->json([
                'message' => 'Invoice not finalized'
            ], 422);
        }

        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice,
        ])->setPaper('A4');

        return $pdf->download(
            'Invoice-' . $invoice->invoice_number . '.pdf'
        );
    }
}
