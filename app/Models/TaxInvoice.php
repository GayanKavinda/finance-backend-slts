<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxInvoice extends Model
{
    protected $fillable = [
        'invoice_id',
        'tax_invoice_number',
        'tax_percentage',
        'tax_amount',
        'total_amount',
        'locked',
    ];

    protected $casts = [
        'locked' => 'boolean',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
