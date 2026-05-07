<?php

namespace Tests\Feature\Lms;

use App\Models\CampaignLead;
use App\Models\LeadFollowUp;
use App\Models\LeadNote;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Feature tests for the LMS Lead Detail View.
 *
 * Validates: Requirements 4.1, 4.2, 4.3, 4.4
 */
class LeadDetailTest extends TestCase
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
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
            'phone_number' => '+919876543210',
            'date_of_birth' => '1990-05-15',
            'place_of_birth' => 'Mumbai',
            'message' => 'Interested in astrology consultation',
            'source' => 'website',
            'status' => 'contacted',
        ]);
    }

    // =========================================================================
    // Lead Detail Shows All Fields (Requirement 4.1, 4.2)
    // =========================================================================

    /**
     * Test lead detail page shows all lead fields.
     *
     * Validates: Requirement 4.1, 4.2
     */
    public function test_lead_detail_shows_all_fields(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('lms.leads.show', $this->lead));

        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertSee('john@example.com');
        $response->assertSee('+919876543210');
        $response->assertSee('website');
        $response->assertSee('Contacted');
        $response->assertSee('May 15, 1990');
        $response->assertSee('Mumbai');
        $response->assertSee('Interested in astrology consultation');
    }

    // =========================================================================
    // Notes Display (Requirement 4.3)
    // =========================================================================

    /**
     * Test lead detail page shows notes in reverse chronological order.
     *
     * Validates: Requirement 4.3
     */
    public function test_notes_displayed_in_reverse_chronological_order(): void
    {
        // Create notes with specific timestamps
        $note1 = LeadNote::create([
            'campaign_lead_id' => $this->lead->id,
            'user_id' => $this->user->id,
            'body' => 'First note - oldest',
        ]);
        // Manually set created_at to ensure ordering
        LeadNote::where('id', $note1->id)->update(['created_at' => now()->subHours(3)]);

        $note2 = LeadNote::create([
            'campaign_lead_id' => $this->lead->id,
            'user_id' => $this->user->id,
            'body' => 'Second note - middle',
        ]);
        LeadNote::where('id', $note2->id)->update(['created_at' => now()->subHours(2)]);

        $note3 = LeadNote::create([
            'campaign_lead_id' => $this->lead->id,
            'user_id' => $this->user->id,
            'body' => 'Third note - newest',
        ]);
        LeadNote::where('id', $note3->id)->update(['created_at' => now()->subHour()]);

        $response = $this->actingAs($this->user)
            ->get(route('lms.leads.show', $this->lead));

        $response->assertStatus(200);
        $response->assertSee('First note - oldest');
        $response->assertSee('Second note - middle');
        $response->assertSee('Third note - newest');

        // Verify order: newest first in the response content
        $content = $response->getContent();
        $pos1 = strpos($content, 'Third note - newest');
        $pos2 = strpos($content, 'Second note - middle');
        $pos3 = strpos($content, 'First note - oldest');

        $this->assertLessThan($pos2, $pos1, 'Newest note should appear before middle note');
        $this->assertLessThan($pos3, $pos2, 'Middle note should appear before oldest note');
    }

    // =========================================================================
    // Follow-Ups Display (Requirement 4.4)
    // =========================================================================

    /**
     * Test lead detail page shows follow-ups.
     *
     * Validates: Requirement 4.4
     */
    public function test_lead_detail_shows_follow_ups(): void
    {
        LeadFollowUp::create([
            'campaign_lead_id' => $this->lead->id,
            'user_id' => $this->user->id,
            'description' => 'Call client next week',
            'scheduled_date' => now()->addDays(3)->toDateString(),
            'completed_at' => null,
        ]);

        LeadFollowUp::create([
            'campaign_lead_id' => $this->lead->id,
            'user_id' => $this->user->id,
            'description' => 'Send proposal',
            'scheduled_date' => now()->addDays(7)->toDateString(),
            'completed_at' => null,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('lms.leads.show', $this->lead));

        $response->assertStatus(200);
        $response->assertSee('Call client next week');
        $response->assertSee('Send proposal');
    }

    /**
     * Test overdue follow-ups are highlighted with visual indicator.
     *
     * Validates: Requirement 4.4
     */
    public function test_overdue_follow_ups_are_highlighted(): void
    {
        // Create an overdue follow-up (past date, not completed)
        LeadFollowUp::create([
            'campaign_lead_id' => $this->lead->id,
            'user_id' => $this->user->id,
            'description' => 'Overdue task',
            'scheduled_date' => now()->subDays(3)->toDateString(),
            'completed_at' => null,
        ]);

        // Create a future follow-up (not overdue)
        LeadFollowUp::create([
            'campaign_lead_id' => $this->lead->id,
            'user_id' => $this->user->id,
            'description' => 'Future task',
            'scheduled_date' => now()->addDays(5)->toDateString(),
            'completed_at' => null,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('lms.leads.show', $this->lead));

        $response->assertStatus(200);
        $response->assertSee('Overdue task');
        $response->assertSee('Future task');

        // Verify overdue follow-up has the red highlighting classes
        $content = $response->getContent();
        // The overdue follow-up should have bg-red-50 and border-red-400 classes
        $this->assertStringContainsString('bg-red-50', $content);
        // The "Overdue" text indicator should be present
        $response->assertSee('Overdue');
    }

    // =========================================================================
    // Empty State (Requirement 4.1)
    // =========================================================================

    /**
     * Test lead detail page loads correctly with no notes or follow-ups.
     *
     * Validates: Requirement 4.1
     */
    public function test_lead_detail_loads_with_no_notes_or_follow_ups(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('lms.leads.show', $this->lead));

        $response->assertStatus(200);
        $response->assertSee('John Doe');
        // Empty state messages from the view
        $response->assertSee('No notes yet');
        $response->assertSee('No follow-ups scheduled');
    }
}
