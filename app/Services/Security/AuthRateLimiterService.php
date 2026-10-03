<?php

namespace App\Services\Security;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class AuthRateLimiterService
{
    public const MAX_DAILY_ATTEMPTS = 5;

    /**
     * Get the cache key for daily attempt tracking on the same date.
     */
    protected static function key(string $identifier, string $action = 'auth'): string
    {
        $date = Carbon::now()->format('Y-m-d');
        $sanitized = strtolower(trim($identifier));
        return "daily_limit_{$date}_{$action}_" . md5($sanitized);
    }

    /**
     * Get current count of attempts made today.
     */
    public static function getDailyAttempts(string $identifier, string $action = 'auth'): int
    {
        return (int) Cache::get(static::key($identifier, $action), 0);
    }

    /**
     * Check if the identifier has reached the maximum daily limit (5 attempts).
     */
    public static function isDailyLockedOut(string $identifier, string $action = 'auth', int $max = self::MAX_DAILY_ATTEMPTS): bool
    {
        return static::getDailyAttempts($identifier, $action) >= $max;
    }

    /**
     * Get remaining attempts allowed for today.
     */
    public static function getRemainingDailyAttempts(string $identifier, string $action = 'auth', int $max = self::MAX_DAILY_ATTEMPTS): int
    {
        $current = static::getDailyAttempts($identifier, $action);
        return max(0, $max - $current);
    }

    /**
     * Record a failed attempt for today.
     */
    public static function recordFailedAttempt(string $identifier, string $action = 'auth'): int
    {
        $key = static::key($identifier, $action);
        $secondsUntilMidnight = max(60, Carbon::now()->diffInSeconds(Carbon::now()->endOfDay()));

        if (!Cache::has($key)) {
            Cache::put($key, 1, $secondsUntilMidnight);
            return 1;
        }

        $attempts = Cache::increment($key);
        return (int) $attempts;
    }

    /**
     * Clear the daily attempt counter upon successful verification.
     */
    public static function clearDailyAttempts(string $identifier, string $action = 'auth'): void
    {
        Cache::forget(static::key($identifier, $action));
    }

    /**
     * Get user-friendly lockout message with attempt count.
     */
    public static function getLockoutMessage(int $max = self::MAX_DAILY_ATTEMPTS): string
    {
        return "Daily security limit reached. You are only allowed {$max} attempts per calendar day. Please try again tomorrow or request a password reset.";
    }
}
