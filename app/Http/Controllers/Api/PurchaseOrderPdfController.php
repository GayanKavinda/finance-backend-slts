<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseOrderPdfController extends Controller
{
    public function download($id)
    {
        $po = PurchaseOrder::with(['job.tender', 'customer'])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.purchase-order', compact('po'));

        return $pdf->download("PO-{$po->po_number}.pdf");
    }
}
