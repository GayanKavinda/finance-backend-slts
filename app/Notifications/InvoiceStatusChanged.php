<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public $invoice;
    public $action;
    public $actor;
    
    /**
     * Create a new notification instance.
     */
    public function __construct($invoice, $action, $actor)
    {
        $this->invoice = $invoice;
        $this->action = $action;
        $this->actor = $actor;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = match ($this->action) {
            'submitted' => "Invoice {$this->invoice->invoice_number} Awaiting Approval",
            'approved' => "Invoice {$this->invoice->invoice_number} Approved",
            'rejected' => "Invoice {$this->invoice->invoice_number} Rejected",
            'paid' => "Invoice {$this->invoice->invoice_number} Payment Recorded",
            default => "Invoice Update: {$this->invoice->invoice_number}",
        };

        $mail = (new MailMessage())
            ->subject($subject)
            ->line("Invoice {$this->invoice->invoice_number} was {$this->action} by {$this->actor->name}.");

        if ($this->action === 'rejected') {
            $mail->line("Reason: {$this->invoice->rejection_reason}");
        }

        return $mail
            ->action('View Invoice', url("/invoices/{$this->invoice->id}"))
            ->line('Log in to FinancePro to take action.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'action' => $this->action,
            'actor_name' => $this->actor->name,
            'message' => "Invoice {$this->invoice->invoice_number} was {$this->action} by {$this->actor->name}.",
        ];
    }
}
