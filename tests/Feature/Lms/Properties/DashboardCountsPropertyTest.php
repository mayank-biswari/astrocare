<?php

namespace Tests\Feature\Lms\Properties;

use App\Models\CampaignLead;
use App\Models\LeadFollowUp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Property-based tests for LMS Dashboard counts.
 *
 * Uses Faker to generate random sets of leads and follow-ups,
 * then verifies dashboard data matches expectations.
 *
 * **Validates: Requirements 2.1, 2.2, 2.3, 2.4, 7.5**
 */
class DashboardCountsPropertyTest extends TestCase
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
     * Property 1: Dashboard status counts match database
     *
     * For any set of leads in the database with various statuses,
     * the dashboard status count for each status value SHALL equal
     * the actual count of leads with that status in the campaign_leads table.
     *
     * **Validates: Requirements 2.1**
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function property_dashboard_status_counts_match_database(): void
    {
        $faker = \Faker\Factory::create();
        $statuses = ['new', 'contacted', 'qualified', 'converted', 'lost'];

        for ($iteration = 0; $iteration < 100; $iteration++) {
            // Clean up leads from previous iteration
            CampaignLead::query()->delete();

            // Generate a random number of leads (1 to 20) with random statuses
            $numLeads = $faker->numberBetween(1, 20);
            $expectedCounts = array_fill_keys($statuses, 0);

            for ($i = 0; $i < $numLeads; $i++) {
                $status = $faker->randomElement($statuses);
                $expectedCounts[$status]++;

                CampaignLead::create([
                    'full_name' => $faker->name(),
                    'date_of_birth' => $faker->date(),
                    'place_of_birth' => $faker->city(),
                    'phone_number' => '+91' . $faker->numerify('##########'),
                    'email' => $faker->unique()->safeEmail(),
                    'source' => $faker->randomElement(['website', 'referral', 'campaign']),
                    'status' => $status,
                ]);
            }

            $response = $this->actingAs($this->user)->get('/lms');
            $response->assertStatus(200);

            $statusCounts = $response->viewData('statusCounts');

            // Verify each status count matches the expected count
            foreach ($statuses as $status) {
                $actual = $statusCounts[$status] ?? 0;
                $expected = $expectedCounts[$status];

                $this->assertEquals(
                    $expected,
                    $actual,
                    sprintf(
                        'Iteration %d: Status "%s" count mismatch. Expected %d, got %d. '
                        . 'Total leads: %d, Distribution: %s',
                        $iteration + 1,
                        $status,
                        $expected,
                        $actual,
                        $numLeads,
                        json_encode($expectedCounts)
                    )
                );
            }

            $faker->unique(true); // Reset unique generator
        }
    }

    /**
     * Property 2: Recent leads count accuracy
     *
     * For any set of leads with various created_at timestamps,
     * the dashboard "last 7 days" count SHALL equal the number of leads
     * where created_at >= now - 7 days.
     *
     * **Validates: Requirements 2.2**
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function property_recent_leads_count_accuracy(): void
    {
        $faker = \Faker\Factory::create();

        for ($iteration = 0; $iteration < 100; $iteration++) {
            // Clean up leads from previous iteration
            CampaignLead::query()->delete();

            // Generate a random number of leads (1 to 15) with random timestamps
            $numLeads = $faker->numberBetween(1, 15);
            $expectedRecentCount = 0;
            $sevenDaysAgo = now()->subDays(7);

            for ($i = 0; $i < $numLeads; $i++) {
                // Random timestamp: some within 7 days, some older (up to 30 days ago)
                $daysAgo = $faker->numberBetween(0, 30);
                $createdAt = now()->subDays($daysAgo)->subHours($faker->numberBetween(0, 23));

                if ($createdAt->gte($sevenDaysAgo)) {
                    $expectedRecentCount++;
                }

                $lead = CampaignLead::create([
                    'full_name' => $faker->name(),
                    'date_of_birth' => $faker->date(),
                    'place_of_birth' => $faker->city(),
                    'phone_number' => '+91' . $faker->numerify('##########'),
                    'email' => $faker->unique()->safeEmail(),
                    'source' => 'website',
                    'status' => 'new',
                ]);

                // Update created_at directly via query builder
                CampaignLead::where('id', $lead->id)->update(['created_at' => $createdAt]);
            }

            $response = $this->actingAs($this->user)->get('/lms');
            $response->assertStatus(200);

            $recentLeadsCount = $response->viewData('recentLeadsCount');

            $this->assertEquals(
                $expectedRecentCount,
                $recentLeadsCount,
                sprintf(
                    'Iteration %d: Recent leads count mismatch. Expected %d, got %d. '
                    . 'Total leads: %d',
                    $iteration + 1,
                    $expectedRecentCount,
                    $recentLeadsCount,
                    $numLeads
                )
            );

            $faker->unique(true); // Reset unique generator
        }
    }

    /**
     * Property 3: Recent leads list correctness
     *
     * For any set of N leads in the database, the dashboard recent leads list
     * SHALL contain exactly min(N, 10) leads, and those leads SHALL be the ones
     * with the most recent created_at values, ordered descending.
     *
     * **Validates: Requirements 2.3**
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function property_recent_leads_list_correctness(): void
    {
        $faker = \Faker\Factory::create();

        for ($iteration = 0; $iteration < 100; $iteration++) {
            // Clean up leads from previous iteration
            CampaignLead::query()->delete();

            // Generate N random leads (N between 1 and 25)
            $numLeads = $faker->numberBetween(1, 25);
            $leadTimestamps = [];

            for ($i = 0; $i < $numLeads; $i++) {
                // Spread leads across different timestamps to ensure distinct ordering
                $createdAt = now()->subMinutes($faker->numberBetween(1, 10000));

                $lead = CampaignLead::create([
                    'full_name' => $faker->name(),
                    'date_of_birth' => $faker->date(),
                    'place_of_birth' => $faker->city(),
                    'phone_number' => '+91' . $faker->numerify('##########'),
                    'email' => $faker->unique()->safeEmail(),
                    'source' => 'website',
                    'status' => $faker->randomElement(['new', 'contacted', 'qualified']),
                ]);

                // Update created_at directly
                CampaignLead::where('id', $lead->id)->update(['created_at' => $createdAt]);
                $leadTimestamps[$lead->id] = $createdAt;
            }

            $response = $this->actingAs($this->user)->get('/lms');
            $response->assertStatus(200);

            $recentLeads = $response->viewData('recentLeads');

            // Verify count is min(N, 10)
            $expectedCount = min($numLeads, 10);
            $this->assertCount(
                $expectedCount,
                $recentLeads,
                sprintf(
                    'Iteration %d: Expected %d recent leads (min(%d, 10)), got %d',
                    $iteration + 1,
                    $expectedCount,
                    $numLeads,
                    $recentLeads->count()
                )
            );

            // Verify they are the most recent by created_at (descending)
            // Sort all lead timestamps descending and take top 10
            arsort($leadTimestamps);
            $expectedTopIds = array_slice(array_keys($leadTimestamps), 0, 10);

            $actualIds = $recentLeads->pluck('id')->toArray();

            // The set of IDs should match (same leads, regardless of exact order within ties)
            $this->assertEqualsCanonicalizing(
                $expectedTopIds,
                $actualIds,
                sprintf(
                    'Iteration %d: Recent leads list does not contain the %d most recent leads. '
                    . 'Expected IDs: %s, Got IDs: %s',
                    $iteration + 1,
                    $expectedCount,
                    json_encode($expectedTopIds),
                    json_encode($actualIds)
                )
            );

            // Verify ordering is descending by created_at
            $previousCreatedAt = null;
            foreach ($recentLeads as $lead) {
                if ($previousCreatedAt !== null) {
                    $this->assertTrue(
                        $lead->created_at <= $previousCreatedAt,
                        sprintf(
                            'Iteration %d: Leads not in descending created_at order. '
                            . '%s should be <= %s',
                            $iteration + 1,
                            $lead->created_at->toDateTimeString(),
                            $previousCreatedAt->toDateTimeString()
                        )
                    );
                }
                $previousCreatedAt = $lead->created_at;
            }

            $faker->unique(true); // Reset unique generator
        }
    }

    /**
     * Property 4: Upcoming follow-ups filtering
     *
     * For any set of follow-ups with various scheduled dates and completion states,
     * the dashboard upcoming follow-ups list SHALL contain exactly those follow-ups
     * where scheduled_date is between today and today+7 days (inclusive) AND completed_at is null.
     *
     * **Validates: Requirements 2.4, 7.5**
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function property_upcoming_follow_ups_filtering(): void
    {
        $faker = \Faker\Factory::create();

        // Freeze time for the entire test to avoid boundary issues
        $frozenNow = Carbon::create(2026, 5, 7, 12, 0, 0);
        Carbon::setTestNow($frozenNow);

        $todayStr = $frozenNow->toDateString(); // '2026-05-07'
        $sevenDaysStr = $frozenNow->copy()->addDays(7)->toDateString(); // '2026-05-14'

        for ($iteration = 0; $iteration < 100; $iteration++) {
            // Clean up from previous iteration
            LeadFollowUp::query()->delete();
            CampaignLead::query()->delete();

            // Create a lead to attach follow-ups to
            $lead = CampaignLead::create([
                'full_name' => $faker->name(),
                'date_of_birth' => $faker->date(),
                'place_of_birth' => $faker->city(),
                'phone_number' => '+91' . $faker->numerify('##########'),
                'email' => $faker->unique()->safeEmail(),
                'source' => 'website',
                'status' => 'contacted',
            ]);

            // Generate random follow-ups (5 to 15)
            $numFollowUps = $faker->numberBetween(5, 15);
            $expectedUpcomingIds = [];

            for ($i = 0; $i < $numFollowUps; $i++) {
                // Random scheduled_date: from 14 days ago to 14 days in the future
                $daysOffset = $faker->numberBetween(-14, 14);
                $scheduledDateStr = $frozenNow->copy()->startOfDay()->addDays($daysOffset)->toDateString();

                // Randomly decide if completed
                $isCompleted = $faker->boolean(40);
                $completedAt = $isCompleted ? $frozenNow->copy()->subHours($faker->numberBetween(1, 48)) : null;

                $followUp = LeadFollowUp::create([
                    'campaign_lead_id' => $lead->id,
                    'user_id' => $this->user->id,
                    'description' => $faker->sentence(),
                    'scheduled_date' => $scheduledDateStr,
                    'completed_at' => $completedAt,
                ]);

                // Determine if this follow-up should appear in upcoming list
                // Scope logic: scheduled_date >= now()->toDateString() AND scheduled_date <= now()->addDays(7)->toDateString() AND completed_at IS NULL
                $isUpcoming = $scheduledDateStr >= $todayStr
                    && $scheduledDateStr <= $sevenDaysStr
                    && !$isCompleted;

                if ($isUpcoming) {
                    $expectedUpcomingIds[] = $followUp->id;
                }
            }

            $response = $this->actingAs($this->user)->get('/lms');
            $response->assertStatus(200);

            $upcomingFollowUps = $response->viewData('upcomingFollowUps');
            $actualIds = $upcomingFollowUps->pluck('id')->toArray();

            // Verify the set of upcoming follow-ups matches exactly
            $this->assertEqualsCanonicalizing(
                $expectedUpcomingIds,
                $actualIds,
                sprintf(
                    'Iteration %d: Upcoming follow-ups mismatch. '
                    . 'Expected %d follow-ups (IDs: %s), got %d (IDs: %s). '
                    . 'Total follow-ups: %d',
                    $iteration + 1,
                    count($expectedUpcomingIds),
                    json_encode($expectedUpcomingIds),
                    count($actualIds),
                    json_encode($actualIds),
                    $numFollowUps
                )
            );

            $faker->unique(true); // Reset unique generator
        }

        Carbon::setTestNow(); // Unfreeze time
    }
}
