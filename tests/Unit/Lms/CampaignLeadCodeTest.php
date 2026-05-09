<?php

namespace Tests\Unit\Lms;

use App\Models\CampaignLead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class CampaignLeadCodeTest extends TestCase
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

    // --- Auto-generation tests ---

    public function test_lead_code_is_auto_generated_on_creation(): void
    {
        $lead = $this->createLead();

        $this->assertNotNull($lead->lead_code);
        $this->assertMatchesRegularExpression('/^LD-[A-Z0-9]{8}$/', $lead->lead_code);
    }

    public function test_lead_code_format_is_ld_prefix_with_8_alphanumeric_chars(): void
    {
        $lead = $this->createLead();

        $this->assertStringStartsWith('LD-', $lead->lead_code);
        $this->assertEquals(11, strlen($lead->lead_code)); // "LD-" (3) + 8 chars = 11
    }

    public function test_lead_code_is_unique_across_leads(): void
    {
        $lead1 = $this->createLead(['email' => 'lead1@example.com']);
        $lead2 = $this->createLead(['email' => 'lead2@example.com']);

        $this->assertNotEquals($lead1->lead_code, $lead2->lead_code);
    }

    public function test_lead_code_is_not_overwritten_if_provided(): void
    {
        $lead = $this->createLead(['lead_code' => 'LD-CUSTOM01']);

        $this->assertEquals('LD-CUSTOM01', $lead->lead_code);
    }

    // --- Immutability tests ---

    public function test_lead_code_cannot_be_changed_after_creation(): void
    {
        $lead = $this->createLead();
        $originalCode = $lead->lead_code;

        $lead->lead_code = 'LD-CHANGED1';
        $lead->save();

        $lead->refresh();
        $this->assertEquals($originalCode, $lead->lead_code);
    }

    public function test_other_fields_can_still_be_updated(): void
    {
        $lead = $this->createLead();
        $originalCode = $lead->lead_code;

        $lead->full_name = 'Updated Name';
        $lead->save();

        $lead->refresh();
        $this->assertEquals('Updated Name', $lead->full_name);
        $this->assertEquals($originalCode, $lead->lead_code);
    }

    // --- Static helper method tests ---

    public function test_generate_unique_lead_code_returns_valid_format(): void
    {
        $code = CampaignLead::generateUniqueLeadCode();

        $this->assertMatchesRegularExpression('/^LD-[A-Z0-9]{8}$/', $code);
    }

    public function test_generate_unique_lead_code_produces_unique_codes(): void
    {
        $codes = [];
        for ($i = 0; $i < 10; $i++) {
            $codes[] = CampaignLead::generateUniqueLeadCode();
        }

        $this->assertCount(10, array_unique($codes));
    }
}
