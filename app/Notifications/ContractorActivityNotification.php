<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContractorActivityNotification extends Notification
{
    use Queueable;

    public $entity;
    public $type;
    public $action;
    public $actor;
    public $message;

    /**
     * Create a new notification instance.
     * 
     * @param mixed $entity The Quotation or Bill object
     * @param string $type 'quotation' or 'bill'
     * @param string $action e.g., 'selected', 'verified', 'approved', 'paid'
     * @param mixed $actor The user who performed the action
     */
    public function __construct($entity, $type, $action, $actor, $message = null)
    {
        $this->entity = $entity;
        $this->type = $type;
        $this->action = $action;
        $this->actor = $actor;
        $this->message = $message ?: $this->generateDefaultMessage();
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
        $subject = match ($this->type) {
            'quotation' => "Job Awarded: " . ($this->entity->job->name ?? 'Quotation Update'),
            'bill' => "Contractor Bill " . $this->entity->bill_number . " " . ucfirst($this->action),
            default => "Contractor Activity Update",
        };

        return (new MailMessage())
            ->subject($subject)
            ->line($this->message)
            ->action('View Details', url($this->type === 'quotation' ? "/jobs/{$this->entity->job_id}" : "/contractor-bills"))
            ->line('Thank you for using FinancePro.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'entity_id' => $this->entity->id,
            'entity_type' => $this->type,
            'action' => $this->action,
            'actor_name' => $this->actor->name,
            'message' => $this->message,
            'job_name' => $this->type === 'quotation' ? ($this->entity->job->name ?? null) : ($this->entity->job->name ?? null),
        ];
    }

    protected function generateDefaultMessage(): string
    {
        if ($this->type === 'quotation') {
            return "Contractor '{$this->entity->contractor->name}' has been selected for job '{$this->entity->job->name}'.";
        }

        return "Contractor Bill #{$this->entity->bill_number} has been {$this->action} by {$this->actor->name}.";
    }
}
