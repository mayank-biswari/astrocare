<?php

namespace Tests\Feature\Lms\Properties;

use App\Models\CampaignLead;
use App\Models\LmsNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Property-based tests for LMS Notifications.
 *
 * Uses Faker to generate random scenarios over 100 iterations,
 * then verifies notification delivery and read state correctness.
 *
 * **Validates: Real-time notification requirement**
 */
class NotificationPropertyTest extends TestCase
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
     * Property 21: Notification delivery to all LMS users
     *
     * For any event that triggers a notification (new lead, status change),
     * a persistent LmsNotification record SHALL be created for every user who
     * has the 'access lms' permission at the time of the event.
     *
     * **Validates: Real-time notification requirement**
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function property_notification_delivery_to_all_lms_users(): void
    {
        $faker = \Faker\Factory::create();

        for ($iteration = 0; $iteration < 100; $iteration++) {
            // Clean up from previous iteration - delete all users except the setUp user
            LmsNotification::query()->delete();
            CampaignLead::query()->delete();
            User::where('id', '!=', $this->user->id)->delete();

            // Create a random number of users with 'access lms' permission (2-5)
            $numLmsUsers = $faker->numberBetween(2, 5);
            $lmsUsers = [];

            // The setUp user already has permission, include them
            $lmsUsers[] = $this->user;

            for ($i = 1; $i < $numLmsUsers; $i++) {
                $lmsUser = User::factory()->create();
                $lmsUser->givePermissionTo('access lms');
                $lmsUsers[] = $lmsUser;
            }

            // Create some users WITHOUT 'access lms' permission (1-3)
            $numNonLmsUsers = $faker->numberBetween(1, 3);
            $nonLmsUsers = [];
            for ($i = 0; $i < $numNonLmsUsers; $i++) {
                $nonLmsUsers[] = User::factory()->create();
            }

            // Randomly choose to trigger via lead creation or status change
            $triggerType = $faker->randomElement(['new_lead', 'status_change']);

            if ($triggerType === 'new_lead') {
                // Create a lead via POST /lms/leads
                $this->actingAs($this->user)->post('/lms/leads', [
                    'full_name' => $faker->name(),
                    'email' => $faker->unique()->safeEmail(),
                    'phone_number' => '+91' . $faker->numerify('##########'),
                    'date_of_birth' => $faker->date('Y-m-d', '-18 years'),
                    'place_of_birth' => $faker->city(),
                    'message' => $faker->sentence(),
                    'source' => $faker->randomElement(['website', 'referral', 'campaign']),
                ]);

                $expectedType = 'new_lead';
            } else {
                // Create a lead and then change its status
                $lead = CampaignLead::create([
                    'full_name' => $faker->name(),
                    'email' => $faker->unique()->safeEmail(),
                    'phone_number' => '+91' . $faker->numerify('##########'),
                    'date_of_birth' => $faker->date('Y-m-d', '-18 years'),
                    'place_of_birth' => $faker->city(),
                    'status' => 'new',
                ]);

                $this->actingAs($this->user)->put("/lms/leads/{$lead->id}/status", [
                    'status' => 'contacted',
                ]);

                $expectedType = 'status_changed';
            }

            // Verify: LmsNotification records exist for exactly the LMS users
            $notifications = LmsNotification::where('type', $expectedType)->get();

            $this->assertCount(
                $numLmsUsers,
                $notifications,
                sprintf(
                    'Iteration %d (%s): Expected %d notifications (one per LMS user), got %d. '
                    . 'LMS users: %d, Non-LMS users: %d',
                    $iteration + 1,
                    $triggerType,
                    $numLmsUsers,
                    $notifications->count(),
                    $numLmsUsers,
                    $numNonLmsUsers
                )
            );

            // Verify each LMS user has exactly one notification
            foreach ($lmsUsers as $lmsUser) {
                $userNotifications = $notifications->where('user_id', $lmsUser->id);
                $this->assertCount(
                    1,
                    $userNotifications,
                    sprintf(
                        'Iteration %d (%s): LMS user %d should have exactly 1 notification, got %d',
                        $iteration + 1,
                        $triggerType,
                        $lmsUser->id,
                        $userNotifications->count()
                    )
                );
            }

            // Verify NO notifications exist for non-LMS users
            foreach ($nonLmsUsers as $nonLmsUser) {
                $this->assertDatabaseMissing('lms_notifications', [
                    'user_id' => $nonLmsUser->id,
                    'type' => $expectedType,
                ]);
            }

            $faker->unique(true); // Reset unique generator
        }
    }

    /**
     * Property 22: Notification read state correctness
     *
     * For any notification, isRead() SHALL return true if and only if read_at is not null.
     * Marking a notification as read SHALL set read_at to a non-null timestamp,
     * and the notification SHALL not appear in the unread count thereafter.
     *
     * **Validates: Real-time notification requirement**
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function property_notification_read_state_correctness(): void
    {
        $faker = \Faker\Factory::create();

        for ($iteration = 0; $iteration < 100; $iteration++) {
            // Clean up from previous iteration
            LmsNotification::query()->delete();

            // Create a notification with random read_at (null or a timestamp)
            $isInitiallyRead = $faker->boolean(50);
            $readAt = $isInitiallyRead ? $faker->dateTimeBetween('-30 days', 'now') : null;

            $notification = LmsNotification::create([
                'user_id' => $this->user->id,
                'type' => $faker->randomElement(['new_lead', 'status_changed', 'follow_up_overdue']),
                'title' => $faker->sentence(3),
                'message' => $faker->sentence(),
                'read_at' => $readAt,
            ]);

            // Verify isRead() returns the correct boolean based on read_at
            $this->assertEquals(
                $isInitiallyRead,
                $notification->isRead(),
                sprintf(
                    'Iteration %d: isRead() should return %s when read_at is %s',
                    $iteration + 1,
                    $isInitiallyRead ? 'true' : 'false',
                    $isInitiallyRead ? 'not null' : 'null'
                )
            );

            // Verify unread scope correctness
            $unreadCount = LmsNotification::where('user_id', $this->user->id)->unread()->count();
            if ($isInitiallyRead) {
                $this->assertEquals(
                    0,
                    $unreadCount,
                    sprintf(
                        'Iteration %d: Read notification should not appear in unread scope, but unread count is %d',
                        $iteration + 1,
                        $unreadCount
                    )
                );
            } else {
                $this->assertEquals(
                    1,
                    $unreadCount,
                    sprintf(
                        'Iteration %d: Unread notification should appear in unread scope, but unread count is %d',
                        $iteration + 1,
                        $unreadCount
                    )
                );
            }

            // If notification is unread, mark it as read via the controller endpoint
            if (!$isInitiallyRead) {
                $response = $this->actingAs($this->user)
                    ->put("/lms/notifications/{$notification->id}/read");

                $response->assertRedirect();

                // Refresh and verify read_at is now set
                $notification->refresh();

                $this->assertNotNull(
                    $notification->read_at,
                    sprintf(
                        'Iteration %d: After marking as read, read_at should not be null',
                        $iteration + 1
                    )
                );

                $this->assertTrue(
                    $notification->isRead(),
                    sprintf(
                        'Iteration %d: After marking as read, isRead() should return true',
                        $iteration + 1
                    )
                );

                // Verify it no longer appears in unread scope
                $unreadCountAfter = LmsNotification::where('user_id', $this->user->id)->unread()->count();
                $this->assertEquals(
                    0,
                    $unreadCountAfter,
                    sprintf(
                        'Iteration %d: After marking as read, notification should not appear in unread scope, but unread count is %d',
                        $iteration + 1,
                        $unreadCountAfter
                    )
                );
            }
        }
    }
}
