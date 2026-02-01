<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsPageProductVariant extends Model
{
    protected $fillable = [
        'cms_page_product_id',
        'name',
        'price',
        'sale_price',
        'currency_prices',
        'stock_quantity',
        'manage_stock',
        'min_quantity',
        'quantity_step',
        'quantity_unit',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'currency_prices' => 'array',
        'stock_quantity' => 'integer',
        'manage_stock' => 'boolean',
        'min_quantity' => 'integer',
        'quantity_step' => 'integer',
        'is_active' => 'boolean'
    ];

    public function product()
    {
        return $this->belongsTo(CmsPageProduct::class, 'cms_page_product_id');
    }

    public function getEffectivePriceAttribute()
    {
        return $this->sale_price ?? $this->price;
    }

    public function getPriceForCurrency($currencyCode = null)
    {
        $currencyCode = $currencyCode ?? session('currency', Currency::getDefaultCurrency()->code);
        
        if ($this->currency_prices && is_array($this->currency_prices) && isset($this->currency_prices[$currencyCode])) {
            $currencyPrice = $this->currency_prices[$currencyCode];
            if (is_array($currencyPrice)) {
                $salePrice = $currencyPrice['sale_price'] ?? null;
                $price = $currencyPrice['price'] ?? null;
                
                if ($salePrice && $salePrice > 0) {
                    return $salePrice;
                }
                if ($price && $price > 0) {
                    return $price;
                }
            }
        }
        
        return $this->effective_price;
    }

    public function isInStock()
    {
        return !$this->manage_stock || $this->stock_quantity > 0;
    }
}
