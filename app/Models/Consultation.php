<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    protected $fillable = [
        'user_id', 'service_id', 'type', 'scheduled_at', 'status', 'amount', 'currency', 'notes', 'reschedule_reason', 'suggestions', 'remedies', 'cancellation_reason'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'amount' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
