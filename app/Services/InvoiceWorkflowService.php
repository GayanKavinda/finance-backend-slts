<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceStatusHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class InvoiceWorkflowService
{
    /**
     * Submit an invoice to finance
     */
    public function submit(Invoice $invoice)
    {
        if (!Auth::user()->can('submit-invoice')) {
            abort(403, 'Unauthorized action.');
        }

        $this->validateTransition($invoice, Invoice::STATUS_SUBMITTED);

        return DB::transaction(function () use ($invoice) {
            $oldStatus = $invoice->status;

            $invoice->update([
                'status' => Invoice::STATUS_SUBMITTED,
                'submitted_by' => Auth::id(),
                'submitted_at' => now(),
            ]);

            $this->logStatusChange($invoice, $oldStatus, Invoice::STATUS_SUBMITTED);

            // Notify Finance Users
            $financeUsers = \App\Models\User::permission('approve-payment')->get();
            \Illuminate\Support\Facades\Notification::send($financeUsers, new \App\Notifications\InvoiceStatusChanged($invoice, 'submitted', Auth::user()));

            return $invoice;
        });
    }

    /**
     * Approve an invoice (Finance role)
     */
    public function approve(Invoice $invoice)
    {
        if (!Auth::user()->can('approve-payment')) {
            abort(403, 'Unauthorized action.');
        }

        $this->validateTransition($invoice, Invoice::STATUS_APPROVED);

        return DB::transaction(function () use ($invoice) {
            $oldStatus = $invoice->status;

            $invoice->update([
                'status' => Invoice::STATUS_APPROVED,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            $this->logStatusChange($invoice, $oldStatus, Invoice::STATUS_APPROVED);

            // Notify Submitter
            if ($invoice->submitter) {
                $invoice->submitter->notify(new \App\Notifications\InvoiceStatusChanged($invoice, 'approved', Auth::user()));
            }

            return $invoice;
        });
    }

    /**
     * Reject an invoice (Finance role)
     */
    public function reject(Invoice $invoice, string $reason)
    {
        if (!Auth::user()->can('reject-invoice')) {
            abort(403, 'Unauthorized action.');
        }

        $this->validateTransition($invoice, Invoice::STATUS_REJECTED);

        return DB::transaction(function () use ($invoice, $reason) {
            $oldStatus = $invoice->status;

            $updateData = [
                'status' => Invoice::STATUS_REJECTED,
                'rejected_by' => Auth::id(),
                'rejected_at' => now(),
                'rejection_reason' => $reason,
            ];

            // If rejecting an already approved invoice, clear approval data
            if ($oldStatus === Invoice::STATUS_APPROVED) {
                $updateData['approved_by'] = null;
                $updateData['approved_at'] = null;
            }

            $invoice->update($updateData);

            $this->logStatusChange($invoice, $oldStatus, Invoice::STATUS_REJECTED, $reason);

            // Notify Submitter
            if ($invoice->submitter) {
                $invoice->submitter->notify(new \App\Notifications\InvoiceStatusChanged($invoice, 'rejected', Auth::user()));
            }

            return $invoice;
        });
    }

    /**
     * Mark invoice as paid (Finance role)
     */
    public function markPaid(Invoice $invoice, array $paymentData)
    {
        if (!Auth::user()->can('approve-payment')) {
            abort(403, 'Unauthorized action.');
        }

        $this->validateTransition($invoice, Invoice::STATUS_PAID);

        return DB::transaction(function () use ($invoice, $paymentData) {
            $oldStatus = $invoice->status;

            $invoice->update([
                'status' => Invoice::STATUS_PAID,
                'payment_reference' => $paymentData['payment_reference'],
                'payment_method' => $paymentData['payment_method'],
                'payment_notes' => $paymentData['payment_notes'] ?? null,
                'paid_at' => now(),
                'recorded_by' => Auth::id(),
            ]);

            $this->logStatusChange($invoice, $oldStatus, Invoice::STATUS_PAID, null, $paymentData);

            return $invoice;
        });
    }

    /**
     * Internal: Log status change to history
     */
    protected function logStatusChange(Invoice $invoice, ?string $oldStatus, string $newStatus, ?string $reason = null, ?array $additionalMetadata = null)
    {
        InvoiceStatusHistory::create([
            'invoice_id' => $invoice->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by' => Auth::id(),
            'reason' => $reason,
            'metadata' => array_merge([
                'ip_address' => request()->ip(),
                'user_agent' => request()->header('User-Agent'),
            ], $additionalMetadata ?? []),
        ]);
    }

    /**
     * Internal: Validate if transition is allowed
     */
    protected function validateTransition(Invoice $invoice, string $targetStatus)
    {
        $allowed = Invoice::allowedTransitions();
        $currentStatus = $invoice->status;

        if (!isset($allowed[$currentStatus]) || !in_array($targetStatus, $allowed[$currentStatus])) {
            abort(422, "Cannot transition invoice from '{$currentStatus}' to '{$targetStatus}'.");
        }
    }
}
