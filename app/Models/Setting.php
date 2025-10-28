<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];
    public $timestamps = false;

    /**
     * Get a setting value with cache and fallback.
     */
    public static function get($key, $default = null)
    {
        return Cache::rememberForever("setting_{$key}", function () use ($key, $default) {
            $setting = self::where('key', $key)->value('value');

            if (!is_null($setting)) {
                return $setting;
            }

            // Fallback: check config or .env
            return config("app.$key", env(strtoupper($key), $default));
        });
    }

    /**
     * Set or update a setting and clear cache.
     */
    public static function set($key, $value)
    {
        $record = self::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting_{$key}");
        return $record;
    }

    /**
     * Bulk save multiple settings at once.
     */
    public static function saveMany(array $data)
    {
        foreach ($data as $key => $value) {
            self::set($key, $value);
        }
    }
}
