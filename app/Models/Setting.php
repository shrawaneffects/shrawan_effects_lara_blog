<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    public static function getLogoUrl(): ?string
    {
        $logo = static::get('site_logo') ?: static::get('logo');
        if (!empty($logo)) {
            $logo = trim($logo);
            if (str_starts_with($logo, 'http://') || str_starts_with($logo, 'https://') || str_starts_with($logo, 'data:')) {
                return $logo;
            }
            $clean = ltrim($logo, '/');
            if (str_starts_with($clean, 'storage/')) {
                return asset($clean);
            }
            return asset('storage/' . $clean);
        }
        return null;
    }

    public static function getFaviconUrl(): ?string
    {
        $favicon = static::get('site_favicon') ?: static::get('favicon');
        if (!empty($favicon)) {
            $favicon = trim($favicon);
            if (str_starts_with($favicon, 'http://') || str_starts_with($favicon, 'https://') || str_starts_with($favicon, 'data:')) {
                return $favicon;
            }
            $clean = ltrim($favicon, '/');
            if (str_starts_with($clean, 'storage/')) {
                return asset($clean);
            }
            return asset('storage/' . $clean);
        }
        return null;
    }
}
