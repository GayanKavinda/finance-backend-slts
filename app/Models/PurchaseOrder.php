<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;
    const STATUS_DRAFT = 'Draft';
    const STATUS_APPROVED = 'Approved';

    protected $fillable = [
        'po_number',
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
        return $this->belongsTo(Job::class);
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
}
