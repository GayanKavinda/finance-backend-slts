<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contractor extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'tax_id',
        'contact',
        'bank_details',
        'bank_account_number',
        'bank_name',
        'status',
        'rating',
        'notes',
    ];

    public function jobs()
    {
        return $this->hasMany(ProjectJob::class, 'selected_contractor_id');
    }

    public function quotations()
    {
        return $this->hasMany(ContractorQuotation::class);
    }

    public function bills()
    {
        return $this->hasMany(ContractorBill::class);
    }
}
