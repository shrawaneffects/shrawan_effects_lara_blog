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
        $logo = static::get('site_logo');
        if ($logo) {
            $fullPath = public_path('storage/' . $logo);
            if (file_exists($fullPath)) {
                return asset('storage/' . $logo) . '?v=' . filemtime($fullPath);
            }
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($logo)) {
                return asset('storage/' . $logo);
            }
        }
        return null;
    }

    public static function getFaviconUrl(): ?string
    {
        $favicon = static::get('site_favicon');
        if ($favicon) {
            $fullPath = public_path('storage/' . $favicon);
            if (file_exists($fullPath)) {
                return asset('storage/' . $favicon) . '?v=' . filemtime($fullPath);
            }
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($favicon)) {
                return asset('storage/' . $favicon);
            }
        }
        return null;
    }
}
