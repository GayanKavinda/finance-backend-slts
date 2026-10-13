<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    const STATUS_OPEN        = 'Open';
    const STATUS_IN_PROGRESS = 'In Progress';
    const STATUS_RESOLVED    = 'Resolved';
    const STATUS_CLOSED      = 'Closed';

    const PRIORITY_LOW      = 'Low';
    const PRIORITY_MEDIUM   = 'Medium';
    const PRIORITY_HIGH     = 'High';
    const PRIORITY_CRITICAL = 'Critical';

    const CATEGORIES = [
        'Account & Access',
        'Tenders & Jobs',
        'Purchase Orders',
        'Contractor Bills',
        'Invoices & Payments',
        'Notifications',
        'Report a Bug',
        'Feature Request',
        'Other',
    ];

    const PRIORITIES = [
        self::PRIORITY_LOW,
        self::PRIORITY_MEDIUM,
        self::PRIORITY_HIGH,
        self::PRIORITY_CRITICAL,
    ];

    protected $fillable = [
        'ticket_number',
        'user_id',
        'category',
        'priority',
        'subject',
        'description',
        'status',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Boot: auto ticket number TKT-YYYY-#### ─────────────────────

    protected static function booted(): void
    {
        static::creating(function (SupportTicket $ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = self::generateTicketNumber();
            }
        });
    }

    public static function generateTicketNumber(): string
    {
        $year = now()->year;
        $latest = static::where('ticket_number', 'like', "TKT-{$year}-%")
            ->orderByDesc('ticket_number')
            ->value('ticket_number');

        $sequence = $latest ? ((int) substr($latest, -4)) + 1 : 1;

        return sprintf('TKT-%d-%04d', $year, $sequence);
    }

    // ── Helpers ────────────────────────────────────────────────────

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function isResolved(): bool
    {
        return in_array($this->status, [self::STATUS_RESOLVED, self::STATUS_CLOSED], true);
    }
}
