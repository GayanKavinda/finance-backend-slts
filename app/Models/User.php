<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'city',
        'country',
        'postal_code',
        'date_of_birth',
        'gender',
        'company',
        'job_title',
        'tax_id',
        'avatar_path',
        'profile_updated_at',
        'profile_updated_by',
        'login_attempts',
        'lockout_until',
        'two_factor_enabled',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        // 'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'profile_updated_at' => 'datetime',
            'lockout_until' => 'datetime',
            'two_factor_enabled' => 'boolean',
        ];
    }

    /**
     * Prepare a date for array / JSON serialization.
     */
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d\TH:i:s.v\Z');
    }

    protected $appends = ['avatar_url'];

    public function getAvatarUrlAttribute(): ?string
    {
        if (!$this->avatar_path) {
            return null;
        }
        return Storage::url($this->avatar_path);
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function loginActivities()
    {
        return $this->hasMany(LoginActivity::class);
    }

    public function notificationPreference()
    {
        return $this->hasOne(NotificationPreference::class);
    }

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    /**
     * Resolve the user's notification preferences, creating defaults on first use.
     */
    public function preferences(): NotificationPreference
    {
        return NotificationPreference::forUser($this->id);
    }
}
