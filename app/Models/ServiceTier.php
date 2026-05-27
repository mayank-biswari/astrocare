<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceTier extends Model
{
    protected $fillable = [
        'service_id', 'name', 'slug', 'description',
        'price', 'currency', 'features', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    // ─── Relationships ─────────────────────────────────────────────

    /**
     * Get the service that owns this tier.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    // ─── Scopes ────────────────────────────────────────────────────

    /**
     * Scope to only active tiers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
