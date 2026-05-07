<?php

namespace Tests\Unit\Lms;

use App\Models\CampaignLead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignLeadScopesTest extends TestCase
{
    use RefreshDatabase;

    private function createLead(array $attributes = []): CampaignLead
    {
        return CampaignLead::create(array_merge([
            'full_name' => 'John Doe',
            'date_of_birth' => '1990-01-15',
            'place_of_birth' => 'Mumbai',
            'phone_number' => '+919876543210',
            'email' => 'john@example.com',
            'message' => 'Test message',
            'source' => 'website',
            'status' => 'new',
        ], $attributes));
    }

    // --- scopeSearch tests ---

    public function test_search_scope_filters_by_full_name(): void
    {
        $this->createLead(['full_name' => 'Alice Smith']);
        $this->createLead(['full_name' => 'Bob Jones']);

        $results = CampaignLead::search('Alice')->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Alice Smith', $results->first()->full_name);
    }

    public function test_search_scope_filters_by_email(): void
    {
        $this->createLead(['email' => 'alice@test.com', 'full_name' => 'Lead One']);
        $this->createLead(['email' => 'bob@test.com', 'full_name' => 'Lead Two']);

        $results = CampaignLead::search('alice@test')->get();

        $this->assertCount(1, $results);
        $this->assertEquals('alice@test.com', $results->first()->email);
    }

    public function test_search_scope_filters_by_phone_number(): void
    {
        $this->createLead(['phone_number' => '+911111111111', 'full_name' => 'Lead One']);
        $this->createLead(['phone_number' => '+912222222222', 'full_name' => 'Lead Two']);

        $results = CampaignLead::search('1111111111')->get();

        $this->assertCount(1, $results);
        $this->assertEquals('+911111111111', $results->first()->phone_number);
    }

    public function test_search_scope_returns_all_when_term_is_null(): void
    {
        $this->createLead(['full_name' => 'Lead One']);
        $this->createLead(['full_name' => 'Lead Two']);

        $results = CampaignLead::search(null)->get();

        $this->assertCount(2, $results);
    }

    public function test_search_scope_returns_all_when_term_is_empty(): void
    {
        $this->createLead(['full_name' => 'Lead One']);
        $this->createLead(['full_name' => 'Lead Two']);

        $results = CampaignLead::search('')->get();

        $this->assertCount(2, $results);
    }

    // --- scopeFilterStatus tests ---

    public function test_filter_status_scope_filters_by_exact_status(): void
    {
        $this->createLead(['full_name' => 'New Lead', 'status' => 'new']);
        $this->createLead(['full_name' => 'Contacted Lead', 'status' => 'contacted']);
        $this->createLead(['full_name' => 'Qualified Lead', 'status' => 'qualified']);

        $results = CampaignLead::filterStatus('contacted')->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Contacted Lead', $results->first()->full_name);
    }

    public function test_filter_status_scope_returns_all_when_null(): void
    {
        $this->createLead(['status' => 'new']);
        $this->createLead(['status' => 'contacted']);

        $results = CampaignLead::filterStatus(null)->get();

        $this->assertCount(2, $results);
    }

    // --- scopeFilterSource tests ---

    public function test_filter_source_scope_filters_by_exact_source(): void
    {
        $this->createLead(['full_name' => 'Web Lead', 'source' => 'website']);
        $this->createLead(['full_name' => 'Campaign Lead', 'source' => 'facebook']);

        $results = CampaignLead::filterSource('website')->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Web Lead', $results->first()->full_name);
    }

    public function test_filter_source_scope_returns_all_when_null(): void
    {
        $this->createLead(['source' => 'website']);
        $this->createLead(['source' => 'facebook']);

        $results = CampaignLead::filterSource(null)->get();

        $this->assertCount(2, $results);
    }

    // --- scopeFilterDateRange tests ---

    public function test_filter_date_range_scope_filters_within_range(): void
    {
        $lead1 = $this->createLead(['full_name' => 'Old Lead']);
        CampaignLead::where('id', $lead1->id)->update(['created_at' => '2025-01-01 10:00:00']);

        $lead2 = $this->createLead(['full_name' => 'Recent Lead']);
        CampaignLead::where('id', $lead2->id)->update(['created_at' => '2025-06-15 10:00:00']);

        $lead3 = $this->createLead(['full_name' => 'Future Lead']);
        CampaignLead::where('id', $lead3->id)->update(['created_at' => '2025-12-01 10:00:00']);

        $results = CampaignLead::filterDateRange('2025-06-01', '2025-06-30')->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Recent Lead', $results->first()->full_name);
    }

    public function test_filter_date_range_scope_with_only_from_date(): void
    {
        $lead1 = $this->createLead(['full_name' => 'Old Lead']);
        CampaignLead::where('id', $lead1->id)->update(['created_at' => '2025-01-01 10:00:00']);

        $lead2 = $this->createLead(['full_name' => 'Recent Lead']);
        CampaignLead::where('id', $lead2->id)->update(['created_at' => '2025-06-15 10:00:00']);

        $results = CampaignLead::filterDateRange('2025-06-01', null)->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Recent Lead', $results->first()->full_name);
    }

    public function test_filter_date_range_scope_with_only_to_date(): void
    {
        $lead1 = $this->createLead(['full_name' => 'Old Lead']);
        CampaignLead::where('id', $lead1->id)->update(['created_at' => '2025-01-01 10:00:00']);

        $lead2 = $this->createLead(['full_name' => 'Recent Lead']);
        CampaignLead::where('id', $lead2->id)->update(['created_at' => '2025-06-15 10:00:00']);

        $results = CampaignLead::filterDateRange(null, '2025-03-01')->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Old Lead', $results->first()->full_name);
    }

    public function test_filter_date_range_scope_returns_all_when_both_null(): void
    {
        $this->createLead(['full_name' => 'Lead One']);
        $this->createLead(['full_name' => 'Lead Two']);

        $results = CampaignLead::filterDateRange(null, null)->get();

        $this->assertCount(2, $results);
    }
}
