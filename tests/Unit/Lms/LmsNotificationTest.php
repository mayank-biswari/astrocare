<?php

namespace Tests\Unit\Lms;

use App\Models\LmsNotification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LmsNotificationTest extends TestCase
{
    use RefreshDatabase;

    private function createNotification(array $attributes = []): LmsNotification
    {
        $user = User::factory()->create();

        return LmsNotification::create(array_merge([
            'user_id' => $user->id,
            'type' => 'new_lead',
            'title' => 'New Lead',
            'message' => 'A new lead has arrived',
            'lead_id' => null,
            'data' => null,
            'read_at' => null,
        ], $attributes));
    }

    public function test_is_read_returns_true_when_read_at_is_set(): void
    {
        $notification = $this->createNotification([
            'read_at' => Carbon::now(),
        ]);

        $this->assertTrue($notification->isRead());
    }

    public function test_is_read_returns_false_when_read_at_is_null(): void
    {
        $notification = $this->createNotification([
            'read_at' => null,
        ]);

        $this->assertFalse($notification->isRead());
    }

    public function test_scope_unread_returns_only_notifications_with_null_read_at(): void
    {
        $user = User::factory()->create();

        LmsNotification::create([
            'user_id' => $user->id,
            'type' => 'new_lead',
            'title' => 'Unread Notification',
            'message' => 'This is unread',
            'read_at' => null,
        ]);

        LmsNotification::create([
            'user_id' => $user->id,
            'type' => 'status_changed',
            'title' => 'Read Notification',
            'message' => 'This is read',
            'read_at' => Carbon::now(),
        ]);

        LmsNotification::create([
            'user_id' => $user->id,
            'type' => 'follow_up_overdue',
            'title' => 'Another Unread',
            'message' => 'This is also unread',
            'read_at' => null,
        ]);

        $unreadNotifications = LmsNotification::unread()->get();

        $this->assertCount(2, $unreadNotifications);
        $this->assertTrue($unreadNotifications->every(fn ($n) => $n->read_at === null));
    }
}
