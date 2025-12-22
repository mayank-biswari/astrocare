<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name', 'type', 'description', 'price', 'currency', 'features', 'is_active'
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2'
    ];

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }
}
