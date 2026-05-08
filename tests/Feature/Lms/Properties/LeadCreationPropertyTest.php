<?php

namespace Tests\Feature\Lms\Properties;

use App\Models\CampaignLead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Property-based tests for LMS Lead Creation validation.
 *
 * Uses Faker to generate random valid/invalid lead data,
 * then verifies creation defaults and validation rules hold.
 *
 * **Validates: Requirements 8.1, 8.2, 8.3**
 */
class LeadCreationPropertyTest extends TestCase
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
     * Generate a valid phone number matching regex /^[+]?[\d\s\-()]{7,20}$/
     *
     * The regex allows: optional leading +, then 7-20 characters from [\d\s\-()]
     */
    private function generateValidPhone(\Faker\Generator $faker): string
    {
        // Use simple numeric formats that reliably match the regex
        $formats = [
            '+91' . $faker->numerify('##########'),       // +91XXXXXXXXXX (13 chars)
            $faker->numerify('##########'),               // XXXXXXXXXX (10 chars)
            '+1' . $faker->numerify('##########'),        // +1XXXXXXXXXX (12 chars)
            '+44' . $faker->numerify('#########'),        // +44XXXXXXXXX (12 chars)
            $faker->numerify('#######'),                  // XXXXXXX (7 chars - minimum)
            '+' . $faker->numerify('############'),       // +XXXXXXXXXXXX (13 chars)
        ];

        return $faker->randomElement($formats);
    }

    /**
     * Property 17: Lead creation defaults and validation
     *
     * For any valid lead data (non-empty full_name, valid email, valid phone_number),
     * creating a lead SHALL result in a record with status = 'new'.
     * For any submission missing full_name, email, or phone_number, creation SHALL be rejected.
     *
     * **Validates: Requirements 8.1, 8.2**
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function property_lead_creation_defaults_and_validation(): void
    {
        $faker = \Faker\Factory::create();

        for ($iteration = 0; $iteration < 100; $iteration++) {
            // --- Part A: Valid data creates lead with status 'new' ---
            $validData = [
                'full_name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'phone_number' => $this->generateValidPhone($faker),
                'date_of_birth' => $faker->date('Y-m-d', '-20 years'),
                'place_of_birth' => $faker->city(),
            ];

            // Optionally add other optional fields
            if ($faker->boolean(50)) {
                $validData['message'] = $faker->sentence();
            }
            if ($faker->boolean(50)) {
                $validData['source'] = $faker->randomElement(['website', 'referral', 'campaign', 'phone']);
            }

            $response = $this->actingAs($this->user)->post('/lms/leads', $validData);

            // Should redirect (successful creation)
            $response->assertSessionDoesntHaveErrors();
            $response->assertStatus(302);

            // Verify lead was created with status 'new'
            $lead = CampaignLead::where('email', $validData['email'])->first();
            $this->assertNotNull(
                $lead,
                sprintf(
                    'Iteration %d: Lead was not created with valid data. '
                    . 'Data: full_name=%s, email=%s, phone=%s',
                    $iteration + 1,
                    $validData['full_name'],
                    $validData['email'],
                    $validData['phone_number']
                )
            );
            $this->assertEquals(
                'new',
                $lead->status,
                sprintf(
                    'Iteration %d: Lead status should be "new" but got "%s". Email: %s',
                    $iteration + 1,
                    $lead->status,
                    $validData['email']
                )
            );

            // --- Part B: Missing a required field causes rejection ---
            $requiredFields = ['full_name', 'email', 'phone_number'];
            $fieldToRemove = $faker->randomElement($requiredFields);

            $invalidData = [
                'full_name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'phone_number' => $this->generateValidPhone($faker),
                'date_of_birth' => $faker->date('Y-m-d', '-20 years'),
                'place_of_birth' => $faker->city(),
            ];

            // Remove or empty the chosen required field
            $invalidData[$fieldToRemove] = '';

            $response = $this->actingAs($this->user)->post('/lms/leads', $invalidData);

            // Should have a validation error for the missing field
            $response->assertSessionHasErrors($fieldToRemove);

            // Verify no lead was created with this invalid email
            $emailToCheck = $fieldToRemove === 'email' ? '' : $invalidData['email'];
            if ($emailToCheck) {
                $this->assertDatabaseMissing('campaign_leads', [
                    'email' => $emailToCheck,
                ]);
            }
        }
    }

    /**
     * Property 18: Email format validation
     *
     * For any string that does not conform to a valid email format,
     * submitting it as the email field in lead creation SHALL be rejected
     * with a validation error.
     *
     * **Validates: Requirements 8.3**
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function property_email_format_validation(): void
    {
        $faker = \Faker\Factory::create();

        for ($iteration = 0; $iteration < 100; $iteration++) {
            // --- Part A: Invalid email strings are rejected ---
            $invalidEmailGenerators = [
                // No @ symbol
                fn () => $faker->word() . $faker->word() . '.com',
                // No domain after @
                fn () => $faker->word() . '@',
                // Spaces in local part
                fn () => $faker->word() . ' ' . $faker->word() . '@example.com',
                // Double @
                fn () => $faker->word() . '@@example.com',
                // No local part
                fn () => '@example.com',
                // Just a random word (no @ or domain)
                fn () => $faker->word(),
                // Dots only after @
                fn () => $faker->word() . '@...',
                // Numeric only (no @ symbol)
                fn () => (string) $faker->randomNumber(8),
                // Space only
                fn () => '   ',
                // Multiple @ symbols with content
                fn () => $faker->word() . '@' . $faker->word() . '@example.com',
            ];

            $generatorIndex = $iteration % count($invalidEmailGenerators);
            $invalidEmail = $invalidEmailGenerators[$generatorIndex]();

            $data = [
                'full_name' => $faker->name(),
                'email' => $invalidEmail,
                'phone_number' => $this->generateValidPhone($faker),
                'date_of_birth' => $faker->date('Y-m-d', '-20 years'),
                'place_of_birth' => $faker->city(),
            ];

            $response = $this->actingAs($this->user)->post('/lms/leads', $data);

            // Should have a validation error for email
            $response->assertSessionHasErrors(
                'email',
                sprintf(
                    'Iteration %d: Invalid email "%s" was not rejected.',
                    $iteration + 1,
                    $invalidEmail
                )
            );

            // --- Part B: Valid emails (Faker-generated) are accepted ---
            $validEmail = $faker->unique()->safeEmail();

            $validData = [
                'full_name' => $faker->name(),
                'email' => $validEmail,
                'phone_number' => $this->generateValidPhone($faker),
                'date_of_birth' => $faker->date('Y-m-d', '-20 years'),
                'place_of_birth' => $faker->city(),
            ];

            $response = $this->actingAs($this->user)->post('/lms/leads', $validData);

            // Should NOT have a validation error for email
            $response->assertSessionDoesntHaveErrors('email');
            $response->assertStatus(302);

            // Verify lead was created
            $this->assertDatabaseHas('campaign_leads', [
                'email' => $validEmail,
            ]);
        }
    }
}
