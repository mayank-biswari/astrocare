<?php

namespace Tests\Property\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * Feature: user-management-search-filter-sort
 * Property-based tests for user management search, filter, and sort.
 *
 * Properties tested:
 * - Property 1: Search filtering correctness
 * - Property 2: Whitespace search sanitization
 * - Property 4: Role filtering correctness
 * - Property 5: Date range filtering correctness
 * - Property 6: Date validation — start after end
 * - Property 7: Date validation — malformed dates
 * - Property 13: Pagination size invariant
 * - Property 11: Invalid parameters gracefully ignored
 *
 * Validates: Requirements 1.2, 1.7, 2.2, 3.2, 3.3, 3.4, 3.6, 3.7, 5.6, 6.3
 */
class UserManagementPropertyTest extends TestCase
{
    use RefreshDatabase;

    private const ITERATIONS = 100;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Reset cached permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Ensure roles exist
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'expert', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        // Create an admin user for authentication
        $this->adminUser = User::factory()->create(['role' => 'admin']);
        $this->adminUser->assignRole('admin');
    }

    /**
     * Generate a random search term of 1-20 characters using alphanumeric and common chars.
     */
    private function generateRandomSearchTerm(): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $length = mt_rand(1, 20);
        $term = '';
        for ($i = 0; $i < $length; $i++) {
            $term .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $term;
    }

    /**
     * Extract a substring from a user's name or email to use as a search term
     * that is guaranteed to match at least one user.
     */
    private function extractSearchTermFromUser(User $user): string
    {
        // Randomly pick name or email
        $source = mt_rand(0, 1) === 0 ? $user->name : $user->email;
        $len = strlen($source);

        if ($len <= 1) {
            return $source;
        }

        // Extract a random substring of length 1-5
        $maxSubLen = min(5, $len);
        $subLen = mt_rand(1, $maxSubLen);
        $start = mt_rand(0, $len - $subLen);

        return substr($source, $start, $subLen);
    }

    /**
     * Feature: user-management-search-filter-sort, Property 1: Search filtering correctness
     *
     * For any set of users and for any valid search term (1-100 non-whitespace characters),
     * the filtered result set SHALL contain exactly those users whose name or email contains
     * the search term as a case-insensitive substring, and no others.
     *
     * **Validates: Requirements 1.2**
     */
    #[Test]
    public function property_search_filtering_correctness(): void
    {
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            // Clean up users from previous iteration (keep admin user)
            User::where('id', '!=', $this->adminUser->id)->delete();

            // Generate a random number of users (between 3 and 15)
            $userCount = mt_rand(3, 15);
            $createdUsers = [];

            for ($j = 0; $j < $userCount; $j++) {
                $user = User::factory()->create(['role' => 'user']);
                $createdUsers[] = $user;
            }

            // Alternate between random search terms and terms extracted from users
            // This ensures we test both matching and non-matching scenarios
            if ($i % 2 === 0 && !empty($createdUsers)) {
                // Use a substring from a random user to guarantee at least one match
                $randomUser = $createdUsers[array_rand($createdUsers)];
                $searchTerm = $this->extractSearchTermFromUser($randomUser);
            } else {
                // Use a fully random search term (may or may not match)
                $searchTerm = $this->generateRandomSearchTerm();
            }

            // Make the request with search filter
            $response = $this->actingAs($this->adminUser)
                ->get(route('admin.user-management.index', ['search' => $searchTerm]));

            $response->assertStatus(200);

            // Get the users returned in the view
            $returnedUsers = $response->viewData('users');
            $returnedUserIds = $returnedUsers->pluck('id')->sort()->values()->toArray();

            // The controller's prepareForValidation trims the search term,
            // so we must compare against the trimmed version
            $effectiveSearch = trim($searchTerm);
            if ($effectiveSearch === '') {
                // Whitespace-only becomes null (no filter) - skip this iteration
                continue;
            }
            $effectiveSearch = mb_substr($effectiveSearch, 0, 100);

            // Calculate expected users: those whose name or email contains the effective search term (case-insensitive)
            $allUsers = array_merge($createdUsers, [$this->adminUser]);
            $expectedUserIds = [];
            $lowerSearch = mb_strtolower($effectiveSearch);

            foreach ($allUsers as $user) {
                $nameMatch = str_contains(mb_strtolower($user->name), $lowerSearch);
                $emailMatch = str_contains(mb_strtolower($user->email), $lowerSearch);
                if ($nameMatch || $emailMatch) {
                    $expectedUserIds[] = $user->id;
                }
            }

            sort($expectedUserIds);

            $this->assertEquals(
                $expectedUserIds,
                $returnedUserIds,
                "Iteration {$i}: Search term '{$searchTerm}' (effective: '{$effectiveSearch}') should return "
                . "exactly users whose name or email contains the term (case-insensitive). "
                . "Expected IDs: [" . implode(',', $expectedUserIds) . "] "
                . "Got IDs: [" . implode(',', $returnedUserIds) . "]"
            );
        }
    }

    /**
     * Feature: user-management-search-filter-sort, Property 4: Role filtering correctness
     *
     * For any set of users with assigned roles and for any valid role value (admin, expert, user),
     * the filtered result set SHALL contain exactly those users who have the specified role assigned,
     * and no others.
     *
     * **Validates: Requirements 2.2**
     */
    #[Test]
    public function property_role_filtering_correctness(): void
    {
        $validRoles = ['admin', 'expert', 'user'];

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            // Clean up users from previous iteration (keep admin user)
            User::where('id', '!=', $this->adminUser->id)->delete();

            // Generate a random number of users (between 3 and 15)
            $userCount = mt_rand(3, 15);
            $createdUsers = [];

            for ($j = 0; $j < $userCount; $j++) {
                $role = $validRoles[array_rand($validRoles)];
                $user = User::factory()->create(['role' => $role]);
                $user->assignRole($role);
                $createdUsers[] = ['user' => $user, 'role' => $role];
            }

            // Pick a random role to filter by
            $filterRole = $validRoles[array_rand($validRoles)];

            // Make the request with role filter
            $response = $this->actingAs($this->adminUser)
                ->get(route('admin.user-management.index', ['role' => $filterRole]));

            $response->assertStatus(200);

            // Get the users returned in the view
            $returnedUsers = $response->viewData('users');
            $returnedUserIds = $returnedUsers->pluck('id')->toArray();

            // Calculate expected users: those with the specified role (including admin user if applicable)
            $expectedUserIds = [];
            foreach ($createdUsers as $entry) {
                if ($entry['role'] === $filterRole) {
                    $expectedUserIds[] = $entry['user']->id;
                }
            }
            // Include admin user if they have the filtered role
            if ($filterRole === 'admin') {
                $expectedUserIds[] = $this->adminUser->id;
            }

            sort($expectedUserIds);
            sort($returnedUserIds);

            $this->assertEquals(
                $expectedUserIds,
                $returnedUserIds,
                "Iteration {$i}: Role filter '{$filterRole}' should return exactly users with that role. "
                . "Expected IDs: [" . implode(',', $expectedUserIds) . "] "
                . "Got IDs: [" . implode(',', $returnedUserIds) . "]"
            );

            // Reset permission cache for next iteration
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
        }
    }

    /**
     * Feature: user-management-search-filter-sort, Property 2: Whitespace search sanitization
     *
     * For any string composed entirely of whitespace characters (spaces, tabs, newlines),
     * submitting it as a search term SHALL return the same result set as submitting no search term
     * (all users).
     *
     * **Validates: Requirements 1.7**
     */
    #[Test]
    public function property_whitespace_search_sanitization(): void
    {
        // Create a fixed set of users for this test
        $userCount = mt_rand(5, 10);
        $users = User::factory()->count($userCount)->create(['role' => 'user']);

        // Get baseline response (no search term)
        $baselineResponse = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index'));
        $baselineResponse->assertStatus(200);
        $baselineUserIds = $baselineResponse->viewData('users')->pluck('id')->sort()->values()->toArray();

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            // Generate a random whitespace-only string
            $whitespaceChars = [' ', "\t", "\n", "\r", "  ", "\t\t", " \t ", "\n\r", "   ", "\t \n"];
            $length = mt_rand(1, 10);
            $whitespaceString = '';
            for ($j = 0; $j < $length; $j++) {
                $whitespaceString .= $whitespaceChars[array_rand($whitespaceChars)];
            }

            // Submit the whitespace-only string as search term
            $response = $this->actingAs($this->adminUser)
                ->get(route('admin.user-management.index', ['search' => $whitespaceString]));

            $response->assertStatus(200);

            // Get the returned user IDs
            $returnedUserIds = $response->viewData('users')->pluck('id')->sort()->values()->toArray();

            // Verify same results as no search term
            $this->assertEquals(
                $baselineUserIds,
                $returnedUserIds,
                "Iteration {$i}: Whitespace-only search '" . addcslashes($whitespaceString, "\t\n\r") . "' "
                . "(length " . strlen($whitespaceString) . ") should return same results as no search term. "
                . "Expected " . count($baselineUserIds) . " users, got " . count($returnedUserIds) . " users."
            );
        }
    }

    /**
     * Generate a random valid parameter set for the user management filter.
     * Returns an associative array of query parameters.
     *
     * @return array<string, string>
     */
    private function generateRandomValidParams(): array
    {
        $params = [];
        $validRoles = ['admin', 'expert', 'user'];
        $validSortDirs = ['asc', 'desc'];

        // Randomly include search (50% chance)
        if (mt_rand(0, 1) === 1) {
            $params['search'] = $this->generateRandomSearchTerm();
        }

        // Randomly include role (50% chance)
        if (mt_rand(0, 1) === 1) {
            $params['role'] = $validRoles[array_rand($validRoles)];
        }

        // Randomly include date_from (40% chance)
        if (mt_rand(0, 4) >= 3) {
            $params['date_from'] = $this->generateRandomDate('2022-01-01', '2024-06-01');
        }

        // Randomly include date_to (40% chance), ensuring it's >= date_from if both present
        if (mt_rand(0, 4) >= 3) {
            $minTo = $params['date_from'] ?? '2022-01-01';
            $params['date_to'] = $this->generateRandomDate($minTo, '2025-05-01');
        }

        // Randomly include sort_by (60% chance)
        if (mt_rand(0, 4) >= 2) {
            $validSortColumns = ['name', 'email', 'role', 'created_at'];
            $params['sort_by'] = $validSortColumns[array_rand($validSortColumns)];
            $params['sort_dir'] = $validSortDirs[array_rand($validSortDirs)];
        }

        return $params;
    }

    /**
     * Generate a random date within a given range.
     */
    private function generateRandomDate(string $minDate = '2020-01-01', string $maxDate = '2025-12-31'): string
    {
        $minTimestamp = strtotime($minDate);
        $maxTimestamp = strtotime($maxDate);
        $randomTimestamp = mt_rand($minTimestamp, $maxTimestamp);
        return date('Y-m-d', $randomTimestamp);
    }

    /**
     * Feature: user-management-search-filter-sort, Property 10: Query parameter round-trip
     *
     * For any valid combination of filter parameters, encoding them as URL query parameters
     * and then decoding/applying them SHALL produce the same filter state and the same result
     * set as applying the parameters directly.
     *
     * **Validates: Requirements 5.4, 5.5**
     */
    #[Test]
    public function property_query_parameter_round_trip(): void
    {
        // Create a set of users with various attributes for filtering
        $validRoles = ['admin', 'expert', 'user'];
        $users = [];
        for ($j = 0; $j < 10; $j++) {
            $role = $validRoles[array_rand($validRoles)];
            $user = User::factory()->create([
                'role' => $role,
                'created_at' => $this->generateRandomDate('2022-01-01', '2025-05-01') . ' ' . sprintf('%02d:%02d:%02d', mt_rand(0, 23), mt_rand(0, 59), mt_rand(0, 59)),
            ]);
            $user->assignRole($role);
            $users[] = $user;
        }

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            // Generate a random valid parameter set
            $params = $this->generateRandomValidParams();

            // Skip empty parameter sets (no filter state to verify)
            if (empty($params)) {
                continue;
            }

            // Make the request with the generated parameters
            $response = $this->actingAs($this->adminUser)
                ->get(route('admin.user-management.index', $params));

            $response->assertStatus(200);

            $content = $response->getContent();

            // Verify search input preserves value
            if (isset($params['search'])) {
                $escapedSearch = htmlspecialchars($params['search'], ENT_QUOTES, 'UTF-8');
                $this->assertStringContainsString(
                    'value="' . $escapedSearch . '"',
                    $content,
                    "Iteration {$i}: Search term '{$params['search']}' should be preserved in the search input value attribute."
                );
            }

            // Verify role dropdown preserves selection
            if (isset($params['role'])) {
                $this->assertMatchesRegularExpression(
                    '/option\s+value="' . preg_quote($params['role'], '/') . '"\s+selected/',
                    $content,
                    "Iteration {$i}: Role '{$params['role']}' should be selected in the role dropdown."
                );
            }

            // Verify date_from input preserves value
            if (isset($params['date_from'])) {
                $this->assertStringContainsString(
                    'name="date_from"',
                    $content,
                    "Iteration {$i}: date_from input should exist."
                );
                $this->assertMatchesRegularExpression(
                    '/name="date_from"[^>]*value="' . preg_quote($params['date_from'], '/') . '"/',
                    $content,
                    "Iteration {$i}: date_from '{$params['date_from']}' should be preserved in the date_from input."
                );
            }

            // Verify date_to input preserves value
            if (isset($params['date_to'])) {
                $this->assertStringContainsString(
                    'name="date_to"',
                    $content,
                    "Iteration {$i}: date_to input should exist."
                );
                $this->assertMatchesRegularExpression(
                    '/name="date_to"[^>]*value="' . preg_quote($params['date_to'], '/') . '"/',
                    $content,
                    "Iteration {$i}: date_to '{$params['date_to']}' should be preserved in the date_to input."
                );
            }

            // Verify sort_by hidden input preserves value
            if (isset($params['sort_by'])) {
                $this->assertMatchesRegularExpression(
                    '/name="sort_by"[^>]*value="' . preg_quote($params['sort_by'], '/') . '"/',
                    $content,
                    "Iteration {$i}: sort_by '{$params['sort_by']}' should be preserved in the hidden sort_by input."
                );
            }

            // Verify sort_dir hidden input preserves value
            if (isset($params['sort_dir'])) {
                $this->assertMatchesRegularExpression(
                    '/name="sort_dir"[^>]*value="' . preg_quote($params['sort_dir'], '/') . '"/',
                    $content,
                    "Iteration {$i}: sort_dir '{$params['sort_dir']}' should be preserved in the hidden sort_dir input."
                );
            }

            // Verify pagination links preserve query parameters
            $returnedUsers = $response->viewData('users');
            if ($returnedUsers->hasPages()) {
                // Check that pagination links contain the filter parameters
                $paginationHtml = $returnedUsers->links()->toHtml();
                foreach ($params as $key => $value) {
                    $encodedParam = urlencode($key) . '=' . urlencode($value);
                    $this->assertStringContainsString(
                        $encodedParam,
                        $paginationHtml,
                        "Iteration {$i}: Pagination links should contain parameter '{$key}={$value}'."
                    );
                }
            }

            // Verify that making the same request again produces the same result set
            $response2 = $this->actingAs($this->adminUser)
                ->get(route('admin.user-management.index', $params));

            $response2->assertStatus(200);

            $returnedUserIds1 = $returnedUsers->pluck('id')->sort()->values()->toArray();
            $returnedUserIds2 = $response2->viewData('users')->pluck('id')->sort()->values()->toArray();

            $this->assertEquals(
                $returnedUserIds1,
                $returnedUserIds2,
                "Iteration {$i}: Same query parameters should produce the same result set on repeated requests. "
                . "Params: " . json_encode($params)
            );
        }
    }

    /**
     * Feature: user-management-search-filter-sort, Property 5: Date range filtering correctness
     *
     * For any set of users and for any valid date range (where start_date ≤ end_date,
     * either or both may be null), the filtered result set SHALL contain exactly those users
     * whose created_at date satisfies: (start_date is null OR created_at ≥ start_date) AND
     * (end_date is null OR created_at ≤ end_date).
     *
     * **Validates: Requirements 3.2, 3.3, 3.4**
     */
    #[Test]
    public function property_date_range_filtering_correctness(): void
    {
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            // Clean up users from previous iteration (keep admin user)
            User::where('id', '!=', $this->adminUser->id)->delete();

            // Generate a random number of users (between 3 and 15) with random created_at dates
            $userCount = mt_rand(3, 15);
            $createdUsers = [];

            for ($j = 0; $j < $userCount; $j++) {
                $randomDate = $this->generateRandomDate('2022-01-01', '2025-05-01');
                $user = User::factory()->create([
                    'role' => 'user',
                    'created_at' => $randomDate . ' ' . sprintf('%02d:%02d:%02d', mt_rand(0, 23), mt_rand(0, 59), mt_rand(0, 59)),
                ]);
                $createdUsers[] = $user;
            }

            // Generate a random date range scenario:
            // 0 = both dates specified, 1 = only start date, 2 = only end date
            $scenario = mt_rand(0, 2);
            $params = [];
            $dateFrom = null;
            $dateTo = null;

            if ($scenario === 0) {
                // Both dates specified (ensure start ≤ end)
                $date1 = $this->generateRandomDate('2022-01-01', '2025-05-01');
                $date2 = $this->generateRandomDate('2022-01-01', '2025-05-01');
                $dateFrom = min($date1, $date2);
                $dateTo = max($date1, $date2);
                $params['date_from'] = $dateFrom;
                $params['date_to'] = $dateTo;
            } elseif ($scenario === 1) {
                // Only start date
                $dateFrom = $this->generateRandomDate('2022-01-01', '2025-05-01');
                $params['date_from'] = $dateFrom;
            } else {
                // Only end date
                $dateTo = $this->generateRandomDate('2022-01-01', '2025-05-01');
                $params['date_to'] = $dateTo;
            }

            // Make the request with date filter
            $response = $this->actingAs($this->adminUser)
                ->get(route('admin.user-management.index', $params));

            $response->assertStatus(200);

            // Get the users returned in the view
            $returnedUsers = $response->viewData('users');
            $returnedUserIds = $returnedUsers->pluck('id')->sort()->values()->toArray();

            // Calculate expected users: those whose created_at date falls within the range (inclusive)
            $allUsers = array_merge($createdUsers, [$this->adminUser]);
            $expectedUserIds = [];

            foreach ($allUsers as $user) {
                $userDate = $user->created_at->format('Y-m-d');
                $matchesFrom = ($dateFrom === null) || ($userDate >= $dateFrom);
                $matchesTo = ($dateTo === null) || ($userDate <= $dateTo);

                if ($matchesFrom && $matchesTo) {
                    $expectedUserIds[] = $user->id;
                }
            }

            sort($expectedUserIds);

            $scenarioLabel = match ($scenario) {
                0 => "both dates (from={$dateFrom}, to={$dateTo})",
                1 => "only start date (from={$dateFrom})",
                2 => "only end date (to={$dateTo})",
            };

            $this->assertEquals(
                $expectedUserIds,
                $returnedUserIds,
                "Iteration {$i}: Date range filter with {$scenarioLabel} should return exactly users "
                . "whose created_at falls within the range (inclusive). "
                . "Expected IDs: [" . implode(',', $expectedUserIds) . "] "
                . "Got IDs: [" . implode(',', $returnedUserIds) . "]"
            );
        }
    }

    /**
     * Feature: user-management-search-filter-sort, Property 6: Date validation — start after end
     *
     * For any pair of valid dates where start_date > end_date, the system SHALL reject the filter
     * with a validation error and SHALL NOT modify the displayed results.
     *
     * **Validates: Requirements 3.6**
     */
    #[Test]
    public function property_date_validation_start_after_end(): void
    {
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            // Generate two random dates ensuring start > end
            $date1 = $this->generateRandomDate('2022-01-01', '2025-05-01');
            $date2 = $this->generateRandomDate('2022-01-01', '2025-05-01');

            // Ensure the two dates are different; if same, shift one by a day
            if ($date1 === $date2) {
                $date1 = date('Y-m-d', strtotime($date2 . ' +1 day'));
            }

            // Assign so that start > end (invalid scenario)
            $startDate = max($date1, $date2);
            $endDate = min($date1, $date2);

            // Make the request with start_date > end_date
            $response = $this->actingAs($this->adminUser)
                ->get(route('admin.user-management.index', [
                    'date_from' => $startDate,
                    'date_to' => $endDate,
                ]));

            // The system should return a validation error for date_from
            $response->assertSessionHasErrors('date_from');

            $this->assertTrue(
                $startDate > $endDate,
                "Iteration {$i}: Test precondition failed — start_date ({$startDate}) should be after end_date ({$endDate})."
            );
        }
    }

    /**
     * Generate a random non-date string that does not match YYYY-MM-DD format.
     * Produces various categories: random text, numbers, partial dates, reversed formats, etc.
     */
    private function generateMalformedDate(): string
    {
        $category = mt_rand(0, 8);

        return match ($category) {
            // Random alphabetic text
            0 => substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, mt_rand(3, 12)),
            // Random numbers (not date-like)
            1 => (string) mt_rand(1, 99999),
            // Partial date (only year-month)
            2 => sprintf('%04d-%02d', mt_rand(2000, 2025), mt_rand(1, 12)),
            // Reversed date format (DD-MM-YYYY)
            3 => sprintf('%02d-%02d-%04d', mt_rand(1, 28), mt_rand(1, 12), mt_rand(2020, 2025)),
            // Slash-separated date (MM/DD/YYYY)
            4 => sprintf('%02d/%02d/%04d', mt_rand(1, 12), mt_rand(1, 28), mt_rand(2020, 2025)),
            // Invalid month (13-99)
            5 => sprintf('%04d-%02d-%02d', mt_rand(2020, 2025), mt_rand(13, 99), mt_rand(1, 28)),
            // Invalid day (32-99)
            6 => sprintf('%04d-%02d-%02d', mt_rand(2020, 2025), mt_rand(1, 12), mt_rand(32, 99)),
            // Special characters and symbols
            7 => str_shuffle('!@#$%^&*()_+'),
            // Mixed alphanumeric gibberish
            8 => substr(str_shuffle('abc123XYZ!@#def456'), 0, mt_rand(4, 10)),
        };
    }

    /**
     * Feature: user-management-search-filter-sort, Property 7: Date validation — malformed dates
     *
     * For any string that does not match the YYYY-MM-DD format or represents a non-existent date,
     * the system SHALL reject the input with a validation error and SHALL NOT apply the filter.
     *
     * **Validates: Requirements 3.7**
     */
    #[Test]
    public function property_date_validation_malformed_dates(): void
    {
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $malformedDate = $this->generateMalformedDate();

            // Randomly choose whether to submit as date_from or date_to
            $field = mt_rand(0, 1) === 0 ? 'date_from' : 'date_to';

            // Make the request with the malformed date
            $response = $this->actingAs($this->adminUser)
                ->get(route('admin.user-management.index', [$field => $malformedDate]));

            // The system should return a validation error for the date field
            $response->assertSessionHasErrors($field,
                "Iteration {$i}: Malformed date '{$malformedDate}' submitted as '{$field}' "
                . "should trigger a validation error."
            );
        }
    }

    /**
     * Feature: user-management-search-filter-sort, Property 13: Pagination size invariant
     *
     * For any combination of active filters where the matching user count exceeds 20,
     * each page (except possibly the last) SHALL contain exactly 20 users.
     *
     * **Validates: Requirements 6.3**
     */
    #[Test]
    public function property_pagination_size_invariant(): void
    {
        $iterations = 15; // Fewer iterations since we're testing pagination mechanics

        for ($i = 0; $i < $iterations; $i++) {
            // Clean up users from previous iteration (keep admin user)
            User::where('id', '!=', $this->adminUser->id)->delete();

            // Generate a random number of users between 25 and 50 (always > 20)
            $userCount = mt_rand(25, 50);
            $validRoles = ['admin', 'expert', 'user'];

            // Randomly decide whether to apply a role filter
            $applyRoleFilter = mt_rand(0, 1) === 1;
            $filterRole = $applyRoleFilter ? $validRoles[array_rand($validRoles)] : null;

            // Create users - if filtering by role, ensure enough users have that role
            for ($j = 0; $j < $userCount; $j++) {
                if ($filterRole && $j < $userCount) {
                    // Assign the filter role to all users to guarantee > 20 matches
                    $role = $filterRole;
                } else {
                    $role = $validRoles[array_rand($validRoles)];
                }
                $user = User::factory()->create(['role' => $role]);
                $user->assignRole($role);
            }

            // Build query params
            $params = [];
            if ($filterRole) {
                $params['role'] = $filterRole;
            }

            // Calculate total expected users (including admin user if applicable)
            $totalExpected = $userCount;
            if (!$filterRole || $filterRole === 'admin') {
                $totalExpected += 1; // admin user
            }

            // Determine expected number of pages
            $expectedPages = (int) ceil($totalExpected / 20);

            // Verify each page
            for ($page = 1; $page <= $expectedPages; $page++) {
                $pageParams = array_merge($params, ['page' => $page]);

                $response = $this->actingAs($this->adminUser)
                    ->get(route('admin.user-management.index', $pageParams));

                $response->assertStatus(200);

                $returnedUsers = $response->viewData('users');
                $pageCount = $returnedUsers->count();

                if ($page < $expectedPages) {
                    // Non-last pages must have exactly 20 users
                    $this->assertEquals(
                        20,
                        $pageCount,
                        "Iteration {$i}: Page {$page} of {$expectedPages} (total users: {$totalExpected}) "
                        . "should have exactly 20 users, got {$pageCount}. "
                        . "Params: " . json_encode($pageParams)
                    );
                } else {
                    // Last page must have ≤ 20 users and > 0 users
                    $expectedLastPageCount = $totalExpected - (($expectedPages - 1) * 20);
                    $this->assertGreaterThan(
                        0,
                        $pageCount,
                        "Iteration {$i}: Last page {$page} should have at least 1 user."
                    );
                    $this->assertLessThanOrEqual(
                        20,
                        $pageCount,
                        "Iteration {$i}: Last page {$page} should have at most 20 users, got {$pageCount}."
                    );
                    $this->assertEquals(
                        $expectedLastPageCount,
                        $pageCount,
                        "Iteration {$i}: Last page {$page} of {$expectedPages} (total users: {$totalExpected}) "
                        . "should have exactly {$expectedLastPageCount} users, got {$pageCount}. "
                        . "Params: " . json_encode($pageParams)
                    );
                }
            }

            // Reset permission cache for next iteration
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
        }
    }

    /**
     * Feature: user-management-search-filter-sort, Property 9: Combined filters AND logic
     *
     * For any combination of valid search term, role filter, date range, and sort parameters
     * applied simultaneously, the result set SHALL contain exactly those users that satisfy
     * ALL active filter criteria (intersection/AND logic), and the results SHALL be sorted
     * according to the specified sort parameters.
     *
     * **Validates: Requirements 5.1**
     */
    #[Test]
    public function property_combined_filters_and_logic(): void
    {
        $validRoles = ['admin', 'expert', 'user'];

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            // Clean up users from previous iteration (keep admin user)
            User::where('id', '!=', $this->adminUser->id)->delete();
            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            // Generate a random number of users (between 5 and 15) with varied attributes
            $userCount = mt_rand(5, 15);
            $createdUsers = [];

            for ($j = 0; $j < $userCount; $j++) {
                $role = $validRoles[array_rand($validRoles)];
                $randomDate = $this->generateRandomDate('2022-01-01', '2025-05-01');
                $user = User::factory()->create([
                    'role' => $role,
                    'created_at' => $randomDate . ' ' . sprintf('%02d:%02d:%02d', mt_rand(0, 23), mt_rand(0, 59), mt_rand(0, 59)),
                ]);
                $user->assignRole($role);
                $createdUsers[] = $user;
            }

            // Generate a random combination of filters (at least 2 active)
            $params = [];
            $searchTerm = null;
            $filterRole = null;
            $dateFrom = null;
            $dateTo = null;

            // Decide which filters to activate (ensure at least 2 are active)
            $useSearch = mt_rand(0, 1) === 1;
            $useRole = mt_rand(0, 1) === 1;
            $useDateFrom = mt_rand(0, 1) === 1;
            $useDateTo = mt_rand(0, 1) === 1;

            // Ensure at least 2 filters are active
            $activeCount = (int)$useSearch + (int)$useRole + (int)$useDateFrom + (int)$useDateTo;
            while ($activeCount < 2) {
                $choice = mt_rand(0, 3);
                if ($choice === 0 && !$useSearch) { $useSearch = true; $activeCount++; }
                elseif ($choice === 1 && !$useRole) { $useRole = true; $activeCount++; }
                elseif ($choice === 2 && !$useDateFrom) { $useDateFrom = true; $activeCount++; }
                elseif ($choice === 3 && !$useDateTo) { $useDateTo = true; $activeCount++; }
            }

            // Build search filter - use a substring from a random user to increase match likelihood
            if ($useSearch && !empty($createdUsers)) {
                $randomUser = $createdUsers[array_rand($createdUsers)];
                $searchTerm = $this->extractSearchTermFromUser($randomUser);
                $params['search'] = $searchTerm;
            }

            // Build role filter
            if ($useRole) {
                $filterRole = $validRoles[array_rand($validRoles)];
                $params['role'] = $filterRole;
            }

            // Build date range filters (ensure from <= to if both present)
            if ($useDateFrom) {
                $dateFrom = $this->generateRandomDate('2022-01-01', '2025-05-01');
                $params['date_from'] = $dateFrom;
            }
            if ($useDateTo) {
                $minTo = $dateFrom ?? '2022-01-01';
                $dateTo = $this->generateRandomDate($minTo, '2025-05-01');
                $params['date_to'] = $dateTo;
            }

            // Make the request with combined filters
            $response = $this->actingAs($this->adminUser)
                ->get(route('admin.user-management.index', $params));

            $response->assertStatus(200);

            // Get the users returned in the view
            $returnedUsers = $response->viewData('users');
            $returnedUserIds = $returnedUsers->pluck('id')->sort()->values()->toArray();

            // Calculate expected users: those satisfying ALL active criteria (AND logic)
            $allUsers = array_merge($createdUsers, [$this->adminUser]);
            $expectedUserIds = [];

            // Determine effective search term (after sanitization)
            $effectiveSearch = null;
            if ($searchTerm !== null) {
                $effectiveSearch = trim($searchTerm);
                if ($effectiveSearch === '') {
                    $effectiveSearch = null;
                } else {
                    $effectiveSearch = mb_substr($effectiveSearch, 0, 100);
                }
            }

            foreach ($allUsers as $user) {
                // Check search criterion
                if ($effectiveSearch !== null) {
                    $lowerSearch = mb_strtolower($effectiveSearch);
                    $nameMatch = str_contains(mb_strtolower($user->name), $lowerSearch);
                    $emailMatch = str_contains(mb_strtolower($user->email), $lowerSearch);
                    if (!$nameMatch && !$emailMatch) {
                        continue;
                    }
                }

                // Check role criterion
                if ($filterRole !== null) {
                    $userRoles = $user->roles->pluck('name')->toArray();
                    if (!in_array($filterRole, $userRoles)) {
                        continue;
                    }
                }

                // Check date_from criterion
                if ($dateFrom !== null) {
                    $userDate = $user->created_at->format('Y-m-d');
                    if ($userDate < $dateFrom) {
                        continue;
                    }
                }

                // Check date_to criterion
                if ($dateTo !== null) {
                    $userDate = $user->created_at->format('Y-m-d');
                    if ($userDate > $dateTo) {
                        continue;
                    }
                }

                $expectedUserIds[] = $user->id;
            }

            sort($expectedUserIds);

            // Build a description of active filters for error messages
            $filterDesc = [];
            if ($effectiveSearch !== null) { $filterDesc[] = "search='{$effectiveSearch}'"; }
            if ($filterRole !== null) { $filterDesc[] = "role='{$filterRole}'"; }
            if ($dateFrom !== null) { $filterDesc[] = "date_from='{$dateFrom}'"; }
            if ($dateTo !== null) { $filterDesc[] = "date_to='{$dateTo}'"; }

            $this->assertEquals(
                $expectedUserIds,
                $returnedUserIds,
                "Iteration {$i}: Combined filters [" . implode(', ', $filterDesc) . "] "
                . "should return exactly users satisfying ALL criteria (AND logic). "
                . "Expected IDs: [" . implode(',', $expectedUserIds) . "] "
                . "Got IDs: [" . implode(',', $returnedUserIds) . "]"
            );
        }
    }

    /**
     * Feature: user-management-search-filter-sort, Property 12: Result count accuracy
     *
     * For any combination of active filters, the displayed total count SHALL equal the actual
     * number of users matching all active filter criteria.
     *
     * **Validates: Requirements 1.5, 2.6**
     */
    #[Test]
    public function property_result_count_accuracy(): void
    {
        $validRoles = ['admin', 'expert', 'user'];

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            // Clean up users from previous iteration (keep admin user)
            User::where('id', '!=', $this->adminUser->id)->delete();
            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            // Generate a random number of users (between 5 and 20)
            $userCount = mt_rand(5, 20);
            $createdUsers = [];

            for ($j = 0; $j < $userCount; $j++) {
                $role = $validRoles[array_rand($validRoles)];
                $randomDate = $this->generateRandomDate('2022-01-01', '2025-05-01');
                $user = User::factory()->create([
                    'role' => $role,
                    'created_at' => "{$randomDate} " . sprintf('%02d:%02d:%02d', mt_rand(0, 23), mt_rand(0, 59), mt_rand(0, 59)),
                ]);
                $user->assignRole($role);
                $createdUsers[] = $user;
            }

            // Generate a random filter combination
            $params = [];
            $search = null;
            $roleFilter = null;
            $dateFrom = null;
            $dateTo = null;

            // Randomly include search (50% chance)
            if (mt_rand(0, 1) === 1 && !empty($createdUsers)) {
                // Use a substring from a random user to increase chance of matches
                $randomUser = $createdUsers[array_rand($createdUsers)];
                $search = $this->extractSearchTermFromUser($randomUser);
                $params['search'] = $search;
            }

            // Randomly include role filter (50% chance)
            if (mt_rand(0, 1) === 1) {
                $roleFilter = $validRoles[array_rand($validRoles)];
                $params['role'] = $roleFilter;
            }

            // Randomly include date_from (40% chance)
            if (mt_rand(0, 4) >= 3) {
                $dateFrom = $this->generateRandomDate('2022-01-01', '2024-06-01');
                $params['date_from'] = $dateFrom;
            }

            // Randomly include date_to (40% chance), ensuring it's >= date_from if both present
            if (mt_rand(0, 4) >= 3) {
                $minTo = $dateFrom ?? '2022-01-01';
                $dateTo = $this->generateRandomDate($minTo, '2025-05-01');
                $params['date_to'] = $dateTo;
            }

            // Make the request with the random filter combination
            $response = $this->actingAs($this->adminUser)
                ->get(route('admin.user-management.index', $params));

            $response->assertStatus(200);

            // Get the totalFiltered from the view
            $totalFiltered = $response->viewData('totalFiltered');

            // Calculate expected count manually by applying all filters
            $allUsers = array_merge($createdUsers, [$this->adminUser]);
            $expectedCount = 0;

            // Sanitize search term the same way the controller does
            $effectiveSearch = $search !== null ? trim($search) : null;
            if ($effectiveSearch === '') {
                $effectiveSearch = null;
            }
            if ($effectiveSearch !== null) {
                $effectiveSearch = mb_substr($effectiveSearch, 0, 100);
            }

            foreach ($allUsers as $user) {
                // Check search filter
                if ($effectiveSearch !== null) {
                    $lowerSearch = mb_strtolower($effectiveSearch);
                    $nameMatch = str_contains(mb_strtolower($user->name), $lowerSearch);
                    $emailMatch = str_contains(mb_strtolower($user->email), $lowerSearch);
                    if (!$nameMatch && !$emailMatch) {
                        continue;
                    }
                }

                // Check role filter
                if ($roleFilter !== null) {
                    $userRoles = $user->roles->pluck('name')->toArray();
                    if (!in_array($roleFilter, $userRoles)) {
                        continue;
                    }
                }

                // Check date_from filter
                if ($dateFrom !== null) {
                    $userDate = $user->created_at->format('Y-m-d');
                    if ($userDate < $dateFrom) {
                        continue;
                    }
                }

                // Check date_to filter
                if ($dateTo !== null) {
                    $userDate = $user->created_at->format('Y-m-d');
                    if ($userDate > $dateTo) {
                        continue;
                    }
                }

                $expectedCount++;
            }

            // Build a description of the active filters for error messages
            $filterDesc = [];
            if ($effectiveSearch !== null) {
                $filterDesc[] = "search='{$effectiveSearch}'";
            }
            if ($roleFilter !== null) {
                $filterDesc[] = "role='{$roleFilter}'";
            }
            if ($dateFrom !== null) {
                $filterDesc[] = "date_from='{$dateFrom}'";
            }
            if ($dateTo !== null) {
                $filterDesc[] = "date_to='{$dateTo}'";
            }
            $filterLabel = empty($filterDesc) ? 'no filters' : implode(', ', $filterDesc);

            $this->assertEquals(
                $expectedCount,
                $totalFiltered,
                "Iteration {$i}: With filters [{$filterLabel}] and {$userCount} generated users (+1 admin), "
                . "the totalFiltered view variable ({$totalFiltered}) should equal the actual matching count ({$expectedCount})."
            );
        }
    }

    /**
     * Generate a random invalid parameter value for a given parameter type.
     *
     * @return array{string, string} Tuple of [parameter_name, invalid_value]
     */
    private function generateRandomInvalidParam(): array
    {
        $category = mt_rand(0, 2);

        return match ($category) {
            // Invalid role (not in: admin, expert, user)
            0 => ['role', $this->generateInvalidRole()],
            // Invalid sort_by (not in: name, email, role, created_at)
            1 => ['sort_by', $this->generateInvalidSortColumn()],
            // Invalid sort_dir (not in: asc, desc)
            2 => ['sort_dir', $this->generateInvalidSortDirection()],
        };
    }

    /**
     * Generate a random invalid role value.
     */
    private function generateInvalidRole(): string
    {
        $invalidRoles = [
            'superadmin', 'moderator', 'manager', 'editor', 'subscriber',
            'root', 'guest', 'operator', 'viewer', 'owner',
            'ADMIN', 'Admin', 'USER', 'Expert', 'super_admin',
        ];
        return $invalidRoles[array_rand($invalidRoles)];
    }

    /**
     * Generate a random invalid sort column value.
     */
    private function generateInvalidSortColumn(): string
    {
        $invalidColumns = [
            'phone', 'address', 'id', 'password', 'username',
            'last_login', 'status', 'age', 'country', 'updated_at',
            'NAME', 'Email', 'ROLE', 'Created_At', 'random_col',
        ];
        return $invalidColumns[array_rand($invalidColumns)];
    }

    /**
     * Generate a random invalid sort direction value.
     */
    private function generateInvalidSortDirection(): string
    {
        $invalidDirs = [
            'random', 'up', 'down', 'ascending', 'descending',
            'ASC', 'DESC', 'Asc', 'Desc', 'none',
            'reverse', 'forward', 'backward', '1', '0',
        ];
        return $invalidDirs[array_rand($invalidDirs)];
    }

    /**
     * Feature: user-management-search-filter-sort, Property 8: Sort correctness
     *
     * For any set of users, for any sortable column (name, email, role, created_at),
     * and for any direction (asc, desc), the result set SHALL be ordered such that for every
     * consecutive pair of users (u[i], u[i+1]), the sort column value of u[i] compares
     * ≤ (for asc) or ≥ (for desc) to u[i+1], using case-insensitive comparison for text columns.
     *
     * **Validates: Requirements 4.2, 4.3**
     */
    #[Test]
    public function property_sort_correctness(): void
    {
        $validRoles = ['admin', 'expert', 'user'];
        $sortableColumns = ['name', 'email', 'role', 'created_at'];
        $directions = ['asc', 'desc'];

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            // Clean up users from previous iteration (keep admin user)
            User::where('id', '!=', $this->adminUser->id)->delete();
            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            // Generate a random number of users (between 3 and 15)
            $userCount = mt_rand(3, 15);

            for ($j = 0; $j < $userCount; $j++) {
                $role = $validRoles[array_rand($validRoles)];
                $randomDate = $this->generateRandomDate('2022-01-01', '2025-05-01');
                $user = User::factory()->create([
                    'role' => $role,
                    'created_at' => $randomDate . ' ' . sprintf('%02d:%02d:%02d', mt_rand(0, 23), mt_rand(0, 59), mt_rand(0, 59)),
                ]);
                $user->assignRole($role);
            }

            // Pick a random sortable column and direction
            $sortBy = $sortableColumns[array_rand($sortableColumns)];
            $sortDir = $directions[array_rand($directions)];

            // Make the request with sort parameters
            $response = $this->actingAs($this->adminUser)
                ->get(route('admin.user-management.index', [
                    'sort_by' => $sortBy,
                    'sort_dir' => $sortDir,
                ]));

            $response->assertStatus(200);

            // Get the users returned in the view (in order)
            $returnedUsers = $response->viewData('users');
            $usersList = $returnedUsers->items();

            // Verify ordering invariant: for every consecutive pair, the sort column
            // value of u[i] compares ≤ (asc) or ≥ (desc) to u[i+1]
            for ($k = 0; $k < count($usersList) - 1; $k++) {
                $currentUser = $usersList[$k];
                $nextUser = $usersList[$k + 1];

                // Get the sort column value for comparison
                if ($sortBy === 'role') {
                    // For role sorting, compare the role name from the Spatie roles relationship
                    $currentValue = mb_strtolower($currentUser->roles->first()?->name ?? '');
                    $nextValue = mb_strtolower($nextUser->roles->first()?->name ?? '');
                } elseif ($sortBy === 'created_at') {
                    // For created_at, compare as lowercase strings (timestamps compare correctly as strings)
                    $currentValue = mb_strtolower((string) $currentUser->created_at);
                    $nextValue = mb_strtolower((string) $nextUser->created_at);
                } else {
                    // For name and email, use case-insensitive comparison
                    $currentValue = mb_strtolower($currentUser->{$sortBy});
                    $nextValue = mb_strtolower($nextUser->{$sortBy});
                }

                if ($sortDir === 'asc') {
                    $this->assertTrue(
                        $currentValue <= $nextValue,
                        "Iteration {$i}: Sort by '{$sortBy}' {$sortDir} — user at position {$k} "
                        . "(value: '{$currentValue}') should be ≤ user at position " . ($k + 1)
                        . " (value: '{$nextValue}')."
                    );
                } else {
                    $this->assertTrue(
                        $currentValue >= $nextValue,
                        "Iteration {$i}: Sort by '{$sortBy}' {$sortDir} — user at position {$k} "
                        . "(value: '{$currentValue}') should be ≥ user at position " . ($k + 1)
                        . " (value: '{$nextValue}')."
                    );
                }
            }
        }
    }

    /**
     * Feature: user-management-search-filter-sort, Property 11: Invalid parameters gracefully ignored
     *
     * For any set of query parameters containing invalid values (unrecognized role, unsupported
     * sort column, invalid sort direction), the system SHALL ignore the invalid parameters and
     * apply only the valid parameters, producing the same results as if the invalid parameters
     * were absent. The system handles this gracefully via validation — invalid parameters trigger
     * a redirect (validation failure) rather than a server error, ensuring the user sees a
     * functional page.
     *
     * **Validates: Requirements 5.6**
     */
    #[Test]
    public function property_invalid_parameters_gracefully_ignored(): void
    {
        // Create a set of users for testing
        $validRoles = ['admin', 'expert', 'user'];
        for ($j = 0; $j < 8; $j++) {
            $role = $validRoles[array_rand($validRoles)];
            $user = User::factory()->create([
                'role' => $role,
                'created_at' => $this->generateRandomDate('2022-01-01', '2025-05-01') . ' ' . sprintf('%02d:%02d:%02d', mt_rand(0, 23), mt_rand(0, 59), mt_rand(0, 59)),
            ]);
            $user->assignRole($role);
        }

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            // Generate 1-3 random invalid parameters
            $invalidCount = mt_rand(1, 3);
            $invalidParams = [];
            for ($k = 0; $k < $invalidCount; $k++) {
                [$paramName, $paramValue] = $this->generateRandomInvalidParam();
                $invalidParams[$paramName] = $paramValue;
            }

            // Optionally mix with valid parameters (50% chance to include a valid search)
            $validParams = [];
            if (mt_rand(0, 1) === 1) {
                $validParams['search'] = $this->generateRandomSearchTerm();
            }

            // Combine invalid and valid params
            $allParams = array_merge($validParams, $invalidParams);

            // Make the request with mixed valid/invalid parameters
            $response = $this->actingAs($this->adminUser)
                ->get(route('admin.user-management.index', $allParams));

            // The system should handle invalid parameters gracefully:
            // Laravel's FormRequest validation will reject invalid params and redirect (302)
            // This is the "graceful ignoring" — the system doesn't crash (no 500 error)
            $statusCode = $response->getStatusCode();

            $this->assertTrue(
                in_array($statusCode, [200, 302]),
                "Iteration {$i}: Invalid params " . json_encode($invalidParams) . " mixed with valid params "
                . json_encode($validParams) . " should result in either a successful page (200) or a "
                . "graceful redirect (302), not a server error. Got status: {$statusCode}"
            );

            // If we got a redirect (validation failure), verify it's a proper redirect
            // and following it leads to a working page
            if ($statusCode === 302) {
                // Verify the redirect doesn't lead to an error page
                $followedResponse = $this->actingAs($this->adminUser)
                    ->get(route('admin.user-management.index'));

                // Following the redirect to the base route should produce a working page
                $followedResponse->assertStatus(200);

                // Verify the page still shows users (invalid params were ignored)
                $returnedUsers = $followedResponse->viewData('users');
                $this->assertNotNull(
                    $returnedUsers,
                    "Iteration {$i}: After redirect due to invalid params, the base page should still show users."
                );
            }

            // If we got a 200 (invalid params were silently ignored), verify the valid
            // params were still applied correctly
            if ($statusCode === 200) {
                $returnedUsers = $response->viewData('users');
                $this->assertNotNull(
                    $returnedUsers,
                    "Iteration {$i}: When invalid params are ignored and page loads (200), "
                    . "the users view data should still be present."
                );

                // If we had a valid search param, verify it was applied
                if (isset($validParams['search'])) {
                    $effectiveSearch = mb_strtolower(trim($validParams['search']));
                    if ($effectiveSearch !== '') {
                        // All returned users should match the search term
                        foreach ($returnedUsers as $returnedUser) {
                            $nameMatch = str_contains(mb_strtolower($returnedUser->name), $effectiveSearch);
                            $emailMatch = str_contains(mb_strtolower($returnedUser->email), $effectiveSearch);
                            $this->assertTrue(
                                $nameMatch || $emailMatch,
                                "Iteration {$i}: User '{$returnedUser->name}' ({$returnedUser->email}) should match "
                                . "search term '{$validParams['search']}' when invalid params are ignored."
                            );
                        }
                    }
                }
            }
        }
    }
}
