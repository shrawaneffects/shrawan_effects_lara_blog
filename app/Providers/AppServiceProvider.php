<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        try {
            $tz = config('app.timezone', 'Asia/Kolkata');
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $dbTz = \App\Models\Setting::get('timezone', $tz);
                if (!empty($dbTz)) {
                    $tz = $dbTz;
                }
            }
            if (!empty($tz)) {
                date_default_timezone_set($tz);
                config(['app.timezone' => $tz]);
            }
        } catch (\Throwable $e) {
            date_default_timezone_set('Asia/Kolkata');
        }
    }
}
