<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    const STATUS_DRAFT = 'Draft';
    // const STATUS_SENT = 'Sent';
    const STATUS_TAX_GENERATED = 'Tax Generated';
    const STATUS_SUBMITTED = 'Submitted';
    const STATUS_APPROVED = 'Approved';
    const STATUS_REJECTED = 'Rejected';
    const STATUS_PAID = 'Paid';

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_TAX_GENERATED,
            self::STATUS_SUBMITTED,
            self::STATUS_APPROVED,
            self::STATUS_REJECTED,
            self::STATUS_PAID,
        ];
    }

    protected $fillable = [
        'po_id',
        'customer_id',
        'invoice_number',
        'invoice_amount',
        'invoice_date',
        'status',
        'payment_reference',
        'payment_method',
        'paid_at',
        'recorded_by',
        'payment_notes',
        'submitted_by',
        'submitted_at',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    // protected $appends = ['total_amount'];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function taxInvoice()
    {
        return $this->hasOne(TaxInvoice::class);
    }

    public function getTotalAmountAttribute()
    {
        if (!$this->taxInvoice) {
            return $this->invoice_amount;
        }

        return $this->invoice_amount + $this->taxInvoice->tax_amount;
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejecter()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function statusHistory()
    {
        return $this->hasMany(InvoiceStatusHistory::class)->orderBy('created_at', 'desc');
    }

    /**
     * Define allowed state transitions
     */
    public static function allowedTransitions(): array
    {
        return [
            self::STATUS_DRAFT => [self::STATUS_TAX_GENERATED],
            self::STATUS_TAX_GENERATED => [self::STATUS_SUBMITTED],
            self::STATUS_SUBMITTED => [self::STATUS_APPROVED, self::STATUS_REJECTED],
            self::STATUS_APPROVED => [self::STATUS_PAID, self::STATUS_REJECTED],
            self::STATUS_REJECTED => [self::STATUS_DRAFT],
        ];
    }
}
