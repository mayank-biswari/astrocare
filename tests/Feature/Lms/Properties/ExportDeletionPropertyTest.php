<?php

namespace Tests\Feature\Lms\Properties;

use App\Models\CampaignLead;
use App\Models\LeadFollowUp;
use App\Models\LeadNote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Property-based tests for LMS CSV export filter consistency and cascade deletion.
 *
 * Uses Faker to generate random sets of leads, notes, and follow-ups,
 * then verifies export and deletion behavior matches expectations.
 *
 * **Validates: Requirements 9.1, 10.2**
 */
class ExportDeletionPropertyTest extends TestCase
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
     * Property 19: CSV export filter consistency
     *
     * For any set of filter criteria applied to the lead list, the CSV export
     * SHALL contain exactly the same set of leads that the filtered list query
     * returns — no more, no less.
     *
     * **Validates: Requirements 9.1**
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function property_csv_export_filter_consistency(): void
    {
        $faker = \Faker\Factory::create();
        $statuses = ['new', 'contacted', 'qualified', 'converted', 'lost'];
        $sources = ['website', 'referral', 'campaign', 'social-media', 'phone'];

        for ($iteration = 0; $iteration < 100; $iteration++) {
            // Clean up from previous iteration
            CampaignLead::query()->delete();

            // Generate random leads (5 to 20)
            $numLeads = $faker->numberBetween(5, 20);

            for ($i = 0; $i < $numLeads; $i++) {
                CampaignLead::create([
                    'full_name' => $faker->name(),
                    'date_of_birth' => $faker->date(),
                    'place_of_birth' => $faker->city(),
                    'phone_number' => '+91' . $faker->numerify('##########'),
                    'email' => $faker->unique()->safeEmail(),
                    'source' => $faker->randomElement($sources),
                    'status' => $faker->randomElement($statuses),
                    'message' => $faker->optional(0.5)->sentence(),
                ]);
            }

            // Choose a random filter type to apply
            $filterType = $faker->randomElement(['status', 'source', 'search', 'none']);
            $queryParams = [];

            switch ($filterType) {
                case 'status':
                    $queryParams['status'] = $faker->randomElement($statuses);
                    break;
                case 'source':
                    $queryParams['source'] = $faker->randomElement($sources);
                    break;
                case 'search':
                    // Pick a random lead's name fragment as search term
                    $randomLead = CampaignLead::inRandomOrder()->first();
                    $nameParts = explode(' ', $randomLead->full_name);
                    $queryParams['search'] = $nameParts[0];
                    break;
                case 'none':
                    // No filter — export all
                    break;
            }

            // Get the filtered leads from the database using the same scopes
            $expectedLeads = CampaignLead::query()
                ->search($queryParams['search'] ?? null)
                ->filterStatus($queryParams['status'] ?? null)
                ->filterSource($queryParams['source'] ?? null)
                ->filterDateRange($queryParams['date_from'] ?? null, $queryParams['date_to'] ?? null)
                ->get();

            // If no leads match, the export should redirect back
            if ($expectedLeads->isEmpty()) {
                $response = $this->actingAs($this->user)->get('/lms/export?' . http_build_query($queryParams));
                $response->assertRedirect();
                $faker->unique(true);
                continue;
            }

            // Get the CSV export response
            $response = $this->actingAs($this->user)->get('/lms/export?' . http_build_query($queryParams));
            $response->assertStatus(200);
            $this->assertStringStartsWith('text/csv', $response->headers->get('Content-Type'));

            // Parse the CSV content
            $csvContent = $response->streamedContent();
            $lines = array_filter(explode("\n", trim($csvContent)));
            $header = str_getcsv(array_shift($lines));

            $csvLeadEmails = [];
            foreach ($lines as $line) {
                $row = str_getcsv($line);
                if (count($row) >= 2) {
                    // email is the second column
                    $csvLeadEmails[] = $row[1];
                }
            }

            $expectedEmails = $expectedLeads->pluck('email')->sort()->values()->toArray();
            sort($csvLeadEmails);

            $this->assertEquals(
                $expectedEmails,
                $csvLeadEmails,
                sprintf(
                    'Iteration %d: CSV export does not match filtered leads. '
                    . 'Filter: %s=%s. Expected %d leads, got %d in CSV.',
                    $iteration + 1,
                    $filterType,
                    json_encode($queryParams),
                    count($expectedEmails),
                    count($csvLeadEmails)
                )
            );

            $faker->unique(true);
        }
    }

    /**
     * Property 20: Cascade deletion
     *
     * For any lead with N associated notes and M associated follow-ups,
     * deleting the lead SHALL remove the lead record, all N notes, and all M
     * follow-ups from the database.
     *
     * **Validates: Requirements 10.2**
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function property_cascade_deletion(): void
    {
        $faker = \Faker\Factory::create();

        for ($iteration = 0; $iteration < 100; $iteration++) {
            // Clean up from previous iteration
            LeadFollowUp::query()->delete();
            LeadNote::query()->delete();
            CampaignLead::query()->delete();

            // Create a lead
            $lead = CampaignLead::create([
                'full_name' => $faker->name(),
                'date_of_birth' => $faker->date(),
                'place_of_birth' => $faker->city(),
                'phone_number' => '+91' . $faker->numerify('##########'),
                'email' => $faker->unique()->safeEmail(),
                'source' => $faker->randomElement(['website', 'referral', 'campaign']),
                'status' => $faker->randomElement(['new', 'contacted', 'qualified', 'converted', 'lost']),
            ]);

            // Create N random notes (0 to 10)
            $numNotes = $faker->numberBetween(0, 10);
            $noteIds = [];
            for ($i = 0; $i < $numNotes; $i++) {
                $note = LeadNote::create([
                    'campaign_lead_id' => $lead->id,
                    'user_id' => $this->user->id,
                    'body' => $faker->paragraph(),
                ]);
                $noteIds[] = $note->id;
            }

            // Create M random follow-ups (0 to 10)
            $numFollowUps = $faker->numberBetween(0, 10);
            $followUpIds = [];
            for ($i = 0; $i < $numFollowUps; $i++) {
                $followUp = LeadFollowUp::create([
                    'campaign_lead_id' => $lead->id,
                    'user_id' => $this->user->id,
                    'description' => $faker->sentence(),
                    'scheduled_date' => $faker->dateTimeBetween('+1 day', '+30 days')->format('Y-m-d'),
                    'completed_at' => $faker->optional(0.3)->dateTime(),
                ]);
                $followUpIds[] = $followUp->id;
            }

            // Verify the records exist before deletion
            $this->assertDatabaseHas('campaign_leads', ['id' => $lead->id]);
            $this->assertEquals($numNotes, LeadNote::where('campaign_lead_id', $lead->id)->count());
            $this->assertEquals($numFollowUps, LeadFollowUp::where('campaign_lead_id', $lead->id)->count());

            // Delete the lead via the controller
            $response = $this->actingAs($this->user)->delete('/lms/leads/' . $lead->id);
            $response->assertRedirect(route('lms.leads.index'));

            // Verify the lead is removed
            $this->assertDatabaseMissing('campaign_leads', ['id' => $lead->id]);

            // Verify all N notes are removed
            foreach ($noteIds as $noteId) {
                $this->assertDatabaseMissing('lead_notes', ['id' => $noteId]);
            }

            // Verify all M follow-ups are removed
            foreach ($followUpIds as $followUpId) {
                $this->assertDatabaseMissing('lead_follow_ups', ['id' => $followUpId]);
            }

            // Also verify counts are zero for this lead
            $this->assertEquals(0, LeadNote::where('campaign_lead_id', $lead->id)->count(),
                sprintf(
                    'Iteration %d: Expected 0 notes after deletion, but found some. '
                    . 'Lead had %d notes and %d follow-ups.',
                    $iteration + 1,
                    $numNotes,
                    $numFollowUps
                )
            );
            $this->assertEquals(0, LeadFollowUp::where('campaign_lead_id', $lead->id)->count(),
                sprintf(
                    'Iteration %d: Expected 0 follow-ups after deletion, but found some. '
                    . 'Lead had %d notes and %d follow-ups.',
                    $iteration + 1,
                    $numNotes,
                    $numFollowUps
                )
            );

            $faker->unique(true);
        }
    }
}
