<?php

namespace App\Services\Domains;

use App\Models\Invoice;
use App\Models\TaxInvoice;
use App\Services\InvoiceWorkflowService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * BillingDomainService
 * 
 * This service encapsulates all financial business logic for the Billing micro-context.
 * Designed to be portable to a standalone Billing Microservice.
 */
class BillingDomainService
{
    protected $workflow;

    public function __construct(InvoiceWorkflowService $workflow)
    {
        $this->workflow = $workflow;
    }

    /**
     * Record a customer payment atomically.
     */
    public function recordPayment(Invoice $invoice, array $data)
    {
        return DB::transaction(function () use ($invoice, $data) {
            $receiptNumber = $this->generateReceiptNumber();

            $invoice->update([
                'cheque_number' => $data['cheque_number'],
                'bank_name' => $data['bank_name'],
                'payment_amount' => $data['payment_amount'],
                'payment_received_date' => $data['payment_received_date'],
                'receipt_number' => $receiptNumber,
                'recorded_by' => Auth::id(),
            ]);

            return $this->workflow->transitionTo(
                $invoice,
                Invoice::STATUS_PAYMENT_RECEIVED,
                Auth::user(),
                "Payment received via domain service: Cheque {$data['cheque_number']}"
            );
        });
    }

    /**
     * Finalize banking for an invoice.
     */
    public function finalizeBanking(Invoice $invoice, array $data)
    {
        return DB::transaction(function () use ($invoice, $data) {
            $invoice->update([
                'is_banked' => true,
                'banked_at' => $data['banked_at'],
                'bank_reference' => $data['bank_reference'] ?? null,
            ]);

            return $this->workflow->transitionTo(
                $invoice,
                Invoice::STATUS_BANKED,
                Auth::user(),
                "Banking finalized via domain service"
            );
        });
    }

    private function generateReceiptNumber()
    {
        $year = date('Y');
        $count = Invoice::where('receipt_number', 'like', "RCP-{$year}-%")->count() + 1;
        return "RCP-{$year}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
