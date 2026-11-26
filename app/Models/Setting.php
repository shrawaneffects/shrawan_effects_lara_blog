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
            $relativePath = str_starts_with($clean, 'storage/') ? substr($clean, 8) : $clean;

            if (file_exists(public_path('storage/' . $relativePath)) || file_exists(storage_path('app/public/' . $relativePath))) {
                return asset('storage/' . $relativePath);
            }
        }

        // Fallback: look for any valid image in settings/ directory
        $settingsDir = storage_path('app/public/settings');
        if (is_dir($settingsDir)) {
            $files = glob($settingsDir . '/*.{png,jpg,jpeg,webp,svg}', GLOB_BRACE);
            if (!empty($files)) {
                $basename = basename($files[0]);
                return asset('storage/settings/' . $basename);
            }
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
            $relativePath = str_starts_with($clean, 'storage/') ? substr($clean, 8) : $clean;

            if (file_exists(public_path('storage/' . $relativePath)) || file_exists(storage_path('app/public/' . $relativePath))) {
                return asset('storage/' . $relativePath);
            }
        }

        // Fallback: look for any valid image in settings/ directory
        $settingsDir = storage_path('app/public/settings');
        if (is_dir($settingsDir)) {
            $files = glob($settingsDir . '/*.{png,jpg,jpeg,webp,svg,ico}', GLOB_BRACE);
            if (!empty($files)) {
                $basename = basename($files[0]);
                return asset('storage/settings/' . $basename);
            }
        }

        return null;
    }
}
