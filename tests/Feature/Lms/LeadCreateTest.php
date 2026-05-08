<?php

namespace Tests\Feature\Lms;

use App\Models\CampaignLead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Feature tests for the LMS Lead Creation.
 *
 * Validates: Requirements 8.1, 8.2, 8.3, 8.4
 */
class LeadCreateTest extends TestCase
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
            'message' => 'Interested in astrology consultation',
            'source' => 'website',
        ], $overrides);
    }

    // =========================================================================
    // Successful Creation Tests (Requirement 8.1)
    // =========================================================================

    /**
     * Test successful lead creation with all valid data.
     *
     * Validates: Requirement 8.1
     */
    public function test_successful_creation_with_valid_data(): void
    {
        $data = $this->validLeadData();

        $response = $this->actingAs($this->user)->post('/lms/leads', $data);

        $lead = CampaignLead::where('email', 'john@example.com')->first();

        $this->assertNotNull($lead);
        $this->assertEquals('John Doe', $lead->full_name);
        $this->assertEquals('john@example.com', $lead->email);
        $this->assertEquals('+919876543210', $lead->phone_number);
        $this->assertEquals('1990-05-15', $lead->date_of_birth->format('Y-m-d'));
        $this->assertEquals('Mumbai', $lead->place_of_birth);
        $this->assertEquals('Interested in astrology consultation', $lead->message);
        $this->assertEquals('website', $lead->source);

        $response->assertRedirect(route('lms.leads.show', $lead));
    }

    /**
     * Test successful creation with required fields and minimal optional data.
     *
     * Validates: Requirement 8.1, 8.2
     */
    public function test_successful_creation_with_required_fields_and_minimal_data(): void
    {
        $data = [
            'full_name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'phone_number' => '+911234567890',
            'date_of_birth' => '1985-03-20',
            'place_of_birth' => 'Delhi',
        ];

        $response = $this->actingAs($this->user)->post('/lms/leads', $data);

        $lead = CampaignLead::where('email', 'jane@example.com')->first();

        $this->assertNotNull($lead);
        $this->assertEquals('Jane Smith', $lead->full_name);
        $this->assertEquals('jane@example.com', $lead->email);
        $this->assertEquals('+911234567890', $lead->phone_number);
        $this->assertEquals('1985-03-20', $lead->date_of_birth->format('Y-m-d'));
        $this->assertEquals('Delhi', $lead->place_of_birth);
        $this->assertNull($lead->message);

        $response->assertRedirect(route('lms.leads.show', $lead));
    }

    // =========================================================================
    // Default Status Tests (Requirement 8.1)
    // =========================================================================

    /**
     * Test that a newly created lead has status 'new' by default.
     *
     * Validates: Requirement 8.1
     */
    public function test_default_status_is_new(): void
    {
        $data = $this->validLeadData();

        $this->actingAs($this->user)->post('/lms/leads', $data);

        $lead = CampaignLead::where('email', 'john@example.com')->first();

        $this->assertNotNull($lead);
        $this->assertEquals('new', $lead->status);
    }

    // =========================================================================
    // Required Field Validation Tests (Requirement 8.2)
    // =========================================================================

    /**
     * Test validation error when full_name is missing.
     *
     * Validates: Requirement 8.2
     */
    public function test_validation_error_when_full_name_is_missing(): void
    {
        $data = $this->validLeadData(['full_name' => '']);

        $response = $this->actingAs($this->user)->post('/lms/leads', $data);

        $response->assertSessionHasErrors('full_name');
        $this->assertDatabaseMissing('campaign_leads', ['email' => 'john@example.com']);
    }

    /**
     * Test validation error when email is missing.
     *
     * Validates: Requirement 8.2
     */
    public function test_validation_error_when_email_is_missing(): void
    {
        $data = $this->validLeadData(['email' => '']);

        $response = $this->actingAs($this->user)->post('/lms/leads', $data);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseMissing('campaign_leads', ['full_name' => 'John Doe', 'phone_number' => '+919876543210']);
    }

    /**
     * Test validation error when phone_number is missing.
     *
     * Validates: Requirement 8.2
     */
    public function test_validation_error_when_phone_number_is_missing(): void
    {
        $data = $this->validLeadData(['phone_number' => '']);

        $response = $this->actingAs($this->user)->post('/lms/leads', $data);

        $response->assertSessionHasErrors('phone_number');
        $this->assertDatabaseMissing('campaign_leads', ['email' => 'john@example.com']);
    }

    // =========================================================================
    // Email Validation Tests (Requirement 8.3)
    // =========================================================================

    /**
     * Test validation error for invalid email format.
     *
     * Validates: Requirement 8.3
     */
    public function test_validation_error_for_invalid_email_format(): void
    {
        $data = $this->validLeadData(['email' => 'not-an-email']);

        $response = $this->actingAs($this->user)->post('/lms/leads', $data);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseMissing('campaign_leads', ['full_name' => 'John Doe']);
    }

    /**
     * Test validation error for email without domain.
     *
     * Validates: Requirement 8.3
     */
    public function test_validation_error_for_email_without_domain(): void
    {
        $data = $this->validLeadData(['email' => 'user@']);

        $response = $this->actingAs($this->user)->post('/lms/leads', $data);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test validation error for email without at symbol.
     *
     * Validates: Requirement 8.3
     */
    public function test_validation_error_for_email_without_at_symbol(): void
    {
        $data = $this->validLeadData(['email' => 'userexample.com']);

        $response = $this->actingAs($this->user)->post('/lms/leads', $data);

        $response->assertSessionHasErrors('email');
    }

    // =========================================================================
    // Phone Number Validation Tests (Requirement 8.4)
    // =========================================================================

    /**
     * Test validation error for invalid phone number format.
     *
     * Validates: Requirement 8.4
     */
    public function test_validation_error_for_invalid_phone_number_format(): void
    {
        $data = $this->validLeadData(['phone_number' => 'abc123']);

        $response = $this->actingAs($this->user)->post('/lms/leads', $data);

        $response->assertSessionHasErrors('phone_number');
        $this->assertDatabaseMissing('campaign_leads', ['email' => 'john@example.com']);
    }

    /**
     * Test validation error for phone number too short.
     *
     * Validates: Requirement 8.4
     */
    public function test_validation_error_for_phone_number_too_short(): void
    {
        $data = $this->validLeadData(['phone_number' => '12345']);

        $response = $this->actingAs($this->user)->post('/lms/leads', $data);

        $response->assertSessionHasErrors('phone_number');
    }

    /**
     * Test validation error for phone number too long.
     *
     * Validates: Requirement 8.4
     */
    public function test_validation_error_for_phone_number_too_long(): void
    {
        $data = $this->validLeadData(['phone_number' => '+1234567890123456789012345']);

        $response = $this->actingAs($this->user)->post('/lms/leads', $data);

        $response->assertSessionHasErrors('phone_number');
    }

    /**
     * Test validation error for phone number with invalid characters.
     *
     * Validates: Requirement 8.4
     */
    public function test_validation_error_for_phone_number_with_invalid_characters(): void
    {
        $data = $this->validLeadData(['phone_number' => '+91-abc-defgh']);

        $response = $this->actingAs($this->user)->post('/lms/leads', $data);

        $response->assertSessionHasErrors('phone_number');
    }

    /**
     * Test valid phone number formats are accepted.
     *
     * Validates: Requirement 8.4
     */
    public function test_valid_phone_number_formats_are_accepted(): void
    {
        $validPhones = [
            '+919876543210',
            '9876543210',
            '+1 (555) 123-4567',
            '(555) 123-4567',
            '+44 20 7946 0958',
        ];

        foreach ($validPhones as $phone) {
            $data = $this->validLeadData([
                'phone_number' => $phone,
                'email' => fake()->unique()->safeEmail(),
            ]);

            $response = $this->actingAs($this->user)->post('/lms/leads', $data);

            $response->assertSessionDoesntHaveErrors('phone_number');
        }
    }
}
