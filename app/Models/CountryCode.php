<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryCode extends Model
{
    protected $fillable = [
        'name',
        'iso_code',
        'dial_code',
        'phone_digits',
        'flag',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'phone_digits' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get all active country codes ordered by sort_order then name.
     */
    public static function getActive()
    {
        return static::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Find a country code by its dial code (e.g., "+91").
     */
    public static function findByDialCode(string $dialCode): ?self
    {
        return static::where('dial_code', $dialCode)->first();
    }

    /**
     * Get the display label (flag + dial_code).
     */
    public function getDisplayLabelAttribute(): string
    {
        return ($this->flag ? $this->flag . ' ' : '') . $this->dial_code;
    }
}
