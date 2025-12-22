<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'order_number', 'total_amount', 'currency', 'status', 'items',
        'shipping_address', 'payment_method', 'payment_status', 'shipped_at', 'delivered_at'
    ];

    protected $casts = [
        'items' => 'array',
        'shipping_address' => 'array',
        'total_amount' => 'decimal:2',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
