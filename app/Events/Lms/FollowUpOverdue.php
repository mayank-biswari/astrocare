<?php

namespace App\Events\Lms;

use App\Models\LeadFollowUp;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FollowUpOverdue implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public LeadFollowUp $followUp
    ) {}

    /**
     * Get the channel the event should broadcast on.
     * Broadcasts only to the follow-up author.
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("lms.notifications.{$this->followUp->user_id}");
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'type' => 'follow_up_overdue',
            'title' => 'Overdue Follow-Up',
            'message' => "Follow-up for {$this->followUp->lead->full_name} is overdue",
            'lead_id' => $this->followUp->campaign_lead_id,
            'follow_up_id' => $this->followUp->id,
            'created_at' => now()->toISOString(),
        ];
    }
}
