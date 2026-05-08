<?php

namespace Tests\Feature\Lms\Properties;

use App\Models\CampaignLead;
use App\Models\LeadFollowUp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class DebugUpcomingTest extends TestCase
{
    use RefreshDatabase;

    public function test_debug_upcoming_scope(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 5, 7, 12, 0, 0));

        Permission::firstOrCreate(['name' => 'access lms', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->givePermissionTo('access lms');

        $lead = CampaignLead::create([
            'full_name' => 'Test Lead',
            'date_of_birth' => '1990-01-01',
            'place_of_birth' => 'City',
            'phone_number' => '+919876543210',
            'email' => 'test@example.com',
            'source' => 'website',
            'status' => 'contacted',
        ]);

        // Create follow-ups at various dates
        $dates = [
            '2026-05-06' => 'yesterday - should NOT be upcoming',
            '2026-05-07' => 'today - should be upcoming',
            '2026-05-10' => 'in 3 days - should be upcoming',
            '2026-05-14' => 'in 7 days - should be upcoming',
            '2026-05-15' => 'in 8 days - should NOT be upcoming',
        ];

        foreach ($dates as $date => $desc) {
            LeadFollowUp::create([
                'campaign_lead_id' => $lead->id,
                'user_id' => $user->id,
                'description' => $desc,
                'scheduled_date' => $date,
                'completed_at' => null,
            ]);
        }

        // Check scope directly
        $upcoming = LeadFollowUp::upcoming(7)->get();
        $upcomingDates = $upcoming->pluck('scheduled_date')->map(fn($d) => $d->toDateString())->toArray();

        // The scope should include today, +3, +7 but NOT yesterday or +8
        $this->assertContains('2026-05-07', $upcomingDates, 'Today should be upcoming');
        $this->assertContains('2026-05-10', $upcomingDates, '+3 days should be upcoming');
        $this->assertContains('2026-05-14', $upcomingDates, '+7 days should be upcoming');
        $this->assertNotContains('2026-05-06', $upcomingDates, 'Yesterday should NOT be upcoming');
        $this->assertNotContains('2026-05-15', $upcomingDates, '+8 days should NOT be upcoming');
        $this->assertCount(3, $upcoming, 'Expected 3 upcoming follow-ups. Got dates: ' . implode(', ', $upcomingDates));

        Carbon::setTestNow();
    }

    public function test_debug_completed_at_null_check(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 5, 7, 12, 0, 0));

        Permission::firstOrCreate(['name' => 'access lms', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->givePermissionTo('access lms');

        $lead = CampaignLead::create([
            'full_name' => 'Test Lead',
            'date_of_birth' => '1990-01-01',
            'place_of_birth' => 'City',
            'phone_number' => '+919876543210',
            'email' => 'test2@example.com',
            'source' => 'website',
            'status' => 'contacted',
        ]);

        // Create 3 follow-ups for tomorrow: 1 completed, 2 not completed
        $fu1 = LeadFollowUp::create([
            'campaign_lead_id' => $lead->id,
            'user_id' => $user->id,
            'description' => 'Not completed 1',
            'scheduled_date' => '2026-05-08',
            'completed_at' => null,
        ]);

        $fu2 = LeadFollowUp::create([
            'campaign_lead_id' => $lead->id,
            'user_id' => $user->id,
            'description' => 'Completed',
            'scheduled_date' => '2026-05-08',
            'completed_at' => now()->subHour(),
        ]);

        $fu3 = LeadFollowUp::create([
            'campaign_lead_id' => $lead->id,
            'user_id' => $user->id,
            'description' => 'Not completed 2',
            'scheduled_date' => '2026-05-08',
            'completed_at' => null,
        ]);

        // Refresh from DB to check stored values
        $fu1->refresh();
        $fu2->refresh();
        $fu3->refresh();

        $this->assertNull($fu1->completed_at, 'fu1 completed_at should be null');
        $this->assertNotNull($fu2->completed_at, 'fu2 completed_at should NOT be null');
        $this->assertNull($fu3->completed_at, 'fu3 completed_at should be null');

        $upcoming = LeadFollowUp::upcoming(7)->get();
        $this->assertCount(2, $upcoming, 'Expected 2 upcoming (not completed) follow-ups, got ' . $upcoming->count());

        $upcomingIds = $upcoming->pluck('id')->toArray();
        $this->assertContains($fu1->id, $upcomingIds);
        $this->assertNotContains($fu2->id, $upcomingIds);
        $this->assertContains($fu3->id, $upcomingIds);

        Carbon::setTestNow();
    }
}
