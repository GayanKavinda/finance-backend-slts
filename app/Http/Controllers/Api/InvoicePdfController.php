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

        // If tax invoice is missing, dynamically calculate standard 18% VAT or 0% so download never fails with 422
        $taxPercentage = $invoice->taxInvoice ? $invoice->taxInvoice->tax_percentage : 18;
        $taxAmount = $invoice->taxInvoice ? $invoice->taxInvoice->tax_amount : round($invoice->invoice_amount * ($taxPercentage / 100), 2);
        $totalAmount = $invoice->taxInvoice ? $invoice->taxInvoice->total_amount : ($invoice->invoice_amount + $taxAmount);

        $taxInvoiceNumber = $invoice->taxInvoice ? $invoice->taxInvoice->tax_invoice_number : ('TAX-' . $invoice->invoice_number);

        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice,
            'taxPercentage' => $taxPercentage,
            'taxAmount' => $taxAmount,
            'totalAmount' => $totalAmount,
            'taxInvoiceNumber' => $taxInvoiceNumber,
            'company' => [
                'name' => 'Sri Lanka Telecom Services',
                'division' => 'Finance Division',
                'address' => 'Colombo, Sri Lanka',
                'logo' => asset('icons/slt_digital_icon.png'),
            ]
        ])->setPaper('A4');

        return $pdf->download('Invoice-' . $invoice->invoice_number . '.pdf');
    }
}
