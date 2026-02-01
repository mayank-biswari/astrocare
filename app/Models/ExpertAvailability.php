<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpertAvailability extends Model
{
    protected $table = 'expert_availability';
    protected $fillable = ['user_id', 'date', 'is_available'];
    protected $casts = ['date' => 'date', 'is_available' => 'boolean'];
}
