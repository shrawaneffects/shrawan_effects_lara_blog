<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailVerification extends Model
{
    protected $table = 'email_verifications';

    protected $fillable = [
        'user_id',
        'email',
        'code',
        'type',
        'token',
        'payload',
        'attempts',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'expires_at' => 'datetime',
            'attempts' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        // 1. Timezone-independent epoch check: if created_at exists, valid for 20 minutes (1200 seconds)
        if ($this->created_at) {
            $elapsedSeconds = time() - $this->created_at->getTimestamp();
            if ($elapsedSeconds >= 0 && $elapsedSeconds <= (20 * 60)) {
                return false;
            }
            if ($elapsedSeconds > (20 * 60)) {
                return true;
            }
        }

        if (!$this->expires_at) {
            return true;
        }

        // Compare epoch timestamp to eliminate MySQL / PHP timezone skew
        return time() > $this->expires_at->getTimestamp();
    }

    public function isValid(string $inputCode): bool
    {
        if ($this->isExpired()) {
            return false;
        }

        return hash_equals((string) $this->code, trim($inputCode));
    }
}
