<?php

namespace App\Models\UsefulTools;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsefulToolUsageLog extends Model
{
    use HasFactory;

    protected $table = 'useful_tool_usage_logs';

    protected $fillable = [
        'user_id',
        'tool_type',
        'provider',
        'operation',
        'query_string',
        'records_count',
        'credits_used',
        'estimated_cost_usd',
        'response_code',
        'execution_time_ms',
        'is_success',
        'error_message',
    ];

    protected $casts = [
        'records_count' => 'integer',
        'credits_used' => 'decimal:4',
        'estimated_cost_usd' => 'decimal:4',
        'response_code' => 'integer',
        'execution_time_ms' => 'integer',
        'is_success' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function logUsage(
        int $userId,
        string $toolType,
        string $provider,
        string $operation,
        ?string $query = null,
        int $recordsCount = 0,
        float $creditsUsed = 0.0,
        float $costUsd = 0.0,
        ?int $responseCode = 200,
        ?int $executionTimeMs = null,
        bool $isSuccess = true,
        ?string $errorMessage = null
    ): self {
        return self::create([
            'user_id' => $userId,
            'tool_type' => $toolType,
            'provider' => $provider,
            'operation' => $operation,
            'query_string' => $query,
            'records_count' => $recordsCount,
            'credits_used' => $creditsUsed,
            'estimated_cost_usd' => $costUsd,
            'response_code' => $responseCode,
            'execution_time_ms' => $executionTimeMs,
            'is_success' => $isSuccess,
            'error_message' => $errorMessage,
        ]);
    }
}
