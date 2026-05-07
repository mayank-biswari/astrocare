<?php

namespace App\Events\Lms;

use App\Models\CampaignLead;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewLeadCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public CampaignLead $lead
    ) {}

    /**
     * Get the channels the event should broadcast on.
     * Broadcasts to all users with 'access lms' permission.
     *
     * @return array<PrivateChannel>
     */
    public function broadcastOn(): array
    {
        return $this->getLmsUserChannels();
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'type' => 'new_lead',
            'title' => 'New Lead',
            'message' => "{$this->lead->full_name} from {$this->lead->source}",
            'lead_id' => $this->lead->id,
            'created_at' => now()->toISOString(),
        ];
    }

    /**
     * Get private channels for all LMS users.
     *
     * @return array<PrivateChannel>
     */
    private function getLmsUserChannels(): array
    {
        $userIds = User::permission('access lms')->pluck('id');

        return $userIds->map(function ($userId) {
            return new PrivateChannel("lms.notifications.{$userId}");
        })->all();
    }
}
