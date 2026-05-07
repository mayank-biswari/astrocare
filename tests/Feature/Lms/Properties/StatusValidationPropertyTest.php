<?php

namespace Tests\Feature\Lms\Properties;

use App\Models\CampaignLead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Property-based tests for LMS status validation.
 *
 * Uses Faker to generate random invalid status values and terminal status scenarios,
 * then verifies validation rules are enforced correctly.
 *
 * **Validates: Requirements 5.2, 5.4**
 */
class StatusValidationPropertyTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected CampaignLead $lead;

    private const VALID_STATUSES = ['new', 'contacted', 'qualified', 'converted', 'lost'];

    protected function setUp(): void
    {
        parent::setUp();

        Permission::firstOrCreate(['name' => 'access lms', 'guard_name' => 'web']);

        $this->user = User::factory()->create();
        $this->user->givePermissionTo('access lms');

        $this->lead = CampaignLead::create([
            'full_name' => 'Test Lead',
            'date_of_birth' => '1990-01-01',
            'place_of_birth' => 'Test City',
            'phone_number' => '+919876543210',
            'email' => 'test@example.com',
            'source' => 'website',
            'status' => 'new',
        ]);
    }

    /**
     * Property 12: Valid status values enforcement
     *
     * For any string value not in the set {new, contacted, qualified, converted, lost},
     * attempting to set a lead's status to that value SHALL be rejected with a validation error.
     *
     * **Validates: Requirements 5.2**
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function property_invalid_status_values_are_rejected(): void
    {
        $faker = \Faker\Factory::create();

        for ($iteration = 0; $iteration < 100; $iteration++) {
            // Generate a random string that is NOT in the valid statuses set
            $invalidStatus = $this->generateInvalidStatus($faker);

            $response = $this->actingAs($this->user)->put(
                route('lms.leads.status', $this->lead),
                ['status' => $invalidStatus]
            );

            $response->assertSessionHasErrors('status', sprintf(
                'Iteration %d: Invalid status "%s" was not rejected with a validation error.',
                $iteration + 1,
                $invalidStatus
            ));

            // Verify the lead's status was NOT changed
            $this->lead->refresh();
            $this->assertEquals(
                'new',
                $this->lead->status,
                sprintf(
                    'Iteration %d: Lead status was changed to "%s" despite invalid value "%s".',
                    $iteration + 1,
                    $this->lead->status,
                    $invalidStatus
                )
            );
        }
    }

    /**
     * Property 13: Terminal status requires note
     *
     * For any status change to "converted" or "lost", the request SHALL be rejected
     * if no note body is provided, and accepted if a non-empty note body is provided.
     *
     * **Validates: Requirements 5.4**
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function property_terminal_status_requires_note(): void
    {
        $faker = \Faker\Factory::create();
        $terminalStatuses = ['converted', 'lost'];

        for ($iteration = 0; $iteration < 100; $iteration++) {
            // Reset lead status to a non-terminal state for each iteration
            $this->lead->update(['status' => 'contacted']);

            // Randomly choose a terminal status
            $terminalStatus = $faker->randomElement($terminalStatuses);

            // Test 1: Without note → should be rejected
            $response = $this->actingAs($this->user)->put(
                route('lms.leads.status', $this->lead),
                ['status' => $terminalStatus]
            );

            $response->assertSessionHasErrors('note', sprintf(
                'Iteration %d: Terminal status "%s" without note was not rejected.',
                $iteration + 1,
                $terminalStatus
            ));

            // Verify status was NOT changed
            $this->lead->refresh();
            $this->assertEquals(
                'contacted',
                $this->lead->status,
                sprintf(
                    'Iteration %d: Lead status changed to "%s" without a note.',
                    $iteration + 1,
                    $terminalStatus
                )
            );

            // Test 2: With non-empty note → should be accepted
            $noteBody = $faker->sentence($faker->numberBetween(3, 20));
            $noteCountBefore = $this->lead->notes()->count();

            $response = $this->actingAs($this->user)->put(
                route('lms.leads.status', $this->lead),
                [
                    'status' => $terminalStatus,
                    'note' => $noteBody,
                ]
            );

            $response->assertSessionHasNoErrors();
            $response->assertRedirect(route('lms.leads.show', $this->lead));

            // Verify status WAS changed
            $this->lead->refresh();
            $this->assertEquals(
                $terminalStatus,
                $this->lead->status,
                sprintf(
                    'Iteration %d: Lead status was not updated to "%s" despite providing a note.',
                    $iteration + 1,
                    $terminalStatus
                )
            );

            // Verify the note was created
            $noteCountAfter = $this->lead->notes()->count();
            $this->assertEquals(
                $noteCountBefore + 1,
                $noteCountAfter,
                sprintf(
                    'Iteration %d: Note was not created for terminal status "%s".',
                    $iteration + 1,
                    $terminalStatus
                )
            );

            // Get the most recently created note (by id, since it's auto-incrementing)
            $latestNote = $this->lead->notes()->orderBy('id', 'desc')->first();
            $this->assertEquals($noteBody, $latestNote->body, sprintf(
                'Iteration %d: Note body does not match. Expected "%s", got "%s".',
                $iteration + 1,
                $noteBody,
                $latestNote->body
            ));
        }
    }

    /**
     * Generate a random string that is guaranteed NOT to be a valid status.
     */
    private function generateInvalidStatus(\Faker\Generator $faker): string
    {
        $strategies = [
            // Random word
            fn () => $faker->word(),
            // Random alphanumeric string
            fn () => $faker->lexify('??????'),
            // Numeric string
            fn () => (string) $faker->numberBetween(0, 9999),
            // Valid status with extra characters
            fn () => $faker->randomElement(self::VALID_STATUSES) . $faker->randomLetter(),
            // Uppercase version of valid status
            fn () => strtoupper($faker->randomElement(self::VALID_STATUSES)),
            // Mixed case
            fn () => ucfirst($faker->randomElement(self::VALID_STATUSES)),
            // Empty-ish strings
            fn () => $faker->randomElement(['', ' ', 'pending', 'active', 'closed', 'archived', 'deleted']),
            // Random sentence fragment
            fn () => $faker->words($faker->numberBetween(1, 3), true),
        ];

        // Keep generating until we get something NOT in the valid set
        do {
            $strategy = $faker->randomElement($strategies);
            $invalidStatus = $strategy();
        } while (in_array($invalidStatus, self::VALID_STATUSES, true));

        return $invalidStatus;
    }
}
