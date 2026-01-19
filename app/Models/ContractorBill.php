<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractorBill extends Model
{
    const STATUS_UPLOADED = 'Uploaded';
    const STATUS_VERIFIED = 'Verified';
    const STATUS_APPROVED = 'Approved';

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
    ];

    protected $casts = [
        'bill_date' => 'date',
        'verified_at' => 'datetime',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
