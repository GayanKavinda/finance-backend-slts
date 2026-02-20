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

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'job_id');
    }
}
