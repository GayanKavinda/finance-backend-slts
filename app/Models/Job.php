<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    const STATUS_PENDING = 'Pending';
    const STATUS_IN_PROGRESS = 'In Progress';
    const STATUS_COMPLETED = 'Completed';

    protected $table = 'project_jobs';

    protected $fillable = [
        'tender_id',
        'customer_id',
        'contractor_id',
        'name',
        'project_value',
        'description',
        'status',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function contractorBill()
    {
        return $this->hasOne(ContractorBill::class);
    }
}
