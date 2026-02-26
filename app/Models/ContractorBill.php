<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractorBill extends Model
{
    use HasFactory;
    const STATUS_DRAFT = 'Draft';
    const STATUS_VERIFIED = 'Verified';
    const STATUS_SUBMITTED = 'Submitted';
    const STATUS_APPROVED = 'Approved';
    const STATUS_PAID = 'Paid';
    const STATUS_REJECTED = 'Rejected';

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
        'submitted_by',
        'submitted_at',
        'approved_by',
        'approved_at',
        'paid_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'payment_reference',
        'bank_name',
        'payment_amount',
        'notes',
    ];

    protected $casts = [
        'bill_date' => 'date',
        'verified_at' => 'datetime',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
        'rejected_at' => 'datetime',
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

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function rejecter()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function documents()
    {
        return $this->hasMany(ContractorBillDocument::class);
    }
}
