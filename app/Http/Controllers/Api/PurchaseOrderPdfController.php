<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseOrderPdfController extends Controller
{
    private static array $ones = [
        '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
        'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen',
        'Seventeen', 'Eighteen', 'Nineteen',
    ];

    private static array $tens = [
        '', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety',
    ];

    public function download($id)
    {
        $po = PurchaseOrder::with(['job.tender', 'customer'])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.purchase-order', [
            'po' => $po,
            'amountWords' => $this->amountInWords((float) $po->po_amount),
            'company' => [
                'name' => 'Sri Lanka Telecom Services',
                'division' => 'Finance Division',
                'address' => 'Colombo, Sri Lanka',
                'logo' => asset('icons/slt_digital_icon.png'),
            ],
        ]);

        $safePoNumber = str_replace(['/', '\\'], '-', $po->po_number);

        return $pdf->download("PO-{$safePoNumber}.pdf");
    }

    /**
     * Convert a LKR amount into words using the South Asian
     * (thousand / lakh / crore) numbering convention.
     */
    private function amountInWords(float $amount): string
    {
        $num = (int) round($amount);

        if ($num === 0) {
            return 'Zero';
        }

        $units = ['', ' Thousand', ' Lakh', ' Crore', ' Arab', ' Kharab'];

        // First group is up to 3 digits, subsequent groups are 2 digits
        $groups = [$num % 1000];
        $num = intdiv($num, 1000);

        while ($num > 0) {
            $groups[] = $num % 100;
            $num = intdiv($num, 100);
        }

        $parts = [];

        foreach ($groups as $i => $group) {
            if ($group > 0) {
                $parts[] = $this->convertGroup($group) . $units[$i];
            }
        }

        return implode(' ', array_reverse($parts));
    }

    private function convertGroup(int $num): string
    {
        $str = '';

        $hundreds = intdiv($num, 100);
        $remainder = $num % 100;

        if ($hundreds > 0) {
            $str .= self::$ones[$hundreds] . ' Hundred';
        }

        if ($remainder > 0) {
            $str .= ($str !== '' ? ' ' : '');

            if ($remainder < 20) {
                $str .= self::$ones[$remainder];
            } else {
                $str .= self::$tens[intdiv($remainder, 10)];

                if ($remainder % 10 > 0) {
                    $str .= ' ' . self::$ones[$remainder % 10];
                }
            }
        }

        return $str;
    }
}
