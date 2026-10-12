<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value', 'type'];

    public const KEY_MAINTENANCE_MODE = 'maintenance_mode';

    /**
     * Read a setting with a typed default.
     */
    public static function get(string $key, $default = null)
    {
        $row = static::where('key', $key)->first();
        if (!$row) {
            return $default;
        }

        return match ($row->type) {
            'boolean' => filter_var($row->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $row->value,
            'json' => json_decode($row->value, true),
            default => $row->value,
        };
    }

    /**
     * Write a setting, inferring its storage type.
     */
    public static function set(string $key, $value): void
    {
        [$raw, $type] = match (true) {
            is_bool($value) => [$value ? '1' : '0', 'boolean'],
            is_int($value) => [(string) $value, 'integer'],
            is_array($value) => [json_encode($value), 'json'],
            default => [(string) $value, 'string'],
        };

        static::updateOrCreate(
            ['key' => $key],
            ['value' => $raw, 'type' => $type]
        );
    }

    public static function isMaintenanceMode(): bool
    {
        return (bool) static::get(self::KEY_MAINTENANCE_MODE, false);
    }
}
