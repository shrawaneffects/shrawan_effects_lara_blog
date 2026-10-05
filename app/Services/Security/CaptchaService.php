<?php

namespace App\Services\Security;

class CaptchaService
{
    protected const FALLBACK_SECRET = 'shrawaneffects_auth_captcha_secret_2026_tokyotech';

    /**
     * Get the HMAC secret key.
     */
    protected static function getSecretKey(): string
    {
        $key = config('app.key');
        if (!empty($key)) {
            return $key;
        }
        return self::FALLBACK_SECRET;
    }

    /**
     * Generate a new math captcha challenge (100% Free, zero external dependency).
     *
     * @return array{question: string, token: string, timestamp: int}
     */
    public static function generateChallenge(): array
    {
        return self::generate();
    }

    public static function generate(): array
    {
        $ops = ['+', '-'];
        $op = $ops[array_rand($ops)];

        if ($op === '+') {
            $n1 = random_int(3, 15);
            $n2 = random_int(2, 12);
            $ans = $n1 + $n2;
        } else {
            $n1 = random_int(8, 20);
            $n2 = random_int(1, $n1 - 1);
            $ans = $n1 - $n2;
        }

        $time = time();
        $token = hash_hmac('sha256', "{$ans}|{$time}", self::getSecretKey());

        // Also store in session as backup validation
        if (session()) {
            session([
                'captcha_expected_answer' => (string) $ans,
                'captcha_generated_at' => $time,
            ]);
        }

        return [
            'question' => "{$n1} {$op} {$n2}",
            'token' => $token,
            'timestamp' => $time,
        ];
    }

    /**
     * Verify the user's captcha answer.
     *
     * @param string|int|null $answer
     * @param string|null $token
     * @param int|string|null $timestamp
     * @return bool
     */
    public static function verify($answer, $token = null, $timestamp = null): bool
    {
        if ($answer === '' || $answer === null) {
            return false;
        }

        $cleanAnswer = trim(strtolower((string) $answer));
        $timestamp = intval($timestamp);

        // 1. Standard Server HMAC verification (10 minutes validity window)
        if (!empty($token) && !empty($timestamp)) {
            $now = time();
            if (($now - $timestamp) <= 600 && $timestamp <= ($now + 60)) {
                $expected = hash_hmac('sha256', "{$cleanAnswer}|{$timestamp}", self::getSecretKey());
                if (hash_equals($expected, (string) $token)) {
                    return true;
                }
            }
        }

        // 2. Session verification fallback
        if (session() && session()->has('captcha_expected_answer')) {
            $sessAns = (string) session('captcha_expected_answer');
            $sessTime = intval(session('captcha_generated_at', 0));
            if ((time() - $sessTime) <= 600 && $cleanAnswer === $sessAns) {
                // Clear to prevent replay
                session()->forget(['captcha_expected_answer', 'captcha_generated_at']);
                return true;
            }
        }

        // 3. Shyamo-style Base64 client math fallback token
        if (!empty($token)) {
            $decoded = @base64_decode((string) $token);
            if ($decoded && (strpos($decoded, ':shrawan_free_captcha') !== false || strpos($decoded, ':shyamo_free_captcha') !== false)) {
                $parts = explode(':', $decoded);
                if (count($parts) >= 3) {
                    $expectedAns = trim($parts[0]);
                    $tokenTime = intval($parts[1]);
                    if ((time() - $tokenTime) <= 600 && $cleanAnswer === $expectedAns) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
