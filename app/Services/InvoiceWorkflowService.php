<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceStatusHistory;
use App\Models\User;
use App\Notifications\InvoiceStatusChanged;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class InvoiceWorkflowService
{
    /**
     * Submit an invoice to finance (Tax Generated → Submitted)
     * Permission check is handled by route middleware (can:submit-invoice).
     * No need to re-check here.
     */
    public function submit(Invoice $invoice): Invoice
    {
        $this->validateTransition($invoice, Invoice::STATUS_SUBMITTED);

        return DB::transaction(function () use ($invoice) {
            $oldStatus = $invoice->status;

            $invoice->update([
                'status'       => Invoice::STATUS_SUBMITTED,
                'submitted_by' => Auth::id(),
                'submitted_at' => now(),
            ]);

            $this->logStatusChange($invoice, $oldStatus, Invoice::STATUS_SUBMITTED);

            // Notify all Finance users that a new invoice needs their attention
            $financeUsers = User::permission('approve-payment')->get();
            Notification::send($financeUsers, new InvoiceStatusChanged($invoice, 'submitted', Auth::user()));

            return $invoice;
        });
    }

    /**
     * Approve an invoice (Submitted → Approved)
     * Permission check handled by route middleware (can:approve-payment).
     */
    public function approve(Invoice $invoice): Invoice
    {
        $this->validateTransition($invoice, Invoice::STATUS_APPROVED);

        return DB::transaction(function () use ($invoice) {
            $oldStatus = $invoice->status;

            $invoice->update([
                'status'      => Invoice::STATUS_APPROVED,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            $this->logStatusChange($invoice, $oldStatus, Invoice::STATUS_APPROVED);

            // Notify the person who submitted the invoice
            if ($invoice->submitter) {
                $invoice->submitter->notify(
                    new InvoiceStatusChanged($invoice, 'approved', Auth::user())
                );
            }

            return $invoice;
        });
    }

    /**
     * Reject an invoice (Submitted → Rejected  or  Approved → Rejected)
     * Permission check handled by route middleware (can:reject-invoice).
     */
    public function reject(Invoice $invoice, string $reason): Invoice
    {
        $this->validateTransition($invoice, Invoice::STATUS_REJECTED);

        return DB::transaction(function () use ($invoice, $reason) {
            $oldStatus = $invoice->status;

            $updateData = [
                'status'           => Invoice::STATUS_REJECTED,
                'rejected_by'      => Auth::id(),
                'rejected_at'      => now(),
                'rejection_reason' => $reason,
            ];

            // Clear approval data when rejecting a previously approved invoice
            if ($oldStatus === Invoice::STATUS_APPROVED) {
                $updateData['approved_by'] = null;
                $updateData['approved_at'] = null;
            }

            $invoice->update($updateData);

            $this->logStatusChange($invoice, $oldStatus, Invoice::STATUS_REJECTED, $reason);

            // Notify submitter so they know to correct and resubmit
            if ($invoice->submitter) {
                $invoice->submitter->notify(
                    new InvoiceStatusChanged($invoice, 'rejected', Auth::user())
                );
            }

            return $invoice;
        });
    }

    /**
     * Mark invoice as paid (Approved → Paid)
     * Permission check handled by route middleware (can:approve-payment).
     */
    public function markPaid(Invoice $invoice, array $paymentData): Invoice
    {
        $this->validateTransition($invoice, Invoice::STATUS_PAID);

        return DB::transaction(function () use ($invoice, $paymentData) {
            $oldStatus = $invoice->status;

            $invoice->update([
                'status'            => Invoice::STATUS_PAID,
                'payment_reference' => $paymentData['payment_reference'],
                'payment_method'    => $paymentData['payment_method'],
                'payment_notes'     => $paymentData['payment_notes'] ?? null,
                'paid_at'           => now(),
                'recorded_by'       => Auth::id(),
            ]);

            $this->logStatusChange(
                $invoice,
                $oldStatus,
                Invoice::STATUS_PAID,
                null,
                [
                    'payment_reference' => $paymentData['payment_reference'],
                    'payment_method'    => $paymentData['payment_method'],
                ]
            );

            // ✅ Notify submitter that payment has been recorded
            if ($invoice->submitter) {
                $invoice->submitter->notify(
                    new InvoiceStatusChanged($invoice, 'paid', Auth::user())
                );
            }

            return $invoice;
        });
    }

    /**
     * Validate that a transition is allowed per the state machine.
     * Returns a consistent { "message": "..." } 422 response.
     */
    protected function validateTransition(Invoice $invoice, string $targetStatus): void
    {
        $allowed       = Invoice::allowedTransitions();
        $currentStatus = $invoice->status;

        if (
            !isset($allowed[$currentStatus]) ||
            !in_array($targetStatus, $allowed[$currentStatus], true)
        ) {
            abort(422, "Cannot transition invoice from '{$currentStatus}' to '{$targetStatus}'.");
        }
    }

    /**
     * Append an immutable row to invoice_status_history.
     */
    protected function logStatusChange(
        Invoice $invoice,
        ?string $oldStatus,
        string  $newStatus,
        ?string $reason           = null,
        ?array  $additionalMetadata = null
    ): void {
        InvoiceStatusHistory::create([
            'invoice_id' => $invoice->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by' => Auth::id(),
            'reason'     => $reason,
            'metadata'   => array_merge(
                [
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->header('User-Agent'),
                ],
                $additionalMetadata ?? []
            ),
        ]);
    }
}
