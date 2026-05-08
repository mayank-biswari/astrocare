<?php

namespace Tests\Feature\Lms\Properties;

use App\Models\CampaignLead;
use App\Models\LeadNote;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Property-based tests for LMS Notes and Follow-Ups.
 *
 * Uses Faker to generate random data (100 iterations per property),
 * then verifies system behavior matches expectations.
 *
 * **Validates: Requirements 4.3, 6.1, 6.2, 6.3, 7.4**
 */
class NotesFollowUpsPropertyTest extends TestCase
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
     * Property 10: Notes reverse chronological ordering
     *
     * For any lead with N notes (N >= 2), the notes displayed on the lead detail
     * page SHALL be ordered by created_at descending (most recent first).
     *
     * **Validates: Requirements 4.3, 6.2**
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function property_notes_reverse_chronological_ordering(): void
    {
        $faker = \Faker\Factory::create();

        for ($iteration = 0; $iteration < 100; $iteration++) {
            // Clean up from previous iteration
            LeadNote::query()->delete();
            CampaignLead::query()->delete();

            // Create a lead
            $lead = CampaignLead::create([
                'full_name' => $faker->name(),
                'date_of_birth' => $faker->date(),
                'place_of_birth' => $faker->city(),
                'phone_number' => '+91' . $faker->numerify('##########'),
                'email' => $faker->unique()->safeEmail(),
                'source' => 'website',
                'status' => 'new',
            ]);

            // Create N notes (N between 2 and 10) with random timestamps
            $numNotes = $faker->numberBetween(2, 10);
            for ($i = 0; $i < $numNotes; $i++) {
                $note = $lead->notes()->create([
                    'user_id' => $this->user->id,
                    'body' => $faker->sentence(),
                ]);

                // Assign a random timestamp spread across different times
                $randomTimestamp = now()->subMinutes($faker->numberBetween(1, 10000));
                LeadNote::where('id', $note->id)->update(['created_at' => $randomTimestamp]);
            }

            // Visit the lead detail page
            $response = $this->actingAs($this->user)->get(route('lms.leads.show', $lead));
            $response->assertStatus(200);

            $viewLead = $response->viewData('lead');
            $notes = $viewLead->notes;

            // Verify we have the expected number of notes
            $this->assertCount(
                $numNotes,
                $notes,
                sprintf('Iteration %d: Expected %d notes, got %d', $iteration + 1, $numNotes, $notes->count())
            );

            // Verify notes are in descending created_at order
            $previousCreatedAt = null;
            foreach ($notes as $note) {
                if ($previousCreatedAt !== null) {
                    $this->assertTrue(
                        $note->created_at <= $previousCreatedAt,
                        sprintf(
                            'Iteration %d: Notes not in descending created_at order. '
                            . '%s should be <= %s',
                            $iteration + 1,
                            $note->created_at->toDateTimeString(),
                            $previousCreatedAt->toDateTimeString()
                        )
                    );
                }
                $previousCreatedAt = $note->created_at;
            }

            $faker->unique(true);
        }
    }

    /**
     * Property 14: Note storage integrity
     *
     * For any valid note body submitted for a lead, the stored note SHALL have
     * the correct campaign_lead_id, user_id matching the authenticated user,
     * the submitted body, and a non-null created_at timestamp.
     *
     * **Validates: Requirements 6.1**
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function property_note_storage_integrity(): void
    {
        $faker = \Faker\Factory::create();

        for ($iteration = 0; $iteration < 100; $iteration++) {
            // Clean up from previous iteration
            LeadNote::query()->delete();
            CampaignLead::query()->delete();

            // Create a lead
            $lead = CampaignLead::create([
                'full_name' => $faker->name(),
                'date_of_birth' => $faker->date(),
                'place_of_birth' => $faker->city(),
                'phone_number' => '+91' . $faker->numerify('##########'),
                'email' => $faker->unique()->safeEmail(),
                'source' => 'website',
                'status' => 'new',
            ]);

            // Generate a random valid note body (non-empty, non-whitespace-only)
            $noteBody = $faker->sentence($faker->numberBetween(3, 20));

            // Submit the note via the controller
            $response = $this->actingAs($this->user)->post(
                route('lms.leads.notes.store', $lead),
                ['body' => $noteBody]
            );

            $response->assertRedirect(route('lms.leads.show', $lead));

            // Verify the stored note
            $storedNote = LeadNote::where('campaign_lead_id', $lead->id)->latest()->first();

            $this->assertNotNull(
                $storedNote,
                sprintf('Iteration %d: No note was stored', $iteration + 1)
            );

            $this->assertEquals(
                $lead->id,
                $storedNote->campaign_lead_id,
                sprintf('Iteration %d: campaign_lead_id mismatch', $iteration + 1)
            );

            $this->assertEquals(
                $this->user->id,
                $storedNote->user_id,
                sprintf('Iteration %d: user_id mismatch. Expected %d, got %d', $iteration + 1, $this->user->id, $storedNote->user_id)
            );

            $this->assertEquals(
                $noteBody,
                $storedNote->body,
                sprintf('Iteration %d: body mismatch. Expected "%s", got "%s"', $iteration + 1, $noteBody, $storedNote->body)
            );

            $this->assertNotNull(
                $storedNote->created_at,
                sprintf('Iteration %d: created_at is null', $iteration + 1)
            );

            $faker->unique(true);
        }
    }

    /**
     * Property 15: Empty note rejection
     *
     * For any string composed entirely of whitespace characters (including the
     * empty string), submitting it as a note body SHALL be rejected with a
     * validation error.
     *
     * **Validates: Requirements 6.3**
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function property_empty_note_rejection(): void
    {
        $faker = \Faker\Factory::create();

        // Create a lead once (reuse across iterations)
        $lead = CampaignLead::create([
            'full_name' => $faker->name(),
            'date_of_birth' => $faker->date(),
            'place_of_birth' => $faker->city(),
            'phone_number' => '+91' . $faker->numerify('##########'),
            'email' => $faker->safeEmail(),
            'source' => 'website',
            'status' => 'new',
        ]);

        $initialNoteCount = LeadNote::count();

        for ($iteration = 0; $iteration < 100; $iteration++) {
            // Generate various whitespace-only strings
            $whitespaceChars = [' ', "\t", "\n", "\r", "\r\n", '  ', "\t\t"];
            $numChars = $faker->numberBetween(0, 5);

            if ($numChars === 0) {
                $whitespaceBody = '';
            } else {
                $whitespaceBody = '';
                for ($i = 0; $i < $numChars; $i++) {
                    $whitespaceBody .= $faker->randomElement($whitespaceChars);
                }
            }

            // Submit the whitespace-only note
            $response = $this->actingAs($this->user)->post(
                route('lms.leads.notes.store', $lead),
                ['body' => $whitespaceBody]
            );

            // Should be rejected with validation error (redirect back with errors)
            $response->assertSessionHasErrors('body');

            // Verify no note was created
            $this->assertEquals(
                $initialNoteCount,
                LeadNote::count(),
                sprintf(
                    'Iteration %d: A note was created despite whitespace-only body "%s"',
                    $iteration + 1,
                    addcslashes($whitespaceBody, "\t\n\r")
                )
            );
        }
    }

    /**
     * Property 16: Follow-up past date rejection
     *
     * For any date strictly before today, attempting to create a follow-up with
     * that scheduled_date SHALL be rejected with a validation error.
     *
     * **Validates: Requirements 7.4**
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function property_follow_up_past_date_rejection(): void
    {
        $faker = \Faker\Factory::create();

        // Freeze time to avoid boundary issues during test execution
        $frozenNow = Carbon::create(2026, 6, 15, 12, 0, 0);
        Carbon::setTestNow($frozenNow);

        // Create a lead once (reuse across iterations)
        $lead = CampaignLead::create([
            'full_name' => $faker->name(),
            'date_of_birth' => $faker->date(),
            'place_of_birth' => $faker->city(),
            'phone_number' => '+91' . $faker->numerify('##########'),
            'email' => $faker->safeEmail(),
            'source' => 'website',
            'status' => 'new',
        ]);

        for ($iteration = 0; $iteration < 100; $iteration++) {
            // Generate a random past date (1 to 1000 days before today)
            $daysInPast = $faker->numberBetween(1, 1000);
            $pastDate = $frozenNow->copy()->subDays($daysInPast)->toDateString();

            // Attempt to create a follow-up with the past date
            $response = $this->actingAs($this->user)->post(
                route('lms.leads.follow-ups.store', $lead),
                [
                    'description' => $faker->sentence(),
                    'scheduled_date' => $pastDate,
                ]
            );

            // Should be rejected with validation error on scheduled_date
            $response->assertSessionHasErrors('scheduled_date');
        }

        Carbon::setTestNow(); // Unfreeze time
    }
}
