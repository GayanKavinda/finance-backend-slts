<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'billing_address',
        'tax_number',
        'contact_person',
    ];

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
