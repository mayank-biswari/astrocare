<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pooja extends Model
{
    protected $fillable = [
        'user_id', 'name', 'type', 'category', 'description', 'amount', 'currency',
        'scheduled_at', 'location', 'status', 'special_requirements'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'amount' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
