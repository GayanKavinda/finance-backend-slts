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
    const STATUS_PAID = 'Paid';

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_TAX_GENERATED,
            self::STATUS_SUBMITTED,
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
    ];

    protected $casts = [
        'invoice_date' => 'date',
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


}
