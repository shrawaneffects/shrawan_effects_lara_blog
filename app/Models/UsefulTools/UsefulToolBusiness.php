<?php

namespace App\Models\UsefulTools;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsefulToolBusiness extends Model
{
    use HasFactory;

    protected $table = 'useful_tool_businesses';

    protected $fillable = [
        'user_id',
        'job_id',
        'project_id',
        'source_platform',
        'source_record_id',
        'normalized_identifier',
        'name',
        'category',
        'description',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'phone',
        'email',
        'website',
        'listing_url',
        'rating',
        'review_count',
        'latitude',
        'longitude',
        'data_quality_status',
        'first_discovered_at',
        'last_checked_at',
    ];

    protected $casts = [
        'rating' => 'decimal:2',
        'review_count' => 'integer',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'first_discovered_at' => 'datetime',
        'last_checked_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(UsefulToolScrapingJob::class, 'job_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(UsefulToolProject::class, 'project_id');
    }

    /**
     * Compute a normalized identifier to safely prevent duplicate business entries.
     */
    public static function generateIdentifier(string $name, ?string $city = null, ?string $phone = null, ?string $website = null): string
    {
        $cleanName = strtolower(trim(preg_replace('/[^a-z0-9]/i', '', $name)));
        $cleanCity = strtolower(trim(preg_replace('/[^a-z0-9]/i', '', $city ?? '')));
        
        $cleanContact = '';
        if ($phone) {
            $digits = preg_replace('/[^0-9]/', '', $phone);
            $cleanContact = strlen($digits) >= 10 ? substr($digits, -10) : $digits;
        } elseif ($website) {
            $parsed = parse_url($website);
            $cleanContact = strtolower(preg_replace('/^www\./i', '', $parsed['host'] ?? $website));
        }

        return hash('sha256', "{$cleanName}|{$cleanCity}|{$cleanContact}");
    }
}
