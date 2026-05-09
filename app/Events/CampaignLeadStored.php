<?php

namespace App\Events;

use App\Models\CampaignLead;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CampaignLeadStored
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public CampaignLead $lead
    ) {}
}
