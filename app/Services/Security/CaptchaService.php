<?php

namespace App\Services\Security;

use Illuminate\Support\Facades\Cache;

class CaptchaService
{
    protected const FALLBACK_SECRET = 'shrawaneffects_auth_captcha_secret_2026_tokyotech';

    /**
     * Handcrafted geometric SVG vector paths (16x24 viewport) so NO readable text exists in DOM/SVG.
     */
    protected const GLYPH_PATHS = [
        '0' => 'M 8,2 C 13,2 14,6.5 14,12 C 14,17.5 13,22 8,22 C 3,22 2,17.5 2,12 C 2,6.5 3,2 8,2 Z',
        '1' => 'M 4,6 L 9,2 L 9,22 M 4,22 L 13,22',
        '2' => 'M 2.5,6.5 C 2.5,3 6,2 8.5,2 C 12,2 13.5,4.2 13.5,7.2 C 13.5,11 8,14.5 2.5,21.5 L 14,21.5',
        '3' => 'M 2.5,4.5 C 4.5,2 13,2 13,6.5 C 13,10 9.5,11.5 6,11.5 C 10,11.5 13.5,13 13.5,17 C 13.5,21 6.5,22.5 2.5,20',
        '4' => 'M 11,22 L 11,2 L 2,15 L 14.5,15',
        '5' => 'M 13,2.5 L 3.5,2.5 L 2.8,10.5 C 4.5,9.2 13.5,9.2 13.5,15.5 C 13.5,20.5 6.8,22 2.5,20',
        '6' => 'M 12.5,3.5 C 8,1 3,5 2.5,12.5 C 2,19 4.5,22 8.5,22 C 12,22 14,19.5 14,16 C 14,12.5 11.5,10.5 8,10.5 C 5,10.5 3,12 2.5,14',
        '7' => 'M 2,2.5 L 14,2.5 L 5.5,22',
        '8' => 'M 8,11.5 C 4,11.5 2.5,9.5 2.5,6.5 C 2.5,3.5 4.8,2 8,2 C 11.2,2 13.5,3.5 13.5,6.5 C 13.5,9.5 12,11.5 8,11.5 C 3.5,11.5 2,13.5 2,17 C 2,20.2 4.5,22 8,22 C 11.5,22 14,20.2 14,17 C 14,13.5 12.5,11.5 8,11.5',
        '9' => 'M 13.5,9.5 C 13,12.5 10.5,14 7.5,14 C 4,14 2,11.5 2,8 C 2,4 4.5,2 8,2 C 11.5,2 13.5,4.8 13.5,10 C 13.5,16.5 11.5,22 4.5,21.5',
        '+' => 'M 8,5 L 8,19 M 2,12 L 14,12',
        '-' => 'M 2.5,12 L 13.5,12',
        '=' => 'M 2.5,9 L 13.5,9 M 2.5,15 L 13.5,15',
        '?' => 'M 3.5,6.5 C 3.5,3 6,2 8.5,2 C 11.5,2 13,4 13,6.8 C 13,9.5 11,10.8 8.5,12 L 8.5,16 M 8.5,19.5 L 8.5,21.5',
    ];

    protected static function getSecretKey(): string
    {
        $key = config('app.key');
        return !empty($key) ? (string) $key : self::FALLBACK_SECRET;
    }

    /**
     * Generate a cryptographic form render timestamp signature to block sub-second bot posts.
     *
     * @return array{ts: int, sig: string}
     */
    public static function generateFormTimestamp(): array
    {
        $ts = time();
        $sig = hash_hmac('sha256', "form_ts|{$ts}", self::getSecretKey());

        return ['ts' => $ts, 'sig' => $sig];
    }

    /**
     * Verify form timestamp signature and ensure minimum human fill delay.
     */
    public static function verifyFormTimestamp(int|string|null $ts, ?string $sig, int $minSeconds = 1): bool
    {
        if (empty($ts) || empty($sig)) {
            return true; // Skip only if legacy request omitted it; Honeypot + CAPTCHA still enforce
        }

        $timeVal = intval($ts);
        $expectedSig = hash_hmac('sha256', "form_ts|{$timeVal}", self::getSecretKey());
        if (!hash_equals($expectedSig, (string) $sig)) {
            return false;
        }

        $elapsed = time() - $timeVal;
        return $elapsed >= $minSeconds && $elapsed <= 7200;
    }

    public static function generateChallenge(): array
    {
        return self::generate();
    }

    /**
     * Generate a randomized distorted Vector-SVG math CAPTCHA challenge.
     * Note: Plaintext expression is NOT exposed in returned array so bots cannot parse HTML/JSON.
     *
     * @return array{image_data_uri: string, token: string, timestamp: int, nonce: string, form_ts_sig: string}
     */
    public static function generate(): array
    {
        $ops = ['+', '-'];
        $op = $ops[array_rand($ops)];

        if ($op === '+') {
            $n1 = random_int(3, 18);
            $n2 = random_int(2, 14);
            $ans = $n1 + $n2;
        } else {
            $n1 = random_int(9, 24);
            $n2 = random_int(2, $n1 - 1);
            $ans = $n1 - $n2;
        }

        $expression = "{$n1}{$op}{$n2}=?";
        $svg = self::renderVectorSvg($expression);
        $imageDataUri = 'data:image/svg+xml;base64,' . base64_encode($svg);

        $time = time();
        $nonce = bin2hex(random_bytes(8));
        $token = hash_hmac('sha256', "{$ans}|{$time}|{$nonce}", self::getSecretKey());
        $formTsSig = hash_hmac('sha256', "form_ts|{$time}", self::getSecretKey());

        return [
            'image_data_uri' => $imageDataUri,
            'token' => $token,
            'timestamp' => $time,
            'nonce' => $nonce,
            'form_ts_sig' => $formTsSig,
        ];
    }

