<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'dob',
        'time',
        'place',
        'category',
        'question',
        'amount',
        'status',
        'answer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
