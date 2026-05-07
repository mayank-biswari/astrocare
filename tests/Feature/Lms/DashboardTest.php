<?php

namespace Tests\Feature\Lms;

use App\Models\CampaignLead;
use App\Models\LeadFollowUp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Feature tests for the LMS Dashboard.
 *
 * Validates: Requirements 2.1, 2.2, 2.3, 2.4
 */
class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::firstOrCreate(['name' => 'access lms', 'guard_name' => 'web']);

        $this->user = User::factory()->create();
        $this->user->givePermissionTo('access lms');
    }

    /**
     * Helper to create a lead with a specific status.
     */
    private function createLead(string $status = 'new', ?Carbon $createdAt = null): CampaignLead
    {
        $lead = CampaignLead::create([
            'full_name' => fake()->name(),
            'date_of_birth' => '1990-01-15',
            'place_of_birth' => 'Test City',
            'phone_number' => '+919876543210',
            'email' => fake()->unique()->safeEmail(),
            'source' => 'website',
            'status' => $status,
        ]);

        if ($createdAt) {
            // Use query builder to bypass $fillable guard for timestamps
            CampaignLead::where('id', $lead->id)->update(['created_at' => $createdAt]);
            $lead->refresh();
        }

        return $lead;
    }

    /**
     * Test that the dashboard displays correct status counts.
     *
     * Validates: Requirement 2.1
     */
    public function test_dashboard_displays_correct_status_counts(): void
    {
        // Create leads with various statuses
        $this->createLead('new');
        $this->createLead('new');
        $this->createLead('contacted');
        $this->createLead('qualified');
        $this->createLead('qualified');
        $this->createLead('qualified');
        $this->createLead('converted');
        $this->createLead('lost');
        $this->createLead('lost');

        $response = $this->actingAs($this->user)->get('/lms');

        $response->assertStatus(200);

        $statusCounts = $response->viewData('statusCounts');

        $this->assertEquals(2, $statusCounts['new'] ?? 0);
        $this->assertEquals(1, $statusCounts['contacted'] ?? 0);
        $this->assertEquals(3, $statusCounts['qualified'] ?? 0);
        $this->assertEquals(1, $statusCounts['converted'] ?? 0);
        $this->assertEquals(2, $statusCounts['lost'] ?? 0);
    }

    /**
     * Test that the "last 7 days" count is accurate.
     *
     * Validates: Requirement 2.2
     */
    public function test_dashboard_displays_correct_recent_leads_count(): void
    {
        // Create 3 leads within the last 7 days
        $this->createLead('new', now()->subDays(1));
        $this->createLead('new', now()->subDays(3));
        $this->createLead('contacted', now()->subDays(6));

        // Create 2 leads older than 7 days
        $this->createLead('new', now()->subDays(8));
        $this->createLead('qualified', now()->subDays(15));

        $response = $this->actingAs($this->user)->get('/lms');

        $response->assertStatus(200);

        $recentLeadsCount = $response->viewData('recentLeadsCount');

        $this->assertEquals(3, $recentLeadsCount);
    }

    /**
     * Test that the recent leads list shows the 10 most recent leads.
     *
     * Validates: Requirement 2.3
     */
    public function test_dashboard_displays_10_most_recent_leads(): void
    {
        // Create 12 leads with staggered creation dates
        $leads = [];
        for ($i = 1; $i <= 12; $i++) {
            $leads[] = $this->createLead('new', now()->subMinutes(13 - $i));
        }

        $response = $this->actingAs($this->user)->get('/lms');

        $response->assertStatus(200);

        $recentLeads = $response->viewData('recentLeads');

        // Should only show 10 leads
        $this->assertCount(10, $recentLeads);

        // The most recent lead should be first (lead #12, created most recently)
        $this->assertEquals($leads[11]->id, $recentLeads->first()->id);

        // The oldest two leads should NOT be in the list
        $recentLeadIds = $recentLeads->pluck('id')->toArray();
        $this->assertNotContains($leads[0]->id, $recentLeadIds);
        $this->assertNotContains($leads[1]->id, $recentLeadIds);
    }

    /**
     * Test that when fewer than 10 leads exist, all are shown.
     *
     * Validates: Requirement 2.3
     */
    public function test_dashboard_shows_all_leads_when_fewer_than_10(): void
    {
        $this->createLead('new');
        $this->createLead('contacted');
        $this->createLead('qualified');

        $response = $this->actingAs($this->user)->get('/lms');

        $response->assertStatus(200);

        $recentLeads = $response->viewData('recentLeads');

        $this->assertCount(3, $recentLeads);
    }

    /**
     * Test that upcoming follow-ups within 7 days are displayed.
     *
     * Validates: Requirement 2.4
     */
    public function test_dashboard_displays_upcoming_follow_ups(): void
    {
        $lead = $this->createLead('contacted');

        // Create upcoming follow-ups (within next 7 days, not completed)
        $upcoming1 = LeadFollowUp::create([
            'campaign_lead_id' => $lead->id,
            'user_id' => $this->user->id,
            'description' => 'Call client tomorrow',
            'scheduled_date' => now()->addDays(1)->toDateString(),
            'completed_at' => null,
        ]);

        $upcoming2 = LeadFollowUp::create([
            'campaign_lead_id' => $lead->id,
            'user_id' => $this->user->id,
            'description' => 'Send proposal in 5 days',
            'scheduled_date' => now()->addDays(5)->toDateString(),
            'completed_at' => null,
        ]);

        // Create a follow-up beyond 7 days (should NOT appear)
        LeadFollowUp::create([
            'campaign_lead_id' => $lead->id,
            'user_id' => $this->user->id,
            'description' => 'Follow up in 10 days',
            'scheduled_date' => now()->addDays(10)->toDateString(),
            'completed_at' => null,
        ]);

        // Create a completed follow-up within 7 days (should NOT appear)
        LeadFollowUp::create([
            'campaign_lead_id' => $lead->id,
            'user_id' => $this->user->id,
            'description' => 'Already done',
            'scheduled_date' => now()->addDays(2)->toDateString(),
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->get('/lms');

        $response->assertStatus(200);

        $upcomingFollowUps = $response->viewData('upcomingFollowUps');

        $this->assertCount(2, $upcomingFollowUps);

        $upcomingIds = $upcomingFollowUps->pluck('id')->toArray();
        $this->assertContains($upcoming1->id, $upcomingIds);
        $this->assertContains($upcoming2->id, $upcomingIds);
    }

    /**
     * Test that overdue follow-ups are displayed.
     *
     * Validates: Requirement 2.4, 7.5
     */
    public function test_dashboard_displays_overdue_follow_ups(): void
    {
        $lead = $this->createLead('contacted');

        // Create overdue follow-ups (past date, not completed)
        $overdue1 = LeadFollowUp::create([
            'campaign_lead_id' => $lead->id,
            'user_id' => $this->user->id,
            'description' => 'Missed call yesterday',
            'scheduled_date' => now()->subDays(1)->toDateString(),
            'completed_at' => null,
        ]);

        $overdue2 = LeadFollowUp::create([
            'campaign_lead_id' => $lead->id,
            'user_id' => $this->user->id,
            'description' => 'Missed meeting last week',
            'scheduled_date' => now()->subDays(5)->toDateString(),
            'completed_at' => null,
        ]);

        // Create a past follow-up that IS completed (should NOT appear as overdue)
        LeadFollowUp::create([
            'campaign_lead_id' => $lead->id,
            'user_id' => $this->user->id,
            'description' => 'Completed past follow-up',
            'scheduled_date' => now()->subDays(3)->toDateString(),
            'completed_at' => now()->subDays(2),
        ]);

        // Create a future follow-up (should NOT appear as overdue)
        LeadFollowUp::create([
            'campaign_lead_id' => $lead->id,
            'user_id' => $this->user->id,
            'description' => 'Future follow-up',
            'scheduled_date' => now()->addDays(3)->toDateString(),
            'completed_at' => null,
        ]);

        $response = $this->actingAs($this->user)->get('/lms');

        $response->assertStatus(200);

        $overdueFollowUps = $response->viewData('overdueFollowUps');

        $this->assertCount(2, $overdueFollowUps);

        $overdueIds = $overdueFollowUps->pluck('id')->toArray();
        $this->assertContains($overdue1->id, $overdueIds);
        $this->assertContains($overdue2->id, $overdueIds);
    }

    /**
     * Test that the dashboard handles empty state gracefully.
     *
     * Validates: Requirements 2.1, 2.2, 2.3, 2.4
     */
    public function test_dashboard_handles_empty_state(): void
    {
        $response = $this->actingAs($this->user)->get('/lms');

        $response->assertStatus(200);

        $statusCounts = $response->viewData('statusCounts');
        $recentLeadsCount = $response->viewData('recentLeadsCount');
        $recentLeads = $response->viewData('recentLeads');
        $upcomingFollowUps = $response->viewData('upcomingFollowUps');
        $overdueFollowUps = $response->viewData('overdueFollowUps');

        $this->assertEmpty($statusCounts);
        $this->assertEquals(0, $recentLeadsCount);
        $this->assertCount(0, $recentLeads);
        $this->assertCount(0, $upcomingFollowUps);
        $this->assertCount(0, $overdueFollowUps);
    }
}
