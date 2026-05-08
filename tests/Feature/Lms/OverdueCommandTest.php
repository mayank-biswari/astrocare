<?php

namespace Tests\Feature\Lms;

use App\Events\Lms\FollowUpOverdue;
use App\Models\CampaignLead;
use App\Models\LeadFollowUp;
use App\Models\LmsNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Feature tests for the lms:check-overdue-followups Artisan command.
 *
 * Validates: Requirements 7.2, 7.5
 */
class OverdueCommandTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected CampaignLead $lead;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::firstOrCreate(['name' => 'access lms', 'guard_name' => 'web']);

        $this->user = User::factory()->create();
        $this->user->givePermissionTo('access lms');

        $this->lead = CampaignLead::create([
            'full_name' => 'Overdue Test Lead',
            'email' => 'overdue@example.com',
            'phone_number' => '+919876543210',
            'date_of_birth' => '1990-01-15',
            'place_of_birth' => 'Mumbai',
            'source' => 'website',
            'status' => 'contacted',
        ]);
    }

    // =========================================================================
    // Command Identifies Overdue Follow-Ups (Requirement 7.2)
    // =========================================================================

    /**
     * Test command identifies overdue follow-ups (past date, not completed)
     * and creates notifications for the follow-up author.
     *
     * Validates: Requirements 7.2, 7.5
     */
    public function test_command_creates_notification_for_overdue_follow_up(): void
    {
        $followUp = LeadFollowUp::create([
            'campaign_lead_id' => $this->lead->id,
            'user_id' => $this->user->id,
            'description' => 'Call client about package.',
            'scheduled_date' => now()->subDays(2)->toDateString(),
            'completed_at' => null,
        ]);

        $this->artisan('lms:check-overdue-followups')
            ->assertExitCode(0);

        $this->assertDatabaseHas('lms_notifications', [
            'user_id' => $this->user->id,
            'type' => 'follow_up_overdue',
            'title' => 'Overdue Follow-Up',
            'lead_id' => $this->lead->id,
        ]);

        $notification = LmsNotification::where('user_id', $this->user->id)
            ->where('type', 'follow_up_overdue')
            ->first();

        $this->assertNotNull($notification);
        $this->assertStringContains('Overdue Test Lead', $notification->message);
        $this->assertEquals(['follow_up_id' => $followUp->id], $notification->data);
    }

    /**
     * Test command fires FollowUpOverdue event for overdue follow-ups.
     *
     * Validates: Requirements 7.2, 7.5
     */
    public function test_command_fires_follow_up_overdue_event(): void
    {
        Event::fake([FollowUpOverdue::class]);

        LeadFollowUp::create([
            'campaign_lead_id' => $this->lead->id,
            'user_id' => $this->user->id,
            'description' => 'Overdue task.',
            'scheduled_date' => now()->subDay()->toDateString(),
            'completed_at' => null,
        ]);

        $this->artisan('lms:check-overdue-followups')
            ->assertExitCode(0);

        Event::assertDispatched(FollowUpOverdue::class, function ($event) {
            return $event->followUp->campaign_lead_id === $this->lead->id
                && $event->followUp->user_id === $this->user->id;
        });
    }

    // =========================================================================
    // Command Does NOT Create Notifications for Completed Follow-Ups
    // =========================================================================

    /**
     * Test command does NOT create notifications for completed follow-ups
     * even if their scheduled date is in the past.
     *
     * Validates: Requirement 7.2
     */
    public function test_command_does_not_notify_for_completed_follow_ups(): void
    {
        LeadFollowUp::create([
            'campaign_lead_id' => $this->lead->id,
            'user_id' => $this->user->id,
            'description' => 'Already completed.',
            'scheduled_date' => now()->subDays(3)->toDateString(),
            'completed_at' => now()->subDay(),
        ]);

        $this->artisan('lms:check-overdue-followups')
            ->assertExitCode(0);

        $this->assertDatabaseMissing('lms_notifications', [
            'user_id' => $this->user->id,
            'type' => 'follow_up_overdue',
        ]);
    }

    // =========================================================================
    // Command Does NOT Create Notifications for Future Follow-Ups
    // =========================================================================

    /**
     * Test command does NOT create notifications for future follow-ups.
     *
     * Validates: Requirement 7.2
     */
    public function test_command_does_not_notify_for_future_follow_ups(): void
    {
        LeadFollowUp::create([
            'campaign_lead_id' => $this->lead->id,
            'user_id' => $this->user->id,
            'description' => 'Future task.',
            'scheduled_date' => now()->addDays(3)->toDateString(),
            'completed_at' => null,
        ]);

        $this->artisan('lms:check-overdue-followups')
            ->assertExitCode(0);

        $this->assertDatabaseMissing('lms_notifications', [
            'user_id' => $this->user->id,
            'type' => 'follow_up_overdue',
        ]);
    }

    // =========================================================================
    // Command Does NOT Duplicate Notifications (Requirement 7.5)
    // =========================================================================

    /**
     * Test command does NOT duplicate notifications if run twice on the same day.
     *
     * Validates: Requirement 7.5
     */
    public function test_command_does_not_duplicate_notifications_on_same_day(): void
    {
        LeadFollowUp::create([
            'campaign_lead_id' => $this->lead->id,
            'user_id' => $this->user->id,
            'description' => 'Overdue task.',
            'scheduled_date' => now()->subDays(2)->toDateString(),
            'completed_at' => null,
        ]);

        // Run command first time
        $this->artisan('lms:check-overdue-followups')
            ->assertExitCode(0);

        $this->assertEquals(1, LmsNotification::where('user_id', $this->user->id)
            ->where('type', 'follow_up_overdue')
            ->count());

        // Run command second time on the same day
        $this->artisan('lms:check-overdue-followups')
            ->assertExitCode(0);

        // Should still be only 1 notification
        $this->assertEquals(1, LmsNotification::where('user_id', $this->user->id)
            ->where('type', 'follow_up_overdue')
            ->count());
    }

    // =========================================================================
    // Notification Targets Follow-Up Author Only
    // =========================================================================

    /**
     * Test command creates notifications for the follow-up author (user_id),
     * not all LMS users.
     *
     * Validates: Requirements 7.2, 7.5
     */
    public function test_command_notifies_follow_up_author_only(): void
    {
        // Create another LMS user who is NOT the follow-up author
        $otherUser = User::factory()->create();
        $otherUser->givePermissionTo('access lms');

        LeadFollowUp::create([
            'campaign_lead_id' => $this->lead->id,
            'user_id' => $this->user->id,
            'description' => 'My overdue task.',
            'scheduled_date' => now()->subDay()->toDateString(),
            'completed_at' => null,
        ]);

        $this->artisan('lms:check-overdue-followups')
            ->assertExitCode(0);

        // The follow-up author should get the notification
        $this->assertDatabaseHas('lms_notifications', [
            'user_id' => $this->user->id,
            'type' => 'follow_up_overdue',
            'lead_id' => $this->lead->id,
        ]);

        // The other LMS user should NOT get the notification
        $this->assertDatabaseMissing('lms_notifications', [
            'user_id' => $otherUser->id,
            'type' => 'follow_up_overdue',
        ]);
    }

    // =========================================================================
    // Notification Content Correctness
    // =========================================================================

    /**
     * Test notification has correct type, title, message, and lead_id.
     *
     * Validates: Requirements 7.2, 7.5
     */
    public function test_notification_has_correct_content(): void
    {
        $followUp = LeadFollowUp::create([
            'campaign_lead_id' => $this->lead->id,
            'user_id' => $this->user->id,
            'description' => 'Send proposal.',
            'scheduled_date' => now()->subDays(1)->toDateString(),
            'completed_at' => null,
        ]);

        $this->artisan('lms:check-overdue-followups')
            ->assertExitCode(0);

        $notification = LmsNotification::where('user_id', $this->user->id)
            ->where('type', 'follow_up_overdue')
            ->first();

        $this->assertNotNull($notification);
        $this->assertEquals('follow_up_overdue', $notification->type);
        $this->assertEquals('Overdue Follow-Up', $notification->title);
        $this->assertEquals("Follow-up for Overdue Test Lead is overdue", $notification->message);
        $this->assertEquals($this->lead->id, $notification->lead_id);
        $this->assertEquals(['follow_up_id' => $followUp->id], $notification->data);
    }

    /**
     * Helper to assert a string contains a substring.
     */
    private function assertStringContains(string $needle, string $haystack): void
    {
        $this->assertTrue(
            str_contains($haystack, $needle),
            "Failed asserting that '{$haystack}' contains '{$needle}'."
        );
    }
}
