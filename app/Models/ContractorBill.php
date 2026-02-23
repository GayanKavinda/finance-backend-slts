<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractorBill extends Model
{
    const STATUS_DRAFT = 'Draft';
    const STATUS_VERIFIED = 'Verified';
    const STATUS_APPROVED = 'Approved';
    const STATUS_PAID = 'Paid';

    protected $fillable = [
        'job_id',
        'contractor_id',
        'bill_number',
        'amount',
        'bill_date',
        'document_path',
        'status',
        'verified_by',
        'verified_at',
        'approved_by',
        'approved_at',
        'paid_at',
        'payment_reference',
        'notes',
    ];

    protected $casts = [
        'bill_date' => 'date',
        'verified_at' => 'datetime',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function job()
    {
        return $this->belongsTo(ProjectJob::class, 'job_id');
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function documents()
    {
        return $this->hasMany(ContractorBillDocument::class);
    }
}
