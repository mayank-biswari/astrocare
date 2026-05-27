<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class ServiceSubmission extends Model
{
    protected $fillable = [
        'service_id', 'service_tier_id', 'user_id',
        'form_data', 'amount', 'currency',
        'status', 'payment_status',
        'response', 'report_path', 'assigned_to',
        'scheduled_at', 'completed_at',
    ];

    protected $casts = [
        'form_data' => 'array',
        'amount' => 'decimal:2',
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // ─── Relationships ─────────────────────────────────────────────

    /**
     * Get the service this submission belongs to.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the tier selected for this submission.
     */
    public function tier(): BelongsTo
    {
        return $this->belongsTo(ServiceTier::class, 'service_tier_id');
    }

    /**
     * Get the user who made this submission.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order associated with this submission.
     */
    public function order(): MorphOne
    {
        return $this->morphOne(Order::class, 'orderable');
    }
}
