<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'phone',
        'billing_address',
        'tax_number',
        'contact_person',
    ];

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }

    public function tenders()
    {
        return $this->hasMany(Tender::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
