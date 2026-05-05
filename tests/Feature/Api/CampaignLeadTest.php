<?php

namespace Tests\Feature\Api;

use App\Models\CampaignLead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignLeadTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Valid payload for creating a campaign lead.
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'full_name' => 'Jane Doe',
            'date_of_birth' => '1990-05-15',
            'place_of_birth' => 'Mumbai',
            'phone_number' => '+91 98765 43210',
            'email' => 'jane@example.com',
            'message' => 'I would like a tarot reading.',
        ], $overrides);
    }

    /**
     * Validates: Requirements 4.1
     * Successful lead creation returns 201 with ID.
     */
    public function test_successful_lead_creation_returns_201_with_id(): void
    {
        $response = $this->postJson('/api/campaign-leads', $this->validPayload());

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'message'])
            ->assertJson(['message' => 'Your callback request has been received. We will contact you soon.']);

        $this->assertDatabaseHas('campaign_leads', [
            'full_name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'place_of_birth' => 'Mumbai',
        ]);
    }

    /**
     * Validates: Requirements 4.7
     * Source defaults to 'tarot-reading-campaign' when not provided.
     */
    public function test_source_defaults_to_tarot_reading_campaign(): void
    {
        $this->postJson('/api/campaign-leads', $this->validPayload());

        $lead = CampaignLead::first();

        $this->assertNotNull($lead);
        $this->assertEquals('tarot-reading-campaign', $lead->source);
    }

    /**
     * Validates: Requirements 4.2
     * Missing required fields return 422 with field-level error messages.
     */
    public function test_validation_errors_return_422_with_field_messages(): void
    {
        $response = $this->postJson('/api/campaign-leads', []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'full_name',
                    'date_of_birth',
                    'place_of_birth',
                    'phone_number',
                    'email',
                ],
            ]);
    }

    /**
     * Validates: Requirements 4.3
     * Full name must not exceed 255 characters.
     */
    public function test_full_name_max_length_validation(): void
    {
        $response = $this->postJson('/api/campaign-leads', $this->validPayload([
            'full_name' => str_repeat('A', 256),
        ]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['full_name']);
    }

    /**
     * Validates: Requirements 4.4
     * Date of birth must be a valid date in the past.
     */
    public function test_date_of_birth_must_be_in_the_past(): void
    {
        $response = $this->postJson('/api/campaign-leads', $this->validPayload([
            'date_of_birth' => now()->addDay()->toDateString(),
        ]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['date_of_birth']);
    }

    /**
     * Validates: Requirements 4.4
     * Date of birth must be a valid date format.
     */
    public function test_date_of_birth_must_be_a_valid_date(): void
    {
        $response = $this->postJson('/api/campaign-leads', $this->validPayload([
            'date_of_birth' => 'not-a-date',
        ]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['date_of_birth']);
    }

    /**
     * Validates: Requirements 4.5
     * Phone number must match the allowed format (digits, spaces, hyphens, parens, optional leading +).
     */
    public function test_phone_number_rejects_invalid_format(): void
    {
        $response = $this->postJson('/api/campaign-leads', $this->validPayload([
            'phone_number' => 'abc!@#invalid',
        ]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phone_number']);
    }

    /**
     * Validates: Requirements 4.5
     * Phone number must be at least 7 characters.
     */
    public function test_phone_number_minimum_length(): void
    {
        $response = $this->postJson('/api/campaign-leads', $this->validPayload([
            'phone_number' => '12345',
        ]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phone_number']);
    }

    /**
     * Validates: Requirements 4.5
     * Phone number must not exceed 20 characters.
     */
    public function test_phone_number_maximum_length(): void
    {
        $response = $this->postJson('/api/campaign-leads', $this->validPayload([
            'phone_number' => '+1 234 567 890 123 456 789',
        ]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phone_number']);
    }

    /**
     * Validates: Requirements 4.6
     * Email must be a valid email format.
     */
    public function test_email_must_be_valid_format(): void
    {
        $response = $this->postJson('/api/campaign-leads', $this->validPayload([
            'email' => 'not-an-email',
        ]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Validates: Requirements 4.6
     * Email must not exceed 255 characters.
     */
    public function test_email_max_length_validation(): void
    {
        $response = $this->postJson('/api/campaign-leads', $this->validPayload([
            'email' => str_repeat('a', 247) . '@test.com',
        ]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Validates: Requirements 4.1
     * Message field is optional — lead can be created without it.
     */
    public function test_message_is_optional(): void
    {
        $payload = $this->validPayload();
        unset($payload['message']);

        $response = $this->postJson('/api/campaign-leads', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('campaign_leads', [
            'email' => 'jane@example.com',
            'message' => null,
        ]);
    }

    /**
     * Validates: Requirements 4.1
     * Message must not exceed 1000 characters when provided.
     */
    public function test_message_max_length_validation(): void
    {
        $response = $this->postJson('/api/campaign-leads', $this->validPayload([
            'message' => str_repeat('A', 1001),
        ]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['message']);
    }
}
