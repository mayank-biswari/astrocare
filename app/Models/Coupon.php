<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'start_date',
        'end_date',
        'usage_limit',
        'usage_count',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'usage_limit' => 'integer',
        'usage_count' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Scope to filter only active coupons.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter coupons that are currently valid.
     * Checks that the current date is within [start_date, end_date]
     * and that usage limit has not been reached (usage_limit = 0 means unlimited).
     */
    public function scopeValid(Builder $query): Builder
    {
        $today = now()->toDateString();

        return $query
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->where(function (Builder $q) {
                $q->where('usage_limit', 0)
                  ->orWhereColumn('usage_count', '<', 'usage_limit');
            });
    }
}
