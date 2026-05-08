<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function notes(): HasMany
    {
        return $this->hasMany(LeadNote::class, 'campaign_lead_id');
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(LeadFollowUp::class, 'campaign_lead_id');
    }

    public function scopeSearch($query, ?string $term)
    {
        if (!$term) return $query;
        return $query->where(function ($q) use ($term) {
            $q->where('full_name', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('phone_number', 'like', "%{$term}%");
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
}
