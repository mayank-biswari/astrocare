<?php

namespace Tests\Feature\Lms;

use App\Models\CampaignLead;
use App\Models\LeadNote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Feature tests for the LMS Lead Status Management.
 *
 * Validates: Requirements 5.1, 5.2, 5.4
 */
class LeadStatusTest extends TestCase
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
            'full_name' => 'Test Lead',
            'email' => 'test@example.com',
            'phone_number' => '+919876543210',
            'date_of_birth' => '1990-01-01',
            'place_of_birth' => 'Mumbai',
            'status' => 'new',
        ]);
    }

    // =========================================================================
    // Successful Status Update Tests (Requirement 5.1)
    // =========================================================================

    /**
     * Test status update to 'contacted' succeeds without note.
     *
     * Validates: Requirement 5.1, 5.2
     */
    public function test_status_update_to_contacted_succeeds(): void
    {
        $response = $this->actingAs($this->user)
            ->put("/lms/leads/{$this->lead->id}/status", ['status' => 'contacted']);

        $response->assertRedirect(route('lms.leads.show', $this->lead));
        $response->assertSessionHas('success');

        $this->lead->refresh();
        $this->assertEquals('contacted', $this->lead->status);
    }

    /**
     * Test status update to 'qualified' succeeds without note.
     *
     * Validates: Requirement 5.1, 5.2
     */
    public function test_status_update_to_qualified_succeeds(): void
    {
        $response = $this->actingAs($this->user)
            ->put("/lms/leads/{$this->lead->id}/status", ['status' => 'qualified']);

        $response->assertRedirect(route('lms.leads.show', $this->lead));

        $this->lead->refresh();
        $this->assertEquals('qualified', $this->lead->status);
    }

    /**
     * Test status update to 'new' succeeds.
     *
     * Validates: Requirement 5.1, 5.2
     */
    public function test_status_update_to_new_succeeds(): void
    {
        $this->lead->update(['status' => 'contacted']);

        $response = $this->actingAs($this->user)
            ->put("/lms/leads/{$this->lead->id}/status", ['status' => 'new']);

        $response->assertRedirect(route('lms.leads.show', $this->lead));

        $this->lead->refresh();
        $this->assertEquals('new', $this->lead->status);
    }

    // =========================================================================
    // Terminal Status Requires Note (Requirement 5.4)
    // =========================================================================

    /**
     * Test status update to 'converted' requires a note.
     *
     * Validates: Requirement 5.4
     */
    public function test_status_update_to_converted_requires_note(): void
    {
        $response = $this->actingAs($this->user)
            ->put("/lms/leads/{$this->lead->id}/status", ['status' => 'converted']);

        $response->assertSessionHasErrors('note');

        $this->lead->refresh();
        $this->assertEquals('new', $this->lead->status);
    }

    /**
     * Test status update to 'lost' requires a note.
     *
     * Validates: Requirement 5.4
     */
    public function test_status_update_to_lost_requires_note(): void
    {
        $response = $this->actingAs($this->user)
            ->put("/lms/leads/{$this->lead->id}/status", ['status' => 'lost']);

        $response->assertSessionHasErrors('note');

        $this->lead->refresh();
        $this->assertEquals('new', $this->lead->status);
    }

    /**
     * Test status update to 'converted' with note succeeds and creates note.
     *
     * Validates: Requirement 5.4
     */
    public function test_status_update_to_converted_with_note_succeeds(): void
    {
        $response = $this->actingAs($this->user)
            ->put("/lms/leads/{$this->lead->id}/status", [
                'status' => 'converted',
                'note' => 'Customer signed up for premium plan.',
            ]);

        $response->assertRedirect(route('lms.leads.show', $this->lead));

        $this->lead->refresh();
        $this->assertEquals('converted', $this->lead->status);

        $note = LeadNote::where('campaign_lead_id', $this->lead->id)->first();
        $this->assertNotNull($note);
        $this->assertEquals('Customer signed up for premium plan.', $note->body);
        $this->assertEquals($this->user->id, $note->user_id);
    }

    /**
     * Test status update to 'lost' with note succeeds and creates note.
     *
     * Validates: Requirement 5.4
     */
    public function test_status_update_to_lost_with_note_succeeds(): void
    {
        $response = $this->actingAs($this->user)
            ->put("/lms/leads/{$this->lead->id}/status", [
                'status' => 'lost',
                'note' => 'Customer chose a competitor.',
            ]);

        $response->assertRedirect(route('lms.leads.show', $this->lead));

        $this->lead->refresh();
        $this->assertEquals('lost', $this->lead->status);

        $note = LeadNote::where('campaign_lead_id', $this->lead->id)->first();
        $this->assertNotNull($note);
        $this->assertEquals('Customer chose a competitor.', $note->body);
        $this->assertEquals($this->user->id, $note->user_id);
    }

    // =========================================================================
    // Invalid Status Value Tests (Requirement 5.2)
    // =========================================================================

    /**
     * Test invalid status value is rejected.
     *
     * Validates: Requirement 5.2
     */
    public function test_invalid_status_value_is_rejected(): void
    {
        $response = $this->actingAs($this->user)
            ->put("/lms/leads/{$this->lead->id}/status", ['status' => 'invalid_status']);

        $response->assertSessionHasErrors('status');

        $this->lead->refresh();
        $this->assertEquals('new', $this->lead->status);
    }

    /**
     * Test empty status is rejected.
     *
     * Validates: Requirement 5.2
     */
    public function test_empty_status_is_rejected(): void
    {
        $response = $this->actingAs($this->user)
            ->put("/lms/leads/{$this->lead->id}/status", ['status' => '']);

        $response->assertSessionHasErrors('status');

        $this->lead->refresh();
        $this->assertEquals('new', $this->lead->status);
    }

    /**
     * Test missing status field is rejected.
     *
     * Validates: Requirement 5.2
     */
    public function test_missing_status_field_is_rejected(): void
    {
        $response = $this->actingAs($this->user)
            ->put("/lms/leads/{$this->lead->id}/status", []);

        $response->assertSessionHasErrors('status');

        $this->lead->refresh();
        $this->assertEquals('new', $this->lead->status);
    }

    // =========================================================================
    // Non-terminal status with optional note (Requirement 5.1)
    // =========================================================================

    /**
     * Test non-terminal status update does not create a note when none provided.
     *
     * Validates: Requirement 5.1
     */
    public function test_non_terminal_status_does_not_create_note(): void
    {
        $this->actingAs($this->user)
            ->put("/lms/leads/{$this->lead->id}/status", ['status' => 'contacted']);

        $this->assertDatabaseCount('lead_notes', 0);
    }
}
