<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use RuntimeException;

class CampaignLead extends Model
{
    protected $fillable = [
        'full_name',
        'date_of_birth',
        'place_of_birth',
        'phone_number',
        'email',
        'message',
        'source',
        'status',
        'owner_id',
        'assigned_to',
        'lead_code',
    ];

    protected static function booted(): void
    {
        static::creating(function (CampaignLead $lead) {
            if (empty($lead->lead_code)) {
                $lead->lead_code = static::generateUniqueLeadCode();
            }
        });

        static::saving(function (CampaignLead $lead) {
            if ($lead->exists && $lead->getOriginal('lead_code') !== null && $lead->isDirty('lead_code')) {
                $lead->lead_code = $lead->getOriginal('lead_code');
            }
        });
    }

    /**
     * Generate a unique lead code in the format "LD-" + 8 uppercase alphanumeric characters.
     *
     * @param int $maxAttempts Maximum number of attempts to generate a unique code.
     * @return string
     * @throws RuntimeException If a unique code cannot be generated within the max attempts.
     */
    public static function generateUniqueLeadCode(int $maxAttempts = 5): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $charactersLength = strlen($characters);

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $code = 'LD-';
            for ($i = 0; $i < 8; $i++) {
                $code .= $characters[random_int(0, $charactersLength - 1)];
            }

            if (!static::where('lead_code', $code)->exists()) {
                return $code;
            }
        }

        throw new RuntimeException(
            "Failed to generate a unique lead code after {$maxAttempts} attempts."
        );
    }

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(LeadNote::class, 'campaign_lead_id');
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(LeadFollowUp::class, 'campaign_lead_id');
    }

    public function scopeSearch($query, ?string $term, bool $canViewPii = true)
    {
        if (!$term) return $query;
        return $query->where(function ($q) use ($term, $canViewPii) {
            $q->where('full_name', 'like', "%{$term}%")
              ->orWhere('lead_code', 'like', "%{$term}%");

            if ($canViewPii) {
                $q->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('phone_number', 'like', "%{$term}%");
            }
        });
    }

    public function scopeFilterStatus($query, ?string $status)
    {
        return $status ? $query->where('status', $status) : $query;
    }

    public function scopeFilterSource($query, ?string $source)
    {
        return $source ? $query->where('source', $source) : $query;
    }

    public function scopeFilterDateRange($query, ?string $from, ?string $to)
    {
        if ($from) $query->where('created_at', '>=', $from);
        if ($to) $query->where('created_at', '<=', $to . ' 23:59:59');
        return $query;
    }

    public function scopeOwnedByOrAssignedTo($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('owner_id', $userId)
              ->orWhere('assigned_to', $userId);
        });
    }
}
