<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prediction extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'dob',
        'time',
        'place',
        'type',
        'amount',
        'status',
        'report',
        'payment_status',
    ];

    protected $casts = [
        'dob' => 'date',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
