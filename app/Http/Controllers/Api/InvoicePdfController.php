<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoicePdfController extends Controller
{
    public function download($id)
    {
        $invoice = Invoice::with(['customer', 'purchaseOrder', 'taxInvoice'])
            ->findOrFail($id);

        if (!$invoice->taxInvoice) {
            return response()->json(['message' => 'Tax invoice not generated yet'], 422);
        }

        if (!in_array($invoice->status, [
            Invoice::STATUS_TAX_GENERATED,
            Invoice::STATUS_SUBMITTED,
            Invoice::STATUS_PAID,
        ])) {
            return response()->json([
                'message' => 'Invoice not finalized'
            ], 422);
        }

        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice,
            'company' => [
                'name' => 'Sri Lanka Telecom Services',
                'division' => 'Finance Division',
                'address' => 'Colombo, Sri Lanka',
                // 'logo' => public_path('icons/slt_digital_icon.png'),
                'logo' => asset('icons/slt_digital_icon.png'),
            ]
        ])->setPaper('A4');

        return $pdf->download('Invoice-' . $invoice->invoice_number . '.pdf');
    }
}
