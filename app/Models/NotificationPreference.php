<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'email_alerts',
        'browser_alerts',
        'critical_alerts',
    ];

    protected $casts = [
        'email_alerts' => 'boolean',
        'browser_alerts' => 'boolean',
        'critical_alerts' => 'boolean',
    ];

    /**
     * Get (creating sensible defaults) or create preferences for a user.
     */
    public static function forUser(int $userId): self
    {
        return static::firstOrCreate(
            ['user_id' => $userId],
            [
                'email_alerts' => true,
                'browser_alerts' => true,
                'critical_alerts' => true,
            ]
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
