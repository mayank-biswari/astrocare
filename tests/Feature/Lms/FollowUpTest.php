<?php

namespace Tests\Feature\Lms;

use App\Models\CampaignLead;
use App\Models\LeadFollowUp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Feature tests for the LMS Follow-Up creation and completion.
 *
 * Validates: Requirements 7.1, 7.3, 7.4
 */
class FollowUpTest extends TestCase
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
            'full_name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'phone_number' => '+919876543210',
            'date_of_birth' => '1985-03-20',
            'place_of_birth' => 'Delhi',
            'message' => 'Wants a kundli reading',
            'source' => 'referral',
            'status' => 'contacted',
        ]);
    }

    // =========================================================================
    // Successful Follow-Up Creation (Requirement 7.1)
    // =========================================================================

    /**
     * Test successful follow-up creation with valid future date.
     *
     * Validates: Requirement 7.1
     */
    public function test_successful_follow_up_creation_with_valid_future_date(): void
    {
        $futureDate = now()->addDays(5)->toDateString();

        $response = $this->actingAs($this->user)
            ->post(route('lms.leads.follow-ups.store', $this->lead), [
                'description' => 'Call client to discuss consultation package.',
                'scheduled_date' => $futureDate,
            ]);

        $followUp = LeadFollowUp::where('description', 'Call client to discuss consultation package.')->first();

        $this->assertNotNull($followUp);
        $this->assertEquals($this->lead->id, $followUp->campaign_lead_id);
        $this->assertEquals($this->user->id, $followUp->user_id);
        $this->assertEquals($futureDate, $followUp->scheduled_date->toDateString());

        $response->assertRedirect(route('lms.leads.show', $this->lead));
    }

    /**
     * Test follow-up is associated with the correct lead and user.
     *
     * Validates: Requirement 7.1
     */
    public function test_follow_up_is_associated_with_correct_lead_and_user(): void
    {
        $futureDate = now()->addDays(3)->toDateString();

        $this->actingAs($this->user)
            ->post(route('lms.leads.follow-ups.store', $this->lead), [
                'description' => 'Send proposal document.',
                'scheduled_date' => $futureDate,
            ]);

        $followUp = LeadFollowUp::where('description', 'Send proposal document.')->first();

        $this->assertNotNull($followUp);
        $this->assertEquals($this->lead->id, $followUp->campaign_lead_id);
        $this->assertEquals($this->user->id, $followUp->user_id);
        $this->assertNull($followUp->completed_at);
        $this->assertNotNull($followUp->created_at);
    }

    /**
     * Test follow-up creation with today's date is accepted (after_or_equal:today).
     *
     * Validates: Requirement 7.4
     */
    public function test_follow_up_creation_with_today_date_is_accepted(): void
    {
        $today = now()->toDateString();

        $response = $this->actingAs($this->user)
            ->post(route('lms.leads.follow-ups.store', $this->lead), [
                'description' => 'Urgent follow-up today.',
                'scheduled_date' => $today,
            ]);

        $followUp = LeadFollowUp::where('description', 'Urgent follow-up today.')->first();

        $this->assertNotNull($followUp);
        $this->assertEquals($this->lead->id, $followUp->campaign_lead_id);
        $this->assertEquals($today, $followUp->scheduled_date->toDateString());

        $response->assertRedirect(route('lms.leads.show', $this->lead));
    }

    // =========================================================================
    // Past Date Rejection (Requirement 7.4)
    // =========================================================================

    /**
     * Test follow-up creation with past date is rejected.
     *
     * Validates: Requirement 7.4
     */
    public function test_follow_up_creation_with_past_date_is_rejected(): void
    {
        $pastDate = now()->subDays(3)->toDateString();

        $response = $this->actingAs($this->user)
            ->post(route('lms.leads.follow-ups.store', $this->lead), [
                'description' => 'This should fail.',
                'scheduled_date' => $pastDate,
            ]);

        $response->assertSessionHasErrors('scheduled_date');
        $this->assertDatabaseMissing('lead_follow_ups', [
            'campaign_lead_id' => $this->lead->id,
            'description' => 'This should fail.',
        ]);
    }

    // =========================================================================
    // Description Validation (Requirement 7.1)
    // =========================================================================

    /**
     * Test missing description is rejected.
     *
     * Validates: Requirement 7.1
     */
    public function test_missing_description_is_rejected(): void
    {
        $futureDate = now()->addDays(5)->toDateString();

        $response = $this->actingAs($this->user)
            ->post(route('lms.leads.follow-ups.store', $this->lead), [
                'description' => '',
                'scheduled_date' => $futureDate,
            ]);

        $response->assertSessionHasErrors('description');
        $this->assertDatabaseMissing('lead_follow_ups', [
            'campaign_lead_id' => $this->lead->id,
        ]);
    }

    /**
     * Test description exceeding 500 characters is rejected.
     *
     * Validates: Requirement 7.1
     */
    public function test_description_exceeding_500_chars_is_rejected(): void
    {
        $futureDate = now()->addDays(5)->toDateString();
        $longDescription = str_repeat('a', 501);

        $response = $this->actingAs($this->user)
            ->post(route('lms.leads.follow-ups.store', $this->lead), [
                'description' => $longDescription,
                'scheduled_date' => $futureDate,
            ]);

        $response->assertSessionHasErrors('description');
        $this->assertDatabaseMissing('lead_follow_ups', [
            'campaign_lead_id' => $this->lead->id,
        ]);
    }

    // =========================================================================
    // Follow-Up Completion (Requirement 7.3)
    // =========================================================================

    /**
     * Test follow-up completion sets completed_at to current timestamp.
     *
     * Validates: Requirement 7.3
     */
    public function test_follow_up_completion_sets_completed_at(): void
    {
        $followUp = LeadFollowUp::create([
            'campaign_lead_id' => $this->lead->id,
            'user_id' => $this->user->id,
            'description' => 'Call client about package.',
            'scheduled_date' => now()->addDays(2)->toDateString(),
            'completed_at' => null,
        ]);

        $this->assertNull($followUp->completed_at);

        Carbon::setTestNow(now());

        $response = $this->actingAs($this->user)
            ->put(route('lms.follow-ups.complete', $followUp));

        $followUp->refresh();

        $this->assertNotNull($followUp->completed_at);
        $this->assertEquals(now()->format('Y-m-d H:i'), $followUp->completed_at->format('Y-m-d H:i'));

        $response->assertRedirect();

        Carbon::setTestNow();
    }

    // =========================================================================
    // Redirect Behavior (Requirement 7.1)
    // =========================================================================

    /**
     * Test redirect back after follow-up creation.
     *
     * Validates: Requirement 7.1
     */
    public function test_redirects_to_lead_detail_after_creation(): void
    {
        $futureDate = now()->addDays(7)->toDateString();

        $response = $this->actingAs($this->user)
            ->post(route('lms.leads.follow-ups.store', $this->lead), [
                'description' => 'Schedule a meeting.',
                'scheduled_date' => $futureDate,
            ]);

        $response->assertRedirect(route('lms.leads.show', $this->lead));
    }

    /**
     * Test redirect back after follow-up completion.
     *
     * Validates: Requirement 7.3
     */
    public function test_redirects_back_after_completion(): void
    {
        $followUp = LeadFollowUp::create([
            'campaign_lead_id' => $this->lead->id,
            'user_id' => $this->user->id,
            'description' => 'Send email.',
            'scheduled_date' => now()->addDays(1)->toDateString(),
            'completed_at' => null,
        ]);

        $response = $this->actingAs($this->user)
            ->from(route('lms.leads.show', $this->lead))
            ->put(route('lms.follow-ups.complete', $followUp));

        $response->assertRedirect(route('lms.leads.show', $this->lead));
    }
}
