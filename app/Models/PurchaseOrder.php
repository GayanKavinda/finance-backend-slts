<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    const STATUS_DRAFT = 'Draft';
    const STATUS_APPROVED = 'Approved';
    const STATUS_SENT = 'Sent';
    const STATUS_RECEIVED = 'Received';
    const STATUS_CANCELLED = 'Cancelled';

    /**
     * Valid lifecycle transitions for a purchase order.
     */
    public static function allowedTransitions(): array
    {
        return [
            self::STATUS_DRAFT    => [self::STATUS_APPROVED, self::STATUS_CANCELLED],
            self::STATUS_APPROVED => [self::STATUS_SENT, self::STATUS_CANCELLED],
            self::STATUS_SENT     => [self::STATUS_RECEIVED, self::STATUS_CANCELLED],
            self::STATUS_RECEIVED => [],
            self::STATUS_CANCELLED => [],
        ];
    }

    public static function statusList(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_APPROVED,
            self::STATUS_SENT,
            self::STATUS_RECEIVED,
            self::STATUS_CANCELLED,
        ];
    }

    public function canTransitionTo(string $status): bool
    {
        if ($status === $this->status) {
            return true;
        }

        return in_array($status, self::allowedTransitions()[$this->status] ?? [], true);
    }

    protected $fillable = [
        'po_number',
        'po_date',
        'po_description',
        'po_amount',
        'billing_address',
        'tender_id',
        'customer_id',
        'job_id',
        'status',
    ];

    public function job()
    {
        return $this->belongsTo(ProjectJob::class, 'job_id');
    }

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'po_id');
    }

    public function statusHistory()
    {
        return $this->hasMany(PurchaseOrderStatusHistory::class)->orderBy('created_at', 'desc');
    }
}
