<?php

namespace Tests\Feature\Lms;

use App\Events\Lms\LeadStatusChanged;
use App\Events\Lms\NewLeadCreated;
use App\Models\CampaignLead;
use App\Models\LmsNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Feature tests for the LMS Notifications.
 *
 * Validates: Real-time notification requirement
 */
class NotificationTest extends TestCase
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
     * Valid lead data for creation.
     */
    private function validLeadData(array $overrides = []): array
    {
        return array_merge([
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
            'phone_number' => '+919876543210',
            'date_of_birth' => '1990-05-15',
            'place_of_birth' => 'Mumbai',
            'message' => 'Interested in consultation',
            'source' => 'website',
        ], $overrides);
    }

    // =========================================================================
    // Event Dispatch Tests (using Event::fake())
    // =========================================================================

    /**
     * Test NewLeadCreated event is dispatched when a lead is created.
     *
     * Validates: Real-time notification requirement
     */
    public function test_new_lead_created_event_is_dispatched_on_lead_creation(): void
    {
        Event::fake([NewLeadCreated::class]);

        $this->actingAs($this->user)
            ->post('/lms/leads', $this->validLeadData());

        Event::assertDispatched(NewLeadCreated::class, function ($event) {
            return $event->lead->full_name === 'John Doe'
                && $event->lead->email === 'john@example.com';
        });
    }

    /**
     * Test LeadStatusChanged event is dispatched when a lead status is updated.
     *
     * Validates: Real-time notification requirement
     */
    public function test_lead_status_changed_event_is_dispatched_on_status_change(): void
    {
        Event::fake([LeadStatusChanged::class]);

        $lead = CampaignLead::create([
            'full_name' => 'Status Test Lead',
            'email' => 'status@example.com',
            'phone_number' => '+919876543210',
            'date_of_birth' => '1990-01-01',
            'place_of_birth' => 'Mumbai',
            'status' => 'new',
        ]);

        $this->actingAs($this->user)
            ->put("/lms/leads/{$lead->id}/status", ['status' => 'contacted']);

        Event::assertDispatched(LeadStatusChanged::class, function ($event) use ($lead) {
            return $event->lead->id === $lead->id
                && $event->oldStatus === 'new'
                && $event->newStatus === 'contacted'
                && $event->changedBy->id === $this->user->id;
        });
    }

    // =========================================================================
    // LmsNotification Record Creation Tests (no Event::fake())
    // =========================================================================

    /**
     * Test LmsNotification records are created for all LMS users when a lead is created.
     *
     * Validates: Real-time notification requirement
     */
    public function test_notification_records_created_for_all_lms_users_on_lead_creation(): void
    {
        // Create additional LMS users
        $user2 = User::factory()->create();
        $user2->givePermissionTo('access lms');

        $user3 = User::factory()->create();
        $user3->givePermissionTo('access lms');

        // Create a user without LMS access (should NOT get notification)
        User::factory()->create();

        $this->actingAs($this->user)
            ->post('/lms/leads', $this->validLeadData());

        // All 3 LMS users should have a notification
        $this->assertDatabaseCount('lms_notifications', 3);

        $this->assertDatabaseHas('lms_notifications', [
            'user_id' => $this->user->id,
            'type' => 'new_lead',
            'title' => 'New Lead',
        ]);

        $this->assertDatabaseHas('lms_notifications', [
            'user_id' => $user2->id,
            'type' => 'new_lead',
            'title' => 'New Lead',
        ]);

        $this->assertDatabaseHas('lms_notifications', [
            'user_id' => $user3->id,
            'type' => 'new_lead',
            'title' => 'New Lead',
        ]);
    }

    /**
     * Test LmsNotification records are created for all LMS users on status change.
     *
     * Validates: Real-time notification requirement
     */
    public function test_notification_records_created_for_all_lms_users_on_status_change(): void
    {
        // Create additional LMS user
        $user2 = User::factory()->create();
        $user2->givePermissionTo('access lms');

        $lead = CampaignLead::create([
            'full_name' => 'Notify Lead',
            'email' => 'notify@example.com',
            'phone_number' => '+919876543210',
            'date_of_birth' => '1990-01-01',
            'place_of_birth' => 'Mumbai',
            'status' => 'new',
        ]);

        $this->actingAs($this->user)
            ->put("/lms/leads/{$lead->id}/status", ['status' => 'contacted']);

        // Both LMS users should have a notification
        $this->assertDatabaseCount('lms_notifications', 2);

        $this->assertDatabaseHas('lms_notifications', [
            'user_id' => $this->user->id,
            'type' => 'status_changed',
            'title' => 'Lead Status Updated',
            'lead_id' => $lead->id,
        ]);

        $this->assertDatabaseHas('lms_notifications', [
            'user_id' => $user2->id,
            'type' => 'status_changed',
            'title' => 'Lead Status Updated',
            'lead_id' => $lead->id,
        ]);
    }

    // =========================================================================
    // Mark as Read Tests
    // =========================================================================

    /**
     * Test mark as read updates read_at on the notification.
     *
     * Validates: Real-time notification requirement
     */
    public function test_mark_as_read_updates_read_at(): void
    {
        $notification = LmsNotification::create([
            'user_id' => $this->user->id,
            'type' => 'new_lead',
            'title' => 'New Lead',
            'message' => 'Test notification',
            'read_at' => null,
        ]);

        $this->assertNull($notification->read_at);

        $response = $this->actingAs($this->user)
            ->put("/lms/notifications/{$notification->id}/read");

        $response->assertRedirect();

        $notification->refresh();
        $this->assertNotNull($notification->read_at);
    }

    /**
     * Test mark all as read updates all unread notifications for the user.
     *
     * Validates: Real-time notification requirement
     */
    public function test_mark_all_as_read_updates_all_unread_notifications(): void
    {
        // Create multiple unread notifications for the user
        $notification1 = LmsNotification::create([
            'user_id' => $this->user->id,
            'type' => 'new_lead',
            'title' => 'New Lead 1',
            'message' => 'First notification',
            'read_at' => null,
        ]);

        $notification2 = LmsNotification::create([
            'user_id' => $this->user->id,
            'type' => 'status_changed',
            'title' => 'Status Changed',
            'message' => 'Second notification',
            'read_at' => null,
        ]);

        // Create an already-read notification
        $notification3 = LmsNotification::create([
            'user_id' => $this->user->id,
            'type' => 'new_lead',
            'title' => 'Already Read',
            'message' => 'Third notification',
            'read_at' => now()->subHour(),
        ]);

        // Create a notification for another user (should NOT be affected)
        $otherUser = User::factory()->create();
        $otherUser->givePermissionTo('access lms');

        $otherNotification = LmsNotification::create([
            'user_id' => $otherUser->id,
            'type' => 'new_lead',
            'title' => 'Other User Notification',
            'message' => 'Other user notification',
            'read_at' => null,
        ]);

        $response = $this->actingAs($this->user)
            ->post('/lms/notifications/mark-all-read');

        $response->assertRedirect();

        // Both unread notifications for the user should now be read
        $notification1->refresh();
        $notification2->refresh();
        $this->assertNotNull($notification1->read_at);
        $this->assertNotNull($notification2->read_at);

        // Already-read notification should still have its original read_at
        $notification3->refresh();
        $this->assertNotNull($notification3->read_at);

        // Other user's notification should remain unread
        $otherNotification->refresh();
        $this->assertNull($otherNotification->read_at);
    }

    // =========================================================================
    // Notification Index View Tests
    // =========================================================================

    /**
     * Test notification index displays notifications for the authenticated user.
     *
     * Validates: Real-time notification requirement
     */
    public function test_notification_index_displays_notifications(): void
    {
        LmsNotification::create([
            'user_id' => $this->user->id,
            'type' => 'new_lead',
            'title' => 'New Lead Arrived',
            'message' => 'John Doe from website',
            'read_at' => null,
        ]);

        LmsNotification::create([
            'user_id' => $this->user->id,
            'type' => 'status_changed',
            'title' => 'Lead Status Updated',
            'message' => 'Jane Smith: new → contacted',
            'read_at' => now(),
        ]);

        $response = $this->actingAs($this->user)
            ->get('/lms/notifications');

        $response->assertStatus(200);
        $response->assertSee('New Lead Arrived');
        $response->assertSee('John Doe from website');
        $response->assertSee('Lead Status Updated');
        $response->assertSee('Jane Smith: new → contacted');
    }

    /**
     * Test notification index does not display other users' notifications.
     *
     * Validates: Real-time notification requirement
     */
    public function test_notification_index_does_not_show_other_users_notifications(): void
    {
        $otherUser = User::factory()->create();
        $otherUser->givePermissionTo('access lms');

        LmsNotification::create([
            'user_id' => $otherUser->id,
            'type' => 'new_lead',
            'title' => 'Other User Lead',
            'message' => 'Should not appear',
            'read_at' => null,
        ]);

        $response = $this->actingAs($this->user)
            ->get('/lms/notifications');

        $response->assertStatus(200);
        $response->assertDontSee('Other User Lead');
        $response->assertDontSee('Should not appear');
    }
}
