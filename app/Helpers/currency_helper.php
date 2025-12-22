<?php

use App\Models\Currency;

if (!function_exists('getCurrentCurrency')) {
    function getCurrentCurrency()
    {
        $currencyCode = session('currency', Currency::getDefaultCurrency()->code);
        return Currency::where('code', $currencyCode)->first() ?? Currency::getDefaultCurrency();
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
