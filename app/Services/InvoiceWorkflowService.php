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
     * Transition an invoice to a new status
     */
    public function transitionTo(Invoice $invoice, string $targetStatus, User $user, ?string $reason = null): Invoice
    {
        $this->validateTransition($invoice, $targetStatus);

        return DB::transaction(function () use ($invoice, $targetStatus, $user, $reason) {
            $oldStatus = $invoice->status;

            $updateData = ['status' => $targetStatus];

            // Specific metadata based on status
            if ($targetStatus === Invoice::STATUS_SUBMITTED) {
                $updateData['submitted_by'] = $user->id;
                $updateData['submitted_at'] = now();
            } elseif ($targetStatus === Invoice::STATUS_APPROVED) {
                $updateData['approved_by'] = $user->id;
                $updateData['approved_at'] = now();
            } elseif ($targetStatus === Invoice::STATUS_REJECTED) {
                $updateData['rejected_by'] = $user->id;
                $updateData['rejected_at'] = now();
                $updateData['rejection_reason'] = $reason;
            }

            $invoice->update($updateData);

            $this->logStatusChange($invoice, $oldStatus, $targetStatus, $reason);

            // Notify based on status
            $this->handleNotifications($invoice, $targetStatus, $user);

            return $invoice;
        });
    }

    protected function handleNotifications(Invoice $invoice, string $status, User $actor): void
    {
        if ($status === Invoice::STATUS_SUBMITTED) {
            $financeUsers = User::permission('approve-payment')->get();
            Notification::send($financeUsers, new InvoiceStatusChanged($invoice, 'submitted', $actor));
        } elseif ($status === Invoice::STATUS_APPROVED) {
            if ($invoice->submitter) {
                $invoice->submitter->notify(new InvoiceStatusChanged($invoice, 'approved', $actor));
            }
        } elseif ($status === Invoice::STATUS_REJECTED) {
            if ($invoice->submitter) {
                $invoice->submitter->notify(new InvoiceStatusChanged($invoice, 'rejected', $actor));
            }
        }
    }

    protected function validateTransition(Invoice $invoice, string $targetStatus): void
    {
        $allowed = Invoice::allowedTransitions();
        $currentStatus = $invoice->status;

        if (!isset($allowed[$currentStatus]) || !in_array($targetStatus, $allowed[$currentStatus], true)) {
            abort(422, "Cannot transition invoice from '{$currentStatus}' to '{$targetStatus}'.");
        }
    }

    protected function logStatusChange(Invoice $invoice, ?string $oldStatus, string $newStatus, ?string $reason = null): void
    {
        InvoiceStatusHistory::create([
            'invoice_id' => $invoice->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by' => Auth::id(),
            'notes' => $reason, // Note: original model might use 'reason' or 'notes'. The migration checked earlier used 'notes' in recordPayment.
        ]);
    }
}
