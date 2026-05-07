<?php

namespace Tests\Feature\Lms\Properties;

use App\Models\CampaignLead;
use App\Models\LeadFollowUp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Property 11: Overdue follow-up classification
 *
 * For any follow-up, isOverdue() returns true if and only if
 * scheduled_date < today AND completed_at is null.
 *
 * **Validates: Requirements 7.2, 4.4**
 */
class OverdueClassificationPropertyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Property 11: Overdue follow-up classification
     *
     * For any follow-up generated with random scheduled_date and completion state,
     * isOverdue() SHALL return true iff scheduled_date < today AND completed_at is null.
     *
     * Uses Faker to generate 100 random follow-ups with various dates and completion states.
     *
     * **Validates: Requirements 7.2, 4.4**
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function property_overdue_classification_holds_for_all_generated_follow_ups(): void
    {
        $user = User::factory()->create();

        $lead = CampaignLead::create([
            'full_name' => 'Property Test Lead',
            'date_of_birth' => '1990-01-01',
            'place_of_birth' => 'Test City',
            'phone_number' => '+919876543210',
            'email' => 'property-test@example.com',
            'source' => 'website',
            'status' => 'new',
        ]);

        $faker = \Faker\Factory::create();
        $today = Carbon::today();

        for ($i = 0; $i < 100; $i++) {
            // Generate a random scheduled_date: past, today, or future
            $scheduledDate = $faker->dateTimeBetween('-60 days', '+60 days');
            $scheduledDateCarbon = Carbon::parse($scheduledDate)->startOfDay();

            // Randomly decide if the follow-up is completed or not
            $isCompleted = $faker->boolean(50);
            $completedAt = $isCompleted
                ? Carbon::parse($faker->dateTimeBetween($scheduledDate, '+90 days'))
                : null;

            $followUp = LeadFollowUp::create([
                'campaign_lead_id' => $lead->id,
                'user_id' => $user->id,
                'description' => $faker->sentence(),
                'scheduled_date' => $scheduledDateCarbon->toDateString(),
                'completed_at' => $completedAt,
            ]);

            // Refresh to ensure casts are applied from DB
            $followUp->refresh();

            // The property: isOverdue() == (scheduled_date < today AND completed_at is null)
            $expectedOverdue = $scheduledDateCarbon->lt($today) && $followUp->completed_at === null;
            $actualOverdue = $followUp->isOverdue();

            $this->assertSame(
                $expectedOverdue,
                $actualOverdue,
                sprintf(
                    'Iteration %d: isOverdue() returned %s but expected %s '
                    . '(scheduled_date=%s, today=%s, completed_at=%s)',
                    $i + 1,
                    $actualOverdue ? 'true' : 'false',
                    $expectedOverdue ? 'true' : 'false',
                    $scheduledDateCarbon->toDateString(),
                    $today->toDateString(),
                    $completedAt ? $completedAt->toDateTimeString() : 'null'
                )
            );
        }
    }
}
