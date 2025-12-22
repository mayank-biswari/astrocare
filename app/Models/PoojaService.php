<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoojaService extends Model
{
    protected $fillable = [
        'name', 'slug', 'icon', 'description', 'price', 'currency', 
        'includes', 'benefits', 'duration', 'category', 'is_active'
    ];

    protected $casts = [
        'includes' => 'array',
        'benefits' => 'array',
        'price' => 'decimal:2',
        'is_active' => 'boolean'
    ];
}
