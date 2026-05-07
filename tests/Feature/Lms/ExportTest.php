<?php

namespace Tests\Feature\Lms;

use App\Models\CampaignLead;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Feature tests for the LMS CSV Export.
 *
 * Validates: Requirements 9.1, 9.2, 9.3
 */
class ExportTest extends TestCase
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
    // CSV Export Response Tests (Requirement 9.1, 9.2)
    // =========================================================================

    /**
     * Test CSV export returns a streamed response with correct content type.
     *
     * Validates: Requirement 9.1
     */
    public function test_csv_export_returns_streamed_response_with_correct_content_type(): void
    {
        $this->createLead();

        $response = $this->actingAs($this->user)->get('/lms/export');

        $response->assertStatus(200);
        $this->assertStringStartsWith('text/csv', $response->headers->get('Content-Type'));
    }

    /**
     * Test CSV export contains the correct column headers.
     *
     * Validates: Requirement 9.2
     */
    public function test_csv_export_contains_correct_column_headers(): void
    {
        $this->createLead();

        $response = $this->actingAs($this->user)->get('/lms/export');

        $response->assertStatus(200);

        $content = $response->streamedContent();
        $lines = explode("\n", trim($content));
        $header = str_getcsv($lines[0]);

        $expectedColumns = ['full_name', 'email', 'phone_number', 'date_of_birth', 'place_of_birth', 'source', 'status', 'message', 'created_at'];
        $this->assertEquals($expectedColumns, $header);
    }

    /**
     * Test CSV export contains lead data rows.
     *
     * Validates: Requirement 9.1, 9.2
     */
    public function test_csv_export_contains_lead_data(): void
    {
        $lead = $this->createLead([
            'full_name' => 'John Smith',
            'email' => 'john@example.com',
            'phone_number' => '+919876543210',
            'date_of_birth' => '1990-05-15',
            'place_of_birth' => 'Mumbai',
            'source' => 'website',
            'status' => 'new',
            'message' => 'Test message',
        ]);

        $response = $this->actingAs($this->user)->get('/lms/export');

        $content = $response->streamedContent();
        $lines = explode("\n", trim($content));

        // Should have header + 1 data row
        $this->assertCount(2, $lines);

        $dataRow = str_getcsv($lines[1]);
        $this->assertEquals('John Smith', $dataRow[0]);
        $this->assertEquals('john@example.com', $dataRow[1]);
        $this->assertEquals('+919876543210', $dataRow[2]);
        $this->assertEquals('1990-05-15', $dataRow[3]);
        $this->assertEquals('Mumbai', $dataRow[4]);
        $this->assertEquals('website', $dataRow[5]);
        $this->assertEquals('new', $dataRow[6]);
        $this->assertEquals('Test message', $dataRow[7]);
    }

    // =========================================================================
    // Filter Tests (Requirement 9.1)
    // =========================================================================

    /**
     * Test CSV export applies status filter correctly.
     *
     * Validates: Requirement 9.1
     */
    public function test_csv_export_applies_status_filter(): void
    {
        $this->createLead(['full_name' => 'Lead A', 'status' => 'new']);
        $this->createLead(['full_name' => 'Lead B', 'status' => 'contacted']);
        $this->createLead(['full_name' => 'Lead C', 'status' => 'new']);

        $response = $this->actingAs($this->user)->get('/lms/export?status=new');

        $response->assertStatus(200);

        $content = $response->streamedContent();
        $lines = explode("\n", trim($content));

        // Header + 2 data rows (only 'new' status leads)
        $this->assertCount(3, $lines);

        // Verify all data rows have 'new' status
        for ($i = 1; $i < count($lines); $i++) {
            $row = str_getcsv($lines[$i]);
            $this->assertEquals('new', $row[6]); // status is column index 6
        }
    }

    /**
     * Test CSV export applies search filter correctly.
     *
     * Validates: Requirement 9.1
     */
    public function test_csv_export_applies_search_filter(): void
    {
        $this->createLead(['full_name' => 'Alice Johnson', 'email' => 'alice@example.com']);
        $this->createLead(['full_name' => 'Bob Smith', 'email' => 'bob@example.com']);
        $this->createLead(['full_name' => 'Charlie Johnson', 'email' => 'charlie@example.com']);

        $response = $this->actingAs($this->user)->get('/lms/export?search=Johnson');

        $response->assertStatus(200);

        $content = $response->streamedContent();
        $lines = explode("\n", trim($content));

        // Header + 2 data rows (Alice Johnson and Charlie Johnson)
        $this->assertCount(3, $lines);

        $names = [];
        for ($i = 1; $i < count($lines); $i++) {
            $row = str_getcsv($lines[$i]);
            $names[] = $row[0];
        }
        $this->assertContains('Alice Johnson', $names);
        $this->assertContains('Charlie Johnson', $names);
    }

    /**
     * Test CSV export applies source filter correctly.
     *
     * Validates: Requirement 9.1
     */
    public function test_csv_export_applies_source_filter(): void
    {
        $this->createLead(['full_name' => 'Lead A', 'source' => 'website']);
        $this->createLead(['full_name' => 'Lead B', 'source' => 'referral']);
        $this->createLead(['full_name' => 'Lead C', 'source' => 'website']);

        $response = $this->actingAs($this->user)->get('/lms/export?source=referral');

        $response->assertStatus(200);

        $content = $response->streamedContent();
        $lines = explode("\n", trim($content));

        // Header + 1 data row (only referral source)
        $this->assertCount(2, $lines);

        $row = str_getcsv($lines[1]);
        $this->assertEquals('Lead B', $row[0]);
        $this->assertEquals('referral', $row[5]); // source is column index 5
    }

    /**
     * Test CSV export applies date range filter correctly.
     *
     * Validates: Requirement 9.1
     */
    public function test_csv_export_applies_date_range_filter(): void
    {
        $this->createLead(['full_name' => 'Lead A'], Carbon::parse('2025-01-10 12:00:00'));
        $this->createLead(['full_name' => 'Lead B'], Carbon::parse('2025-01-15 12:00:00'));
        $this->createLead(['full_name' => 'Lead C'], Carbon::parse('2025-02-01 12:00:00'));

        $response = $this->actingAs($this->user)
            ->get('/lms/export?date_from=2025-01-01&date_to=2025-01-20');

        $response->assertStatus(200);

        $content = $response->streamedContent();
        $lines = explode("\n", trim($content));

        // Header + 2 data rows (Lead A and Lead B within range)
        $this->assertCount(3, $lines);
    }

    // =========================================================================
    // No Matching Leads Tests (Requirement 9.3)
    // =========================================================================

    /**
     * Test CSV export with no matching leads redirects back with info message.
     *
     * Validates: Requirement 9.3
     */
    public function test_csv_export_with_no_matching_leads_redirects_with_message(): void
    {
        $this->createLead(['status' => 'new']);

        $response = $this->actingAs($this->user)
            ->get('/lms/export?status=converted');

        $response->assertRedirect();
        $response->assertSessionHas('info', 'No leads match the current filters');
    }

    /**
     * Test CSV export with no leads at all redirects back with info message.
     *
     * Validates: Requirement 9.3
     */
    public function test_csv_export_with_no_leads_redirects_with_message(): void
    {
        $response = $this->actingAs($this->user)
            ->from('/lms/leads')
            ->get('/lms/export');

        $response->assertRedirect();
        $response->assertSessionHas('info', 'No leads match the current filters');
    }
}
