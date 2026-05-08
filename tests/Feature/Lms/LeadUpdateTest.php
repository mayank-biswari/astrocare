<?php

namespace Tests\Feature\Lms;

use App\Models\CampaignLead;
use App\Models\LeadNote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Feature tests for the LMS Lead Update.
 *
 * Validates: Requirements 4.1
 */
class LeadUpdateTest extends TestCase
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
            'full_name' => 'Original Name',
            'email' => 'original@example.com',
            'phone_number' => '+919876543210',
            'date_of_birth' => '1990-01-01',
            'place_of_birth' => 'Mumbai',
            'message' => 'Original message',
            'source' => 'website',
            'status' => 'new',
        ]);
    }

    private function validUpdateData(array $overrides = []): array
    {
        return array_merge([
            'full_name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone_number' => '+911234567890',
            'date_of_birth' => '1985-06-15',
            'place_of_birth' => 'Delhi',
            'message' => 'Updated message',
            'source' => 'referral',
        ], $overrides);
    }

    // =========================================================================
    // Successful Update Tests (Requirement 4.1)
    // =========================================================================

    /**
     * Test successful lead update with valid data.
     *
     * Validates: Requirement 4.1
     */
    public function test_successful_update_with_valid_data(): void
    {
        $data = $this->validUpdateData();

        $response = $this->actingAs($this->user)
            ->put("/lms/leads/{$this->lead->id}", $data);

        $response->assertRedirect(route('lms.leads.show', $this->lead));

        $this->lead->refresh();
        $this->assertEquals('Updated Name', $this->lead->full_name);
        $this->assertEquals('updated@example.com', $this->lead->email);
        $this->assertEquals('+911234567890', $this->lead->phone_number);
        $this->assertEquals('1985-06-15', $this->lead->date_of_birth->format('Y-m-d'));
        $this->assertEquals('Delhi', $this->lead->place_of_birth);
        $this->assertEquals('Updated message', $this->lead->message);
        $this->assertEquals('referral', $this->lead->source);
    }

    /**
     * Test update does not change the lead status.
     *
     * Validates: Requirement 4.1
     */
    public function test_update_does_not_change_status(): void
    {
        $this->lead->update(['status' => 'contacted']);

        $data = $this->validUpdateData();

        $this->actingAs($this->user)
            ->put("/lms/leads/{$this->lead->id}", $data);

        $this->lead->refresh();
        $this->assertEquals('contacted', $this->lead->status);
    }

    /**
     * Test update with only required fields.
     *
     * Validates: Requirement 4.1
     */
    public function test_update_with_only_required_fields(): void
    {
        $data = [
            'full_name' => 'Minimal Update',
            'email' => 'minimal@example.com',
            'phone_number' => '+919999999999',
        ];

        $response = $this->actingAs($this->user)
            ->put("/lms/leads/{$this->lead->id}", $data);

        $response->assertRedirect(route('lms.leads.show', $this->lead));

        $this->lead->refresh();
        $this->assertEquals('Minimal Update', $this->lead->full_name);
        $this->assertEquals('minimal@example.com', $this->lead->email);
        $this->assertEquals('+919999999999', $this->lead->phone_number);
    }

    // =========================================================================
    // Validation Error Tests (Requirement 4.1)
    // =========================================================================

    /**
     * Test validation error when full_name is missing on update.
     */
    public function test_validation_error_when_full_name_is_missing(): void
    {
        $data = $this->validUpdateData(['full_name' => '']);

        $response = $this->actingAs($this->user)
            ->put("/lms/leads/{$this->lead->id}", $data);

        $response->assertSessionHasErrors('full_name');

        $this->lead->refresh();
        $this->assertEquals('Original Name', $this->lead->full_name);
    }

    /**
     * Test validation error when email is missing on update.
     */
    public function test_validation_error_when_email_is_missing(): void
    {
        $data = $this->validUpdateData(['email' => '']);

        $response = $this->actingAs($this->user)
            ->put("/lms/leads/{$this->lead->id}", $data);

        $response->assertSessionHasErrors('email');

        $this->lead->refresh();
        $this->assertEquals('original@example.com', $this->lead->email);
    }

    /**
     * Test validation error when phone_number is missing on update.
     */
    public function test_validation_error_when_phone_number_is_missing(): void
    {
        $data = $this->validUpdateData(['phone_number' => '']);

        $response = $this->actingAs($this->user)
            ->put("/lms/leads/{$this->lead->id}", $data);

        $response->assertSessionHasErrors('phone_number');

        $this->lead->refresh();
        $this->assertEquals('+919876543210', $this->lead->phone_number);
    }

    /**
     * Test validation error for invalid email format on update.
     */
    public function test_validation_error_for_invalid_email_format(): void
    {
        $data = $this->validUpdateData(['email' => 'not-an-email']);

        $response = $this->actingAs($this->user)
            ->put("/lms/leads/{$this->lead->id}", $data);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test validation error for invalid phone number format on update.
     */
    public function test_validation_error_for_invalid_phone_number_format(): void
    {
        $data = $this->validUpdateData(['phone_number' => 'abc123']);

        $response = $this->actingAs($this->user)
            ->put("/lms/leads/{$this->lead->id}", $data);

        $response->assertSessionHasErrors('phone_number');
    }
}
