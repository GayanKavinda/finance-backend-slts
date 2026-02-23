<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractorQuotation extends Model
{
    const STATUS_SUBMITTED = 'Submitted';
    const STATUS_SELECTED = 'Selected';
    const STATUS_REJECTED = 'Rejected';

    protected $fillable = [
        'job_id',
        'contractor_id',
        'quotation_amount',
        'quotation_date',
        'work_scope',
        'estimated_days',
        'notes',
        'status',
        'entered_by',
    ];

    protected $casts = [
        'quotation_date' => 'date',
    ];

    public function job()
    {
        return $this->belongsTo(ProjectJob::class, 'job_id');
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'entered_by');
    }
}
