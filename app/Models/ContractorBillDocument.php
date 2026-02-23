<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractorBillDocument extends Model
{
    protected $fillable = [
        'contractor_bill_id',
        'document_type',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'uploaded_by',
        'description',
    ];

    public function bill()
    {
        return $this->belongsTo(ContractorBill::class, 'contractor_bill_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
