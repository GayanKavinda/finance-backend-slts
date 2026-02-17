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
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
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
