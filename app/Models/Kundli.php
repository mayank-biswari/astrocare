<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kundli extends Model
{
    protected $fillable = [
        'user_id', 'name', 'birth_date', 'birth_time', 'birth_place', 'type', 
        'chart_data', 'report', 'pdf_path', 'amount', 'currency', 'status'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'birth_time' => 'datetime:H:i',
        'chart_data' => 'array',
        'amount' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
