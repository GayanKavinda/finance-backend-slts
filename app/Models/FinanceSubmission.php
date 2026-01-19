<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceSubmission extends Model
{
    protected $fillable = [
        'submission_type',
        'document_ids',
        'submitted_by',
        'approved_by',
        'status',
    ];

    protected $casts = [
        'document_ids' => 'array',
    ];

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
