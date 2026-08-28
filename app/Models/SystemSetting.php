<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value with caching.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever("system_setting:{$key}", function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            if (!$setting) {
                return $default;
            }

            $decoded = json_decode($setting->value, true);
            return json_last_error() === JSON_ERROR_NONE ? $decoded : $setting->value;
        });
    }

    /**
     * Set a setting value and update cache.
     */
    public static function set(string $key, mixed $value): void
    {
        $encoded = is_array($value) || is_bool($value) || is_numeric($value) 
            ? json_encode($value) 
            : (string) $value;

        static::updateOrCreate(
            ['key' => $key],
            ['value' => $encoded]
        );

        Cache::forget("system_setting:{$key}");
        Cache::forever("system_setting:{$key}", $value);
    }

    /**
     * Check if maintenance mode is active.
     */
    public static function isMaintenanceMode(): bool
    {
        return (bool) static::get('maintenance_mode', false);
    }

    /**
     * Get all maintenance mode information.
     */
    public static function getMaintenanceDetails(): array
    {
        return [
            'is_active' => static::isMaintenanceMode(),
            'message' => (string) static::get('maintenance_message', 'Sistem sedang dalam proses pemeliharaan berkala untuk peningkatan performa.'),
            'estimated_finish' => static::get('maintenance_estimated_finish', null),
            'updated_at' => static::get('maintenance_updated_at', null),
            'updated_by_name' => static::get('maintenance_updated_by_name', 'Warehouse Manager'),
        ];
    }
}
