<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contractor extends Model
{
    protected $fillable = [
        'name',
        'tax_id',
        'address',
        'contact',
        'bank_details',
    ];

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }

    public function bills()
    {
        return $this->hasMany(ContractorBill::class);
    }
}