    /**
     * Verify the user's captcha answer with strict single-use HMAC validation (no insecure fallbacks).
     */
    public static function verify($answer, $token = null, $timestamp = null, $nonce = null): bool
    {
        if ($answer === '' || $answer === null || empty($token) || empty($timestamp) || empty($nonce)) {
            return false;
        }

        $cleanAnswer = trim((string) $answer);
        if (!is_numeric($cleanAnswer)) {
            return false;
        }

        $ts = intval($timestamp);
        $now = time();

        // Enforce 15-minute validity window
        if (($now - $ts) > 900 || $ts > ($now + 60)) {
            return false;
        }

        $expected = hash_hmac('sha256', "{$cleanAnswer}|{$ts}|{$nonce}", self::getSecretKey());
        if (!hash_equals($expected, (string) $token)) {
            return false;
        }

        // Enforce single-use replay protection
        if (!Cache::add("captcha_used_{$token}", true, now()->addMinutes(20))) {
            return false;
        }

        return true;
    }

    /**
     * Render distorted geometric SVG paths with interference curves & speckle noise.
     */
    protected static function renderVectorSvg(string $expression): string
    {
        $chars = str_split($expression);
        $charCount = count($chars);
        $slotWidth = 22;
        $width = max(165, 24 + ($charCount * $slotWidth));
        $height = 48;

        $palette = ['#00ff66', '#e4fff0', '#00f0ff', '#a3ffce'];

        $noiseLines = '';
        for ($i = 0; $i < 4; $i++) {
            $x1 = random_int(0, 25);
            $y1 = random_int(4, $height - 4);
            $cx1 = random_int(40, (int) ($width * 0.5));
            $cy1 = random_int(-6, $height + 6);
            $cx2 = random_int((int) ($width * 0.5), $width - 25);
            $cy2 = random_int(-6, $height + 6);
            $x2 = random_int($width - 25, $width);
            $y2 = random_int(4, $height - 4);
            $color = $palette[array_rand($palette)];
            $sw = random_int(10, 16) / 10;
            $noiseLines .= "<path d=\"M {$x1},{$y1} C {$cx1},{$cy1} {$cx2},{$cy2} {$x2},{$y2}\" fill=\"none\" stroke=\"{$color}\" stroke-width=\"{$sw}\" stroke-opacity=\"0.28\" />";
        }

        $dots = '';
        for ($i = 0; $i < 24; $i++) {
            $dx = random_int(4, $width - 4);
            $dy = random_int(4, $height - 4);
            $r = random_int(7, 14) / 10;
            $color = $palette[array_rand($palette)];
            $dots .= "<circle cx=\"{$dx}\" cy=\"{$dy}\" r=\"{$r}\" fill=\"{$color}\" fill-opacity=\"0.35\" />";
        }

        $glyphMarkup = '';
        $cursorX = 14;
        foreach ($chars as $ch) {
            if (!isset(self::GLYPH_PATHS[$ch])) {
                $cursorX += 14;
                continue;
            }
            $path = self::GLYPH_PATHS[$ch];
            $rot = random_int(-9, 9);
            $dy = 11 + random_int(-3, 3);
            $scale = random_int(94, 108) / 100;
            $color = ($ch === '=' || $ch === '?') ? '#00f0ff' : $palette[array_rand($palette)];
            $strokeW = number_format(random_int(21, 25) / 10, 1);

            $glyphMarkup .= "<g transform=\"translate({$cursorX}, {$dy}) rotate({$rot}, 8, 12) scale({$scale})\">";
            $glyphMarkup .= "<path d=\"{$path}\" fill=\"none\" stroke=\"{$color}\" stroke-width=\"{$strokeW}\" stroke-linecap=\"round\" stroke-linejoin=\"round\" />";
            $glyphMarkup .= '</g>';

            $cursorX += ($ch === '1' || $ch === '-') ? 19 : $slotWidth;
        }

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$width}" height="{$height}" viewBox="0 0 {$width} {$height}" role="img" aria-label="Security math challenge">
  <defs>
    <linearGradient id="bg" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#071206"/>
      <stop offset="100%" stop-color="#11220c"/>
    </linearGradient>
    <pattern id="grid" width="12" height="12" patternUnits="userSpaceOnUse">
      <path d="M 12 0 L 0 0 0 12" fill="none" stroke="#00ff66" stroke-width="0.4" stroke-opacity="0.14"/>
    </pattern>
  </defs>
  <rect width="100%" height="100%" rx="6" fill="url(#bg)"/>
  <rect width="100%" height="100%" rx="6" fill="url(#grid)"/>
  {$noiseLines}
  {$dots}
  {$glyphMarkup}
</svg>
SVG;
    }
}
