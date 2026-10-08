<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Contractor extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * Note: contractor_code is intentionally excluded from $fillable to prevent
     * manual tampering or overrides during mass-assignment. It is auto-generated
     * strictly via the booted() model lifecycle event.
     */
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

    protected $casts = [
        'rating' => 'integer',
    ];

    /**
     * Boot the model.
     *
     * Generates a deterministic contractor_code automatically whenever a new
     * Contractor record is created.  The code is derived from the database
     * primary key so it is unique and stable across the entire lifecycle.
     *
     * Format: CON-000001  (zero-padded to 6 digits)
     *
     * The column is intentionally nullable at the DB level so the INSERT can
     * complete before the generated value is written — the application always
     * guarantees a non-null value after creation.
     */
    protected static function booted()
    {
        static::created(function (Contractor $contractor) {
            if (empty($contractor->contractor_code)) {
                $code = 'CON-' . str_pad($contractor->id, 6, '0', STR_PAD_LEFT);
                DB::table('contractors')
                    ->where('id', $contractor->id)
                    ->update(['contractor_code' => $code]);
                $contractor->contractor_code = $code;
            }
        });
    }

    /**
     * Scope a query to only include contractors with a valid contractor_code.
     */
    public function scopeWithCode($query)
    {
        return $query->whereNotNull('contractor_code');
    }

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
