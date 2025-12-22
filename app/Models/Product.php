<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 'category', 'subcategory', 'description', 'price', 'currency', 
        'images', 'stock_quantity', 'show_stock', 'specifications', 'features', 'is_active', 'slug', 'is_featured', 'image'
    ];

    protected $casts = [
        'images' => 'array',
        'specifications' => 'array',
        'features' => 'array',
        'is_active' => 'boolean',
        'show_stock' => 'boolean',
        'price' => 'decimal:2'
    ];
}
