<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignLead extends Model
{
    protected $fillable = [
        'full_name',
        'date_of_birth',
        'place_of_birth',
        'phone_number',
        'email',
        'message',
        'source',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }
}
