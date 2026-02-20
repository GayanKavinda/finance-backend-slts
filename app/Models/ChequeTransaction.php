<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChequeTransaction extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'Pending';
    const STATUS_CLEARED = 'Cleared';
    const STATUS_BOUNCED  = 'Bounced';

    protected $fillable = [
        'invoice_id',

        // Payer (Drawer) information
        'payer_name',
        'payer_account_number',
        'payer_bank_name',
        'payer_bank_branch',
        'payer_bank_code',

        // Cheque details
        'cheque_number',
        'cheque_date',
        'amount',
        'amount_in_words',
        'payee_name',
        'signature_path',

        'status',
    ];

    protected $casts = [
        'cheque_date' => 'date',
        'amount'      => 'decimal:2',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // ── Scopes ────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeCleared($query)
    {
        return $query->where('status', self::STATUS_CLEARED);
    }

    public function scopeBounced($query)
    {
        return $query->where('status', self::STATUS_BOUNCED);
    }

    // ── Helpers ───────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isCleared(): bool
    {
        return $this->status === self::STATUS_CLEARED;
    }

    public function isBounced(): bool
    {
        return $this->status === self::STATUS_BOUNCED;
    }
}
