<?php

namespace Tests\Feature\Lms;

use App\Models\CampaignLead;
use App\Models\LeadNote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Feature tests for the LMS Note creation.
 *
 * Validates: Requirements 6.1, 6.3
 */
class NoteTest extends TestCase
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
            'status' => 'new',
        ]);
    }

    // =========================================================================
    // Successful Note Creation (Requirement 6.1)
    // =========================================================================

    /**
     * Test successful note creation with valid body text.
     *
     * Validates: Requirement 6.1
     */
    public function test_successful_note_creation_with_valid_body(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('lms.leads.notes.store', $this->lead), [
                'body' => 'Called the client and discussed consultation options.',
            ]);

        $this->assertDatabaseHas('lead_notes', [
            'campaign_lead_id' => $this->lead->id,
            'user_id' => $this->user->id,
            'body' => 'Called the client and discussed consultation options.',
        ]);

        $response->assertRedirect(route('lms.leads.show', $this->lead));
    }

    /**
     * Test note is associated with the correct lead and user.
     *
     * Validates: Requirement 6.1
     */
    public function test_note_is_associated_with_correct_lead_and_user(): void
    {
        $this->actingAs($this->user)
            ->post(route('lms.leads.notes.store', $this->lead), [
                'body' => 'Follow-up discussion about services.',
            ]);

        $note = LeadNote::where('body', 'Follow-up discussion about services.')->first();

        $this->assertNotNull($note);
        $this->assertEquals($this->lead->id, $note->campaign_lead_id);
        $this->assertEquals($this->user->id, $note->user_id);
        $this->assertNotNull($note->created_at);
    }

    // =========================================================================
    // Empty Body Rejection (Requirement 6.3)
    // =========================================================================

    /**
     * Test empty string body is rejected.
     *
     * Validates: Requirement 6.3
     */
    public function test_empty_body_is_rejected(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('lms.leads.notes.store', $this->lead), [
                'body' => '',
            ]);

        $response->assertSessionHasErrors('body');
        $this->assertDatabaseMissing('lead_notes', [
            'campaign_lead_id' => $this->lead->id,
        ]);
    }

    /**
     * Test whitespace-only body is rejected (trimmed to empty).
     *
     * Validates: Requirement 6.3
     */
    public function test_whitespace_only_body_is_rejected(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('lms.leads.notes.store', $this->lead), [
                'body' => '   ',
            ]);

        $response->assertSessionHasErrors('body');
        $this->assertDatabaseMissing('lead_notes', [
            'campaign_lead_id' => $this->lead->id,
        ]);
    }

    // =========================================================================
    // Redirect Behavior (Requirement 6.1)
    // =========================================================================

    /**
     * Test redirect back to lead detail after note creation.
     *
     * Validates: Requirement 6.1
     */
    public function test_redirects_to_lead_detail_after_creation(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('lms.leads.notes.store', $this->lead), [
                'body' => 'A valid note body.',
            ]);

        $response->assertRedirect(route('lms.leads.show', $this->lead));
    }
}
