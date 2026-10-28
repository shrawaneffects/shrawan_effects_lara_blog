<?php

namespace App\Models\UsefulTools;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class UsefulToolExport extends Model
{
    use HasFactory;

    protected $table = 'useful_tool_exports';

    protected $fillable = [
        'user_id',
        'tool_type',
        'filename',
        'file_path',
        'format',
        'total_records',
        'file_size_bytes',
        'download_token',
        'expires_at',
    ];

    protected $casts = [
        'total_records' => 'integer',
        'file_size_bytes' => 'integer',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function generateToken(): string
    {
        return Str::random(40) . bin2hex(random_bytes(12));
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
