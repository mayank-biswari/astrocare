<?php

namespace Tests\Unit\Lms;

use App\Models\CampaignLead;
use App\Models\LeadFollowUp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadFollowUpTest extends TestCase
{
    use RefreshDatabase;

    private function createLeadAndUser(): array
    {
        $user = User::factory()->create();

        $lead = CampaignLead::create([
            'full_name' => 'Test Lead',
            'date_of_birth' => '1990-01-15',
            'place_of_birth' => 'Mumbai',
            'phone_number' => '+919876543210',
            'email' => 'test@example.com',
            'source' => 'website',
            'status' => 'new',
        ]);

        return [$lead, $user];
    }

    public function test_is_overdue_returns_true_when_scheduled_date_is_past_and_not_completed(): void
    {
        [$lead, $user] = $this->createLeadAndUser();

        $followUp = LeadFollowUp::create([
            'campaign_lead_id' => $lead->id,
            'user_id' => $user->id,
            'description' => 'Follow up call',
            'scheduled_date' => Carbon::yesterday(),
            'completed_at' => null,
        ]);

        $this->assertTrue($followUp->isOverdue());
    }

    public function test_is_overdue_returns_false_when_scheduled_date_is_past_but_completed(): void
    {
        [$lead, $user] = $this->createLeadAndUser();

        $followUp = LeadFollowUp::create([
            'campaign_lead_id' => $lead->id,
            'user_id' => $user->id,
            'description' => 'Follow up call',
            'scheduled_date' => Carbon::yesterday(),
            'completed_at' => Carbon::now(),
        ]);

        $this->assertFalse($followUp->isOverdue());
    }

    public function test_is_overdue_returns_false_when_scheduled_date_is_today(): void
    {
        [$lead, $user] = $this->createLeadAndUser();

        $followUp = LeadFollowUp::create([
            'campaign_lead_id' => $lead->id,
            'user_id' => $user->id,
            'description' => 'Follow up call',
            'scheduled_date' => Carbon::today(),
            'completed_at' => null,
        ]);

        $this->assertFalse($followUp->isOverdue());
    }

    public function test_is_overdue_returns_false_when_scheduled_date_is_future(): void
    {
        [$lead, $user] = $this->createLeadAndUser();

        $followUp = LeadFollowUp::create([
            'campaign_lead_id' => $lead->id,
            'user_id' => $user->id,
            'description' => 'Follow up call',
            'scheduled_date' => Carbon::tomorrow(),
            'completed_at' => null,
        ]);

        $this->assertFalse($followUp->isOverdue());
    }
}
