<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceStatusChanged extends Notification
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
     * In-app (database) delivery is always on. Email delivery respects the
     * recipient's notification preferences (Settings → Notifications).
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if ($notifiable instanceof \App\Models\User) {
            $prefs = $notifiable->notificationPreference ?: $notifiable->preferences();
            if ($prefs->email_alerts) {
                $channels[] = 'mail';
            }
        }

        return $channels;
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
            'payment received' => "Invoice {$this->invoice->invoice_number} Payment Recorded",
            'banked' => "Invoice {$this->invoice->invoice_number} Cleared & Banked",
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
            ->line('Log in to SLT ProcureX to take action.');
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
