<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'event',
        'description',
        'ip_address',
        'user_agent',
        'details',
        'severity',
    ];

    protected function casts(): array
    {
        return [
            'details' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper to quickly record an audit log event
     */
    public static function record(string $event, string $description, ?int $userId = null, array $details = [], string $severity = 'info', ?Request $request = null): self
    {
        $req = $request ?: request();

        return static::create([
            'user_id' => $userId ?: (auth()->check() ? auth()->id() : null),
            'event' => $event,
            'description' => $description,
            'ip_address' => $req ? $req->ip() : null,
            'user_agent' => $req ? substr($req->userAgent() ?? '', 0, 500) : null,
            'details' => $details,
            'severity' => $severity,
        ]);
    }
}
