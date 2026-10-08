<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderStatusHistory extends Model
{
    public $timestamps = false;

    protected $table = 'purchase_order_status_history';

    protected $fillable = [
        'purchase_order_id',
        'old_status',
        'new_status',
        'changed_by',
        'reason',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d\TH:i:s.v\Z');
    }
}
