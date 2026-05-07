<?php

namespace Tests\Feature\Lms;

use App\Models\CampaignLead;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Feature tests for the LMS Lead Listing, Search, Filter, and Sort.
 *
 * Validates: Requirements 3.1, 3.2, 3.3, 3.4, 3.5, 3.7
 */
class LeadListTest extends TestCase
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
     * Helper to create a lead with specific attributes.
     */
    private function createLead(array $attributes = [], ?Carbon $createdAt = null): CampaignLead
    {
        $lead = CampaignLead::create(array_merge([
            'full_name' => fake()->name(),
            'date_of_birth' => '1990-01-15',
            'place_of_birth' => 'Test City',
            'phone_number' => '+919876543210',
            'email' => fake()->unique()->safeEmail(),
            'source' => 'website',
            'status' => 'new',
        ], $attributes));

        if ($createdAt) {
            CampaignLead::where('id', $lead->id)->update(['created_at' => $createdAt]);
            $lead->refresh();
        }

        return $lead;
    }

    // =========================================================================
    // Pagination Tests (Requirement 3.1)
    // =========================================================================

    /**
     * Test that the lead list paginates at 20 per page.
     *
     * Validates: Requirement 3.1
     */
    public function test_lead_list_returns_20_per_page(): void
    {
        // Create 25 leads
        for ($i = 0; $i < 25; $i++) {
            $this->createLead();
        }

        $response = $this->actingAs($this->user)->get('/lms/leads');

        $response->assertStatus(200);

        $leads = $response->viewData('leads');

        $this->assertCount(20, $leads);
        $this->assertEquals(25, $leads->total());
        $this->assertEquals(2, $leads->lastPage());
    }

    /**
     * Test that the second page shows remaining leads.
     *
     * Validates: Requirement 3.1
     */
    public function test_lead_list_second_page_shows_remaining(): void
    {
        // Create 25 leads
        for ($i = 0; $i < 25; $i++) {
            $this->createLead();
        }

        $response = $this->actingAs($this->user)->get('/lms/leads?page=2');

        $response->assertStatus(200);

        $leads = $response->viewData('leads');

        $this->assertCount(5, $leads);
    }

    // =========================================================================
    // Search Tests (Requirement 3.2)
    // =========================================================================

    /**
     * Test that search filters by full_name.
     *
     * Validates: Requirement 3.2
     */
    public function test_search_filters_by_full_name(): void
    {
        $this->createLead(['full_name' => 'John Smith']);
        $this->createLead(['full_name' => 'Jane Doe']);
        $this->createLead(['full_name' => 'Bob Johnson']);

        $response = $this->actingAs($this->user)->get('/lms/leads?search=John');

        $response->assertStatus(200);

        $leads = $response->viewData('leads');

        $this->assertCount(2, $leads); // John Smith and Bob Johnson
        $names = $leads->pluck('full_name')->toArray();
        $this->assertContains('John Smith', $names);
        $this->assertContains('Bob Johnson', $names);
    }

    /**
     * Test that search filters by email.
     *
     * Validates: Requirement 3.2
     */
    public function test_search_filters_by_email(): void
    {
        $this->createLead(['email' => 'alice@example.com']);
        $this->createLead(['email' => 'bob@example.com']);
        $this->createLead(['email' => 'charlie@other.com']);

        $response = $this->actingAs($this->user)->get('/lms/leads?search=example.com');

        $response->assertStatus(200);

        $leads = $response->viewData('leads');

        $this->assertCount(2, $leads);
        $emails = $leads->pluck('email')->toArray();
        $this->assertContains('alice@example.com', $emails);
        $this->assertContains('bob@example.com', $emails);
    }

    /**
     * Test that search filters by phone_number.
     *
     * Validates: Requirement 3.2
     */
    public function test_search_filters_by_phone_number(): void
    {
        $this->createLead(['phone_number' => '+911234567890']);
        $this->createLead(['phone_number' => '+919876543210']);
        $this->createLead(['phone_number' => '+911234000000']);

        $response = $this->actingAs($this->user)->get('/lms/leads?search=1234');

        $response->assertStatus(200);

        $leads = $response->viewData('leads');

        $this->assertCount(2, $leads);
        $phones = $leads->pluck('phone_number')->toArray();
        $this->assertContains('+911234567890', $phones);
        $this->assertContains('+911234000000', $phones);
    }

    /**
     * Test that search with no matches returns empty results.
     *
     * Validates: Requirement 3.2
     */
    public function test_search_with_no_matches_returns_empty(): void
    {
        $this->createLead(['full_name' => 'John Smith']);

        $response = $this->actingAs($this->user)->get('/lms/leads?search=nonexistent');

        $response->assertStatus(200);

        $leads = $response->viewData('leads');

        $this->assertCount(0, $leads);
    }

    // =========================================================================
    // Status Filter Tests (Requirement 3.3)
    // =========================================================================

    /**
     * Test that status filter returns only leads with matching status.
     *
     * Validates: Requirement 3.3
     */
    public function test_status_filter_returns_matching_leads(): void
    {
        $this->createLead(['status' => 'new']);
        $this->createLead(['status' => 'new']);
        $this->createLead(['status' => 'contacted']);
        $this->createLead(['status' => 'qualified']);
        $this->createLead(['status' => 'converted']);

        $response = $this->actingAs($this->user)->get('/lms/leads?status=new');

        $response->assertStatus(200);

        $leads = $response->viewData('leads');

        $this->assertCount(2, $leads);
        foreach ($leads as $lead) {
            $this->assertEquals('new', $lead->status);
        }
    }

    /**
     * Test that status filter works for each valid status.
     *
     * Validates: Requirement 3.3
     */
    public function test_status_filter_works_for_contacted(): void
    {
        $this->createLead(['status' => 'new']);
        $this->createLead(['status' => 'contacted']);
        $this->createLead(['status' => 'contacted']);
        $this->createLead(['status' => 'qualified']);

        $response = $this->actingAs($this->user)->get('/lms/leads?status=contacted');

        $response->assertStatus(200);

        $leads = $response->viewData('leads');

        $this->assertCount(2, $leads);
        foreach ($leads as $lead) {
            $this->assertEquals('contacted', $lead->status);
        }
    }

    // =========================================================================
    // Source Filter Tests (Requirement 3.4)
    // =========================================================================

    /**
     * Test that source filter returns only leads with matching source.
     *
     * Validates: Requirement 3.4
     */
    public function test_source_filter_returns_matching_leads(): void
    {
        $this->createLead(['source' => 'website']);
        $this->createLead(['source' => 'website']);
        $this->createLead(['source' => 'referral']);
        $this->createLead(['source' => 'tarot-reading-campaign']);

        $response = $this->actingAs($this->user)->get('/lms/leads?source=website');

        $response->assertStatus(200);

        $leads = $response->viewData('leads');

        $this->assertCount(2, $leads);
        foreach ($leads as $lead) {
            $this->assertEquals('website', $lead->source);
        }
    }

    /**
     * Test that source filter works for different sources.
     *
     * Validates: Requirement 3.4
     */
    public function test_source_filter_works_for_referral(): void
    {
        $this->createLead(['source' => 'website']);
        $this->createLead(['source' => 'referral']);
        $this->createLead(['source' => 'referral']);
        $this->createLead(['source' => 'referral']);

        $response = $this->actingAs($this->user)->get('/lms/leads?source=referral');

        $response->assertStatus(200);

        $leads = $response->viewData('leads');

        $this->assertCount(3, $leads);
        foreach ($leads as $lead) {
            $this->assertEquals('referral', $lead->source);
        }
    }

    // =========================================================================
    // Date Range Filter Tests (Requirement 3.5)
    // =========================================================================

    /**
     * Test that date range filter returns leads within the specified range.
     *
     * Validates: Requirement 3.5
     */
    public function test_date_range_filter_returns_leads_within_range(): void
    {
        $this->createLead([], Carbon::parse('2025-01-10 12:00:00'));
        $this->createLead([], Carbon::parse('2025-01-15 12:00:00'));
        $this->createLead([], Carbon::parse('2025-01-20 12:00:00'));
        $this->createLead([], Carbon::parse('2025-02-01 12:00:00'));
        $this->createLead([], Carbon::parse('2024-12-25 12:00:00'));

        $response = $this->actingAs($this->user)->get('/lms/leads?date_from=2025-01-10&date_to=2025-01-20');

        $response->assertStatus(200);

        $leads = $response->viewData('leads');

        $this->assertCount(3, $leads);
    }

    /**
     * Test that date_from filter alone works correctly.
     *
     * Validates: Requirement 3.5
     */
    public function test_date_from_filter_alone(): void
    {
        $this->createLead([], Carbon::parse('2025-01-05 12:00:00'));
        $this->createLead([], Carbon::parse('2025-01-15 12:00:00'));
        $this->createLead([], Carbon::parse('2025-01-25 12:00:00'));

        $response = $this->actingAs($this->user)->get('/lms/leads?date_from=2025-01-10');

        $response->assertStatus(200);

        $leads = $response->viewData('leads');

        $this->assertCount(2, $leads);
    }

    /**
     * Test that date_to filter alone works correctly.
     *
     * Validates: Requirement 3.5
     */
    public function test_date_to_filter_alone(): void
    {
        $this->createLead([], Carbon::parse('2025-01-05 12:00:00'));
        $this->createLead([], Carbon::parse('2025-01-15 12:00:00'));
        $this->createLead([], Carbon::parse('2025-01-25 12:00:00'));

        $response = $this->actingAs($this->user)->get('/lms/leads?date_to=2025-01-15');

        $response->assertStatus(200);

        $leads = $response->viewData('leads');

        $this->assertCount(2, $leads);
    }

    // =========================================================================
    // Sorting Tests (Requirement 3.7)
    // =========================================================================

    /**
     * Test that sorting by full_name ascending works.
     *
     * Validates: Requirement 3.7
     */
    public function test_sort_by_full_name_ascending(): void
    {
        $this->createLead(['full_name' => 'Charlie Brown']);
        $this->createLead(['full_name' => 'Alice Smith']);
        $this->createLead(['full_name' => 'Bob Jones']);

        $response = $this->actingAs($this->user)->get('/lms/leads?sort_by=full_name&sort_dir=asc');

        $response->assertStatus(200);

        $leads = $response->viewData('leads');

        $names = $leads->pluck('full_name')->toArray();
        $this->assertEquals(['Alice Smith', 'Bob Jones', 'Charlie Brown'], $names);
    }

    /**
     * Test that sorting by full_name descending works.
     *
     * Validates: Requirement 3.7
     */
    public function test_sort_by_full_name_descending(): void
    {
        $this->createLead(['full_name' => 'Charlie Brown']);
        $this->createLead(['full_name' => 'Alice Smith']);
        $this->createLead(['full_name' => 'Bob Jones']);

        $response = $this->actingAs($this->user)->get('/lms/leads?sort_by=full_name&sort_dir=desc');

        $response->assertStatus(200);

        $leads = $response->viewData('leads');

        $names = $leads->pluck('full_name')->toArray();
        $this->assertEquals(['Charlie Brown', 'Bob Jones', 'Alice Smith'], $names);
    }

    /**
     * Test that sorting by created_at works.
     *
     * Validates: Requirement 3.7
     */
    public function test_sort_by_created_at_ascending(): void
    {
        $lead1 = $this->createLead(['full_name' => 'First'], Carbon::parse('2025-01-01'));
        $lead2 = $this->createLead(['full_name' => 'Second'], Carbon::parse('2025-01-15'));
        $lead3 = $this->createLead(['full_name' => 'Third'], Carbon::parse('2025-01-10'));

        $response = $this->actingAs($this->user)->get('/lms/leads?sort_by=created_at&sort_dir=asc');

        $response->assertStatus(200);

        $leads = $response->viewData('leads');

        $names = $leads->pluck('full_name')->toArray();
        $this->assertEquals(['First', 'Third', 'Second'], $names);
    }

    /**
     * Test that default sort is created_at descending.
     *
     * Validates: Requirement 3.7
     */
    public function test_default_sort_is_created_at_descending(): void
    {
        $lead1 = $this->createLead(['full_name' => 'First'], Carbon::parse('2025-01-01'));
        $lead2 = $this->createLead(['full_name' => 'Second'], Carbon::parse('2025-01-15'));
        $lead3 = $this->createLead(['full_name' => 'Third'], Carbon::parse('2025-01-10'));

        $response = $this->actingAs($this->user)->get('/lms/leads');

        $response->assertStatus(200);

        $leads = $response->viewData('leads');

        $names = $leads->pluck('full_name')->toArray();
        $this->assertEquals(['Second', 'Third', 'First'], $names);
    }

    /**
     * Test that an invalid sort column falls back to created_at desc.
     *
     * Validates: Requirement 3.7
     */
    public function test_invalid_sort_column_falls_back_to_default(): void
    {
        $lead1 = $this->createLead(['full_name' => 'First'], Carbon::parse('2025-01-01'));
        $lead2 = $this->createLead(['full_name' => 'Second'], Carbon::parse('2025-01-15'));
        $lead3 = $this->createLead(['full_name' => 'Third'], Carbon::parse('2025-01-10'));

        $response = $this->actingAs($this->user)->get('/lms/leads?sort_by=invalid_column&sort_dir=asc');

        $response->assertStatus(200);

        $leads = $response->viewData('leads');

        // Should fall back to created_at asc (since sort_dir=asc is still valid)
        $names = $leads->pluck('full_name')->toArray();
        $this->assertEquals(['First', 'Third', 'Second'], $names);
    }

    /**
     * Test that an invalid sort direction falls back to desc.
     *
     * Validates: Requirement 3.7
     */
    public function test_invalid_sort_direction_falls_back_to_desc(): void
    {
        $lead1 = $this->createLead(['full_name' => 'First'], Carbon::parse('2025-01-01'));
        $lead2 = $this->createLead(['full_name' => 'Second'], Carbon::parse('2025-01-15'));
        $lead3 = $this->createLead(['full_name' => 'Third'], Carbon::parse('2025-01-10'));

        $response = $this->actingAs($this->user)->get('/lms/leads?sort_by=created_at&sort_dir=invalid');

        $response->assertStatus(200);

        $leads = $response->viewData('leads');

        // Should fall back to desc
        $names = $leads->pluck('full_name')->toArray();
        $this->assertEquals(['Second', 'Third', 'First'], $names);
    }

    /**
     * Test sorting by status column.
     *
     * Validates: Requirement 3.7
     */
    public function test_sort_by_status(): void
    {
        $this->createLead(['full_name' => 'Lead A', 'status' => 'qualified']);
        $this->createLead(['full_name' => 'Lead B', 'status' => 'contacted']);
        $this->createLead(['full_name' => 'Lead C', 'status' => 'new']);

        $response = $this->actingAs($this->user)->get('/lms/leads?sort_by=status&sort_dir=asc');

        $response->assertStatus(200);

        $leads = $response->viewData('leads');

        $statuses = $leads->pluck('status')->toArray();
        $this->assertEquals(['contacted', 'new', 'qualified'], $statuses);
    }
}
