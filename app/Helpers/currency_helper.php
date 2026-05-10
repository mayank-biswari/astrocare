<?php

use App\Models\Currency;

if (!function_exists('getCurrentCurrency')) {
    function getCurrentCurrency()
    {
        $defaultCurrency = Currency::getDefaultCurrency();
        if (!$defaultCurrency) {
            // Fallback if no default currency exists
            return (object) ['code' => 'USD', 'symbol' => '$', 'name' => 'US Dollar'];
        }

        $currencyCode = session('currency', $defaultCurrency->code);
        return Currency::where('code', $currencyCode)->first() ?? $defaultCurrency;
    }
}

if (!function_exists('convertPrice')) {
    function convertPrice($amount, $fromCurrency = 'INR')
    {
        $toCurrency = getCurrentCurrency();
        return Currency::convert($amount, $fromCurrency, $toCurrency->code);
    }
}

if (!function_exists('formatPrice')) {
    function formatPrice($amount, $fromCurrency = 'INR')
    {
        $currency = getCurrentCurrency();
        $convertedAmount = Currency::convert($amount, $fromCurrency, $currency->code);
        return $currency->symbol . number_format($convertedAmount, 2);
    }
}

if (!function_exists('currencySymbol')) {
    function currencySymbol()
    {
        return getCurrentCurrency()->symbol;
    }
}

if (!function_exists('currencyCode')) {
    function currencyCode()
    {
        return getCurrentCurrency()->code;
    }
}

if (!function_exists('getCurrencySymbol')) {
    /**
     * Get the currency symbol for a given currency code.
     * Looks up the symbol from the currencies table.
     * Falls back to the code itself if not found.
     *
     * @param string $code Currency code (e.g., 'USD', 'INR', 'EUR')
     * @return string The currency symbol (e.g., '$', '₹', '€')
     */
    function getCurrencySymbol($code)
    {
        static $cache = [];

        if (isset($cache[$code])) {
            return $cache[$code];
        }

        $currency = Currency::where('code', $code)->first();
        $symbol = $currency ? $currency->symbol : $code;
        $cache[$code] = $symbol;

        return $symbol;
    }
}
