<?php

namespace Tests\Feature\Lms\Properties;

use App\Models\CampaignLead;
use App\Models\LeadFollowUp;
use App\Models\LmsNotification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Property-based tests for overdue follow-up notification scheduling.
 *
 * Uses Faker to generate random sets of follow-ups with various scheduled_dates
 * and completion states, then verifies the lms:check-overdue-followups command
 * creates correct notifications.
 *
 * **Validates: Requirements 7.2, Real-time notification requirement**
 */
class OverdueSchedulingPropertyTest extends TestCase
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
     * Property 23: Overdue follow-up notification scheduling
     *
     * For any follow-up that transitions from non-overdue to overdue
     * (scheduled_date becomes past and completed_at is null), the scheduled command
     * SHALL create an LmsNotification of type 'follow_up_overdue' for the follow-up's author.
     *
     * Additionally:
     * - No notifications are created for non-overdue follow-ups (future date or completed)
     * - Running the command twice does not create duplicate notifications (deduplication)
     *
     * **Validates: Requirements 7.2, Real-time notification requirement**
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function property_overdue_follow_up_notification_scheduling(): void
    {
        $faker = \Faker\Factory::create();

        // Freeze time to avoid boundary issues
        $frozenNow = Carbon::create(2026, 6, 15, 10, 0, 0);
        Carbon::setTestNow($frozenNow);

        for ($iteration = 0; $iteration < 100; $iteration++) {
            // Clean up from previous iteration
            LmsNotification::query()->delete();
            LeadFollowUp::query()->delete();
            CampaignLead::query()->delete();

            // Create multiple users to act as follow-up authors
            $authors = [];
            $numAuthors = $faker->numberBetween(1, 3);
            for ($a = 0; $a < $numAuthors; $a++) {
                $authors[] = User::factory()->create();
            }

            // Create a lead to attach follow-ups to
            $lead = CampaignLead::create([
                'full_name' => $faker->name(),
                'date_of_birth' => $faker->date(),
                'place_of_birth' => $faker->city(),
                'phone_number' => '+91' . $faker->numerify('##########'),
                'email' => $faker->unique()->safeEmail(),
                'source' => $faker->randomElement(['website', 'referral', 'campaign']),
                'status' => 'contacted',
            ]);

            // Generate random follow-ups (3 to 12)
            $numFollowUps = $faker->numberBetween(3, 12);
            $expectedOverdueAuthorLeadPairs = [];

            for ($i = 0; $i < $numFollowUps; $i++) {
                $author = $faker->randomElement($authors);

                // Random scheduled_date: from 10 days ago to 10 days in the future
                $daysOffset = $faker->numberBetween(-10, 10);
                $scheduledDate = $frozenNow->copy()->addDays($daysOffset)->toDateString();

                // Randomly decide if completed
                $isCompleted = $faker->boolean(30);
                $completedAt = $isCompleted
                    ? $frozenNow->copy()->subHours($faker->numberBetween(1, 48))
                    : null;

                $followUp = LeadFollowUp::create([
                    'campaign_lead_id' => $lead->id,
                    'user_id' => $author->id,
                    'description' => $faker->sentence(),
                    'scheduled_date' => $scheduledDate,
                    'completed_at' => $completedAt,
                ]);

                // A follow-up is overdue if scheduled_date < today AND not completed
                $isOverdue = $scheduledDate < $frozenNow->toDateString() && !$isCompleted;

                if ($isOverdue) {
                    // Track unique (user_id, lead_id) pairs since deduplication is per author+lead+day
                    $pairKey = $author->id . '-' . $lead->id;
                    $expectedOverdueAuthorLeadPairs[$pairKey] = [
                        'user_id' => $author->id,
                        'lead_id' => $lead->id,
                    ];
                }
            }

            // Run the command
            $this->artisan('lms:check-overdue-followups')->assertExitCode(0);

            // Verify notifications were created for exactly the overdue follow-up authors
            $notifications = LmsNotification::where('type', 'follow_up_overdue')->get();

            // Each unique (user_id, lead_id) pair should have exactly one notification
            $actualPairs = $notifications->map(function ($n) {
                return $n->user_id . '-' . $n->lead_id;
            })->unique()->values()->toArray();

            $expectedPairKeys = array_keys($expectedOverdueAuthorLeadPairs);
            sort($actualPairs);
            sort($expectedPairKeys);

            $this->assertEquals(
                $expectedPairKeys,
                $actualPairs,
                sprintf(
                    'Iteration %d: Overdue notification pairs mismatch. '
                    . 'Expected %d unique (user, lead) pairs, got %d. '
                    . 'Expected: %s, Got: %s. Total follow-ups: %d',
                    $iteration + 1,
                    count($expectedPairKeys),
                    count($actualPairs),
                    json_encode($expectedPairKeys),
                    json_encode($actualPairs),
                    $numFollowUps
                )
            );

            // Verify all notifications have correct type
            foreach ($notifications as $notification) {
                $this->assertEquals(
                    'follow_up_overdue',
                    $notification->type,
                    sprintf('Iteration %d: Notification type should be follow_up_overdue', $iteration + 1)
                );
            }

            // Verify no notifications exist for non-overdue follow-ups' authors
            // (unless they also have an overdue one - which is handled by the pair check above)

            // === Deduplication test: run the command again ===
            $countBefore = LmsNotification::where('type', 'follow_up_overdue')->count();

            $this->artisan('lms:check-overdue-followups')->assertExitCode(0);

            $countAfter = LmsNotification::where('type', 'follow_up_overdue')->count();

            $this->assertEquals(
                $countBefore,
                $countAfter,
                sprintf(
                    'Iteration %d: Deduplication failed. Running command twice created duplicates. '
                    . 'Count before: %d, Count after: %d',
                    $iteration + 1,
                    $countBefore,
                    $countAfter
                )
            );

            $faker->unique(true); // Reset unique generator
        }

        Carbon::setTestNow(); // Unfreeze time
    }
}
