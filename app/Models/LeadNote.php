<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadNote extends Model
{
    protected $fillable = ['campaign_lead_id', 'user_id', 'body'];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(CampaignLead::class, 'campaign_lead_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
