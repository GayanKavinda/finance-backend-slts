<?php

namespace App\Services;

use App\Models\ContractorBill;
use App\Models\ContractorQuotation;
use App\Models\User;
use App\Notifications\ContractorActivityNotification;
use Illuminate\Support\Facades\Notification;

class ContractorNotificationService
{
    /**
     * Notify about quotation selection
     */
    public function notifyQuotationSelected(ContractorQuotation $quotation, User $actor)
    {
        // Notify Procurement users
        $procurementUsers = User::permission('enter-quotations')->get();
        Notification::send($procurementUsers, new ContractorActivityNotification($quotation, 'quotation', 'selected', $actor));
    }

    /**
     * Notify about bill status change
     */
    public function notifyBillStatusChanged(ContractorBill $bill, string $status, User $actor)
    {
        $action = strtolower($status);
        $notification = new ContractorActivityNotification($bill, 'bill', $action, $actor);

        if ($status === ContractorBill::STATUS_VERIFIED) {
            // Notify procurement manager to submit to finance
            $procurementManagers = User::permission('submit-contractor-bill')->get();
            if ($procurementManagers->isEmpty()) {
                $procurementManagers = User::permission('enter-quotations')->get();
            }
            Notification::send($procurementManagers, $notification);
        } elseif ($status === ContractorBill::STATUS_SUBMITTED) {
            // Notify Finance to approve
            $financeUsers = User::permission('approve-payment')->get();
            Notification::send($financeUsers, $notification);
        } elseif ($status === ContractorBill::STATUS_APPROVED) {
            // Notify Procurement that it's ready for payment or just for tracking
            $procurementUsers = User::permission('enter-quotations')->get();
            Notification::send($procurementUsers, $notification);

            // Also notify Finance users with specific payment permission
            $financeUsers = User::permission('mark-contractor-paid')->get();
            Notification::send($financeUsers, $notification);
        } elseif ($status === ContractorBill::STATUS_REJECTED) {
            // Notify Procurement that bill needs central correction
            $procurementUsers = User::permission('enter-quotations')->get();
            Notification::send($procurementUsers, $notification);
        } elseif ($status === ContractorBill::STATUS_PAID) {
            // Notify all relevant parties
            $relevantUsers = User::permission('enter-quotations')->get();
            Notification::send($relevantUsers, $notification);
        }
    }
}
