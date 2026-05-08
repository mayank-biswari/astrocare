<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadFollowUp extends Model
{
    protected $fillable = [
        'campaign_lead_id', 'user_id', 'description',
        'scheduled_date', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(CampaignLead::class, 'campaign_lead_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isOverdue(): bool
    {
        return !$this->completed_at && $this->scheduled_date->lt(today());
    }

    public function scopeOverdue($query)
    {
        return $query->whereNull('completed_at')
                     ->where('scheduled_date', '<', now()->startOfDay()->toDateTimeString());
    }

    public function scopeUpcoming($query, int $days = 7)
    {
        return $query->whereNull('completed_at')
                     ->where('scheduled_date', '>=', now()->startOfDay()->toDateTimeString())
                     ->where('scheduled_date', '<=', now()->addDays($days)->endOfDay()->toDateTimeString());
    }
}
