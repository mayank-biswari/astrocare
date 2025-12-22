<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'supported_currencies',
        'credentials',
        'is_active',
        'is_test_mode',
        'sort_order'
    ];

    protected $casts = [
        'supported_currencies' => 'array',
        'credentials' => 'array',
        'is_active' => 'boolean',
        'is_test_mode' => 'boolean'
    ];

    public static function getActiveGateways($currency = null)
    {
        $query = self::where('is_active', true)->orderBy('sort_order');
        
        if ($currency) {
            $query->whereJsonContains('supported_currencies', $currency);
        }
        
        return $query->get();
    }

    public function supportsCurrency($currency)
    {
        return in_array($currency, $this->supported_currencies ?? []);
    }
}
