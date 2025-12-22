<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'code',
        'name',
        'symbol',
        'exchange_rate',
        'is_active',
        'is_default',
        'sort_order'
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:4',
        'is_active' => 'boolean',
        'is_default' => 'boolean'
    ];

    public static function getActiveCurrencies()
    {
        return self::where('is_active', true)->orderBy('sort_order')->get();
    }

    public static function getDefaultCurrency()
    {
        return self::where('is_default', true)->first() ?? self::where('code', 'INR')->first();
    }

    public static function convert($amount, $fromCode, $toCode)
    {
        if ($fromCode === $toCode) {
            return $amount;
        }

        $fromCurrency = self::where('code', $fromCode)->first();
        $toCurrency = self::where('code', $toCode)->first();

        if (!$fromCurrency || !$toCurrency) {
            return $amount;
        }

        // Convert to base currency (INR) first, then to target currency
        $baseAmount = $amount / $fromCurrency->exchange_rate;
        return $baseAmount * $toCurrency->exchange_rate;
    }
}
