<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectJob extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'Pending';
    const STATUS_IN_PROGRESS = 'In Progress';
    const STATUS_COMPLETED = 'Completed';

    protected $fillable = [
        'tender_id',
        'customer_id',
        'name',
        'project_value',
        'description',
        'status',
        'selected_contractor_id',
        'contractor_quote_amount',
        'contractor_quote_date',
        'work_start_date',
        'work_completion_date',
    ];

    protected $casts = [
        'contractor_quote_date' => 'date',
        'work_start_date' => 'date',
        'work_completion_date' => 'date',
    ];

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function selectedContractor()
    {
        return $this->belongsTo(Contractor::class, 'selected_contractor_id');
    }

    public function quotations()
    {
        return $this->hasMany(ContractorQuotation::class, 'job_id');
    }

    public function bills()
    {
        return $this->hasMany(ContractorBill::class, 'job_id');
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'job_id');
    }
}
