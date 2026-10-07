<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractorBillPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'contractor_bill_id',
        'amount',
        'retention_amount',
        'milestone_name',
        'payment_method',
        'payment_reference',
        'bank_name',
        'payment_date',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'retention_amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function bill()
    {
        return $this->belongsTo(ContractorBill::class, 'contractor_bill_id');
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
