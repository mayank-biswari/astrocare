<?php

namespace Tests\Feature\Lms\Properties;

use App\Models\CampaignLead;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Property-based tests for LMS Lead Listing, Search, Filter, and Sort.
 *
 * Uses Faker to generate random sets of leads (100 iterations per property),
 * then verifies listing behavior matches expectations.
 *
 * **Validates: Requirements 3.1, 3.2, 3.3, 3.4, 3.5, 3.7**
 */
class LeadListPropertyTest extends TestCase
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
     * Property 5: Pagination correctness
     *
     * For any set of N leads in the database, the lead list SHALL return at most
     * 20 leads per page, and the total number of pages SHALL equal ceil(N / 20).
     *
     * **Validates: Requirements 3.1**
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function property_pagination_correctness(): void
    {
        $faker = \Faker\Factory::create();

        for ($iteration = 0; $iteration < 100; $iteration++) {
            CampaignLead::query()->delete();

            // Generate a random number of leads (1 to 50)
            $numLeads = $faker->numberBetween(1, 50);

            for ($i = 0; $i < $numLeads; $i++) {
                CampaignLead::create([
                    'full_name' => $faker->name(),
                    'date_of_birth' => $faker->date(),
                    'place_of_birth' => $faker->city(),
                    'phone_number' => '+91' . $faker->numerify('##########'),
                    'email' => $faker->unique()->safeEmail(),
                    'source' => $faker->randomElement(['website', 'referral', 'campaign']),
                    'status' => $faker->randomElement(['new', 'contacted', 'qualified', 'converted', 'lost']),
                ]);
            }

            $response = $this->actingAs($this->user)->get('/lms/leads');
            $response->assertStatus(200);

            $leads = $response->viewData('leads');

            $expectedPerPage = min($numLeads, 20);
            $expectedLastPage = (int) ceil($numLeads / 20);

            $this->assertCount(
                $expectedPerPage,
                $leads,
                sprintf(
                    'Iteration %d: Expected %d leads on first page (N=%d), got %d',
                    $iteration + 1,
                    $expectedPerPage,
                    $numLeads,
                    $leads->count()
                )
            );

            $this->assertEquals(
                $expectedLastPage,
                $leads->lastPage(),
                sprintf(
                    'Iteration %d: Expected %d total pages for %d leads, got %d',
                    $iteration + 1,
                    $expectedLastPage,
                    $numLeads,
                    $leads->lastPage()
                )
            );

            $this->assertEquals(
                $numLeads,
                $leads->total(),
                sprintf(
                    'Iteration %d: Expected total of %d leads, got %d',
                    $iteration + 1,
                    $numLeads,
                    $leads->total()
                )
            );

            $faker->unique(true);
        }
    }

    /**
     * Property 6: Search returns only matching leads
     *
     * For any search term and set of leads, all leads returned by the search SHALL
     * contain the search term in at least one of: full_name, email, or phone_number.
     * Additionally, no lead that contains the term in any of those fields SHALL be
     * excluded from results.
     *
     * **Validates: Requirements 3.2**
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function property_search_returns_only_matching_leads(): void
    {
        $faker = \Faker\Factory::create();

        for ($iteration = 0; $iteration < 100; $iteration++) {
            CampaignLead::query()->delete();

            // Generate a short search term (3-5 lowercase chars) that we'll embed in some leads
            $searchTerm = $faker->lexify('???');

            // Generate leads (5 to 20)
            $numLeads = $faker->numberBetween(5, 20);
            $expectedMatchIds = [];

            for ($i = 0; $i < $numLeads; $i++) {
                // Randomly decide which field (if any) will contain the search term
                $embedIn = $faker->randomElement(['full_name', 'email', 'phone_number', 'none']);

                $fullName = $faker->name();
                $email = $faker->unique()->safeEmail();
                $phone = '+91' . $faker->numerify('##########');

                if ($embedIn === 'full_name') {
                    $fullName = $faker->firstName() . ' ' . $searchTerm . $faker->lastName();
                } elseif ($embedIn === 'email') {
                    $email = $searchTerm . $faker->numberBetween(1, 9999) . '@example.com';
                } elseif ($embedIn === 'phone_number') {
                    $phone = '+91' . $searchTerm . $faker->numerify('#######');
                }

                $lead = CampaignLead::create([
                    'full_name' => $fullName,
                    'date_of_birth' => $faker->date(),
                    'place_of_birth' => $faker->city(),
                    'phone_number' => $phone,
                    'email' => $email,
                    'source' => 'website',
                    'status' => 'new',
                ]);

                // Determine if this lead should match the search
                $matchesName = stripos($lead->full_name, $searchTerm) !== false;
                $matchesEmail = stripos($lead->email, $searchTerm) !== false;
                $matchesPhone = stripos($lead->phone_number, $searchTerm) !== false;

                if ($matchesName || $matchesEmail || $matchesPhone) {
                    $expectedMatchIds[] = $lead->id;
                }
            }

            $response = $this->actingAs($this->user)->get('/lms/leads?search=' . urlencode($searchTerm));
            $response->assertStatus(200);

            $leads = $response->viewData('leads');

            // All returned leads must contain the search term in at least one field
            foreach ($leads as $lead) {
                $matchesName = stripos($lead->full_name, $searchTerm) !== false;
                $matchesEmail = stripos($lead->email, $searchTerm) !== false;
                $matchesPhone = stripos($lead->phone_number, $searchTerm) !== false;

                $this->assertTrue(
                    $matchesName || $matchesEmail || $matchesPhone,
                    sprintf(
                        'Iteration %d: Lead "%s" (email: %s, phone: %s) returned by search "%s" but does not contain the term in any searchable field',
                        $iteration + 1,
                        $lead->full_name,
                        $lead->email,
                        $lead->phone_number,
                        $searchTerm
                    )
                );
            }

            // No matching lead should be excluded (accounting for pagination - all should fit in one page for <= 20 leads)
            $returnedIds = $leads->pluck('id')->toArray();
            foreach ($expectedMatchIds as $expectedId) {
                $this->assertContains(
                    $expectedId,
                    $returnedIds,
                    sprintf(
                        'Iteration %d: Lead ID %d matches search term "%s" but was excluded from results',
                        $iteration + 1,
                        $expectedId,
                        $searchTerm
                    )
                );
            }

            $faker->unique(true);
        }
    }

    /**
     * Property 7: Filter correctness
     *
     * For any filter (status or source) applied to the lead list, all returned leads
     * SHALL have the filtered field matching the filter value, and no lead matching
     * the filter SHALL be excluded from results.
     *
     * **Validates: Requirements 3.3, 3.4**
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function property_filter_correctness(): void
    {
        $faker = \Faker\Factory::create();
        $statuses = ['new', 'contacted', 'qualified', 'converted', 'lost'];
        $sources = ['website', 'referral', 'campaign', 'social', 'phone'];

        for ($iteration = 0; $iteration < 100; $iteration++) {
            CampaignLead::query()->delete();

            // Randomly choose to test status or source filter
            $filterType = $faker->randomElement(['status', 'source']);
            $filterValue = $filterType === 'status'
                ? $faker->randomElement($statuses)
                : $faker->randomElement($sources);

            // Generate leads (5 to 20)
            $numLeads = $faker->numberBetween(5, 20);
            $expectedMatchIds = [];

            for ($i = 0; $i < $numLeads; $i++) {
                $status = $faker->randomElement($statuses);
                $source = $faker->randomElement($sources);

                $lead = CampaignLead::create([
                    'full_name' => $faker->name(),
                    'date_of_birth' => $faker->date(),
                    'place_of_birth' => $faker->city(),
                    'phone_number' => '+91' . $faker->numerify('##########'),
                    'email' => $faker->unique()->safeEmail(),
                    'source' => $source,
                    'status' => $status,
                ]);

                if ($filterType === 'status' && $lead->status === $filterValue) {
                    $expectedMatchIds[] = $lead->id;
                } elseif ($filterType === 'source' && $lead->source === $filterValue) {
                    $expectedMatchIds[] = $lead->id;
                }
            }

            $response = $this->actingAs($this->user)->get("/lms/leads?{$filterType}={$filterValue}");
            $response->assertStatus(200);

            $leads = $response->viewData('leads');

            // All returned leads must have the filtered field matching the filter value
            foreach ($leads as $lead) {
                $this->assertEquals(
                    $filterValue,
                    $lead->{$filterType},
                    sprintf(
                        'Iteration %d: Lead has %s="%s" but filter was %s="%s"',
                        $iteration + 1,
                        $filterType,
                        $lead->{$filterType},
                        $filterType,
                        $filterValue
                    )
                );
            }

            // No matching lead should be excluded (within pagination limits)
            $returnedIds = $leads->pluck('id')->toArray();
            foreach ($expectedMatchIds as $expectedId) {
                $this->assertContains(
                    $expectedId,
                    $returnedIds,
                    sprintf(
                        'Iteration %d: Lead ID %d matches %s filter "%s" but was excluded from results',
                        $iteration + 1,
                        $expectedId,
                        $filterType,
                        $filterValue
                    )
                );
            }

            $faker->unique(true);
        }
    }

    /**
     * Property 8: Date range filter correctness
     *
     * For any date range [from, to] and set of leads, all returned leads SHALL have
     * created_at within the specified range (inclusive), and no lead within the range
     * SHALL be excluded.
     *
     * **Validates: Requirements 3.5**
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function property_date_range_filter_correctness(): void
    {
        $faker = \Faker\Factory::create();

        for ($iteration = 0; $iteration < 100; $iteration++) {
            CampaignLead::query()->delete();

            // Generate a random date range (within 2025)
            $rangeStartDay = $faker->numberBetween(1, 300);
            $rangeLength = $faker->numberBetween(1, 30);
            $dateFrom = Carbon::create(2025, 1, 1)->addDays($rangeStartDay);
            $dateTo = $dateFrom->copy()->addDays($rangeLength);

            $dateFromStr = $dateFrom->toDateString();
            $dateToStr = $dateTo->toDateString();

            // Generate leads (5 to 20) with random created_at dates
            $numLeads = $faker->numberBetween(5, 20);
            $expectedMatchIds = [];

            for ($i = 0; $i < $numLeads; $i++) {
                // Random date spread across a wider range
                $daysOffset = $faker->numberBetween(0, 365);
                $createdAt = Carbon::create(2025, 1, 1)->addDays($daysOffset)
                    ->addHours($faker->numberBetween(0, 23))
                    ->addMinutes($faker->numberBetween(0, 59));

                $lead = CampaignLead::create([
                    'full_name' => $faker->name(),
                    'date_of_birth' => $faker->date(),
                    'place_of_birth' => $faker->city(),
                    'phone_number' => '+91' . $faker->numerify('##########'),
                    'email' => $faker->unique()->safeEmail(),
                    'source' => 'website',
                    'status' => 'new',
                ]);

                CampaignLead::where('id', $lead->id)->update(['created_at' => $createdAt]);

                // Check if this lead falls within the date range (inclusive)
                // The filter uses: created_at >= date_from AND created_at <= date_to 23:59:59
                if ($createdAt->toDateString() >= $dateFromStr && $createdAt->toDateString() <= $dateToStr) {
                    $expectedMatchIds[] = $lead->id;
                }
            }

            $response = $this->actingAs($this->user)->get("/lms/leads?date_from={$dateFromStr}&date_to={$dateToStr}");
            $response->assertStatus(200);

            $leads = $response->viewData('leads');

            // All returned leads must have created_at within the range
            foreach ($leads as $lead) {
                $lead->refresh(); // Ensure we have the updated created_at
                $leadDate = $lead->created_at->toDateString();

                $this->assertTrue(
                    $leadDate >= $dateFromStr && $leadDate <= $dateToStr,
                    sprintf(
                        'Iteration %d: Lead created_at "%s" is outside range [%s, %s]',
                        $iteration + 1,
                        $lead->created_at->toDateTimeString(),
                        $dateFromStr,
                        $dateToStr
                    )
                );
            }

            // No matching lead should be excluded (within pagination - max 20 leads so all fit)
            $returnedIds = $leads->pluck('id')->toArray();
            foreach ($expectedMatchIds as $expectedId) {
                $this->assertContains(
                    $expectedId,
                    $returnedIds,
                    sprintf(
                        'Iteration %d: Lead ID %d is within date range [%s, %s] but was excluded from results',
                        $iteration + 1,
                        $expectedId,
                        $dateFromStr,
                        $dateToStr
                    )
                );
            }

            $faker->unique(true);
        }
    }

    /**
     * Property 9: Sort correctness
     *
     * For any sortable column and sort direction (asc/desc), the lead list SHALL
     * return leads ordered correctly by that column in the specified direction.
     *
     * **Validates: Requirements 3.7**
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function property_sort_correctness(): void
    {
        $faker = \Faker\Factory::create();
        $sortColumns = ['full_name', 'email', 'phone_number', 'source', 'status', 'created_at'];
        $directions = ['asc', 'desc'];

        for ($iteration = 0; $iteration < 100; $iteration++) {
            CampaignLead::query()->delete();

            $sortColumn = $faker->randomElement($sortColumns);
            $sortDir = $faker->randomElement($directions);

            // Generate leads (3 to 15) with distinct values for the sort column
            $numLeads = $faker->numberBetween(3, 15);

            for ($i = 0; $i < $numLeads; $i++) {
                $createdAt = Carbon::create(2025, 1, 1)
                    ->addDays($faker->numberBetween(0, 365))
                    ->addHours($faker->numberBetween(0, 23))
                    ->addMinutes($faker->numberBetween(0, 59))
                    ->addSeconds($faker->numberBetween(0, 59));

                $lead = CampaignLead::create([
                    'full_name' => $faker->name(),
                    'date_of_birth' => $faker->date(),
                    'place_of_birth' => $faker->city(),
                    'phone_number' => '+91' . $faker->numerify('##########'),
                    'email' => $faker->unique()->safeEmail(),
                    'source' => $faker->randomElement(['website', 'referral', 'campaign', 'social']),
                    'status' => $faker->randomElement(['new', 'contacted', 'qualified', 'converted', 'lost']),
                ]);

                CampaignLead::where('id', $lead->id)->update(['created_at' => $createdAt]);
            }

            $response = $this->actingAs($this->user)->get("/lms/leads?sort_by={$sortColumn}&sort_dir={$sortDir}");
            $response->assertStatus(200);

            $leads = $response->viewData('leads');

            // Verify the leads are sorted correctly
            $values = [];
            foreach ($leads as $lead) {
                $lead->refresh(); // Ensure we have the updated created_at
                if ($sortColumn === 'created_at') {
                    $values[] = $lead->created_at->toDateTimeString();
                } else {
                    $values[] = $lead->{$sortColumn};
                }
            }

            // Check that values are in the correct order
            for ($i = 1; $i < count($values); $i++) {
                if ($sortDir === 'asc') {
                    $this->assertTrue(
                        strcasecmp($values[$i], $values[$i - 1]) >= 0,
                        sprintf(
                            'Iteration %d: Sort by %s %s failed. Value "%s" at position %d should be >= "%s" at position %d',
                            $iteration + 1,
                            $sortColumn,
                            $sortDir,
                            $values[$i],
                            $i,
                            $values[$i - 1],
                            $i - 1
                        )
                    );
                } else {
                    $this->assertTrue(
                        strcasecmp($values[$i], $values[$i - 1]) <= 0,
                        sprintf(
                            'Iteration %d: Sort by %s %s failed. Value "%s" at position %d should be <= "%s" at position %d',
                            $iteration + 1,
                            $sortColumn,
                            $sortDir,
                            $values[$i],
                            $i,
                            $values[$i - 1],
                            $i - 1
                        )
                    );
                }
            }

            $faker->unique(true);
        }
    }
}
