<?php

namespace App\Listeners;

use App\Events\CampaignLeadStored;
use App\Mail\CampaignLeadNotificationMail;
use App\Models\CampaignSetting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendCampaignLeadNotification implements ShouldQueue
{
    public int $tries = 3;

    public function handle(CampaignLeadStored $event): void
    {
        $lead = $event->lead;

        // Guard: lead may have been deleted between dispatch and processing
        if (!$lead->exists) {
            return;
        }

        $email = CampaignSetting::getNotificationEmail($lead->source);

        if (!$email) {
            return;
        }

        Mail::to($email)->send(new CampaignLeadNotificationMail($lead));
    }
}
