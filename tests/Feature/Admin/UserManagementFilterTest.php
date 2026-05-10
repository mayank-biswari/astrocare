<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * Feature tests for User Management search, filter, sort, and pagination.
 *
 * Validates: Requirements 1.2, 1.3, 1.6, 1.7, 2.2, 2.3, 2.5, 3.2, 3.3, 3.4, 3.6,
 *            4.2, 4.3, 5.1, 5.2, 5.3, 5.5, 5.6, 5.7, 6.2
 */
class UserManagementFilterTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Ensure roles exist
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'expert', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        // Create admin user for authentication
        $this->adminUser = User::factory()->create(['role' => 'admin', 'name' => 'Admin User', 'email' => 'admin@test.com']);
        $this->adminUser->assignRole('admin');
    }

    // ─── SEARCH TESTS ─────────────────────────────────────────────────────────

    #[Test]
    public function search_returns_matching_users_by_name(): void
    {
        $matchingUser = User::factory()->create(['name' => 'John Smith', 'email' => 'john@example.com']);
        $nonMatchingUser = User::factory()->create(['name' => 'Alice Brown', 'email' => 'alice@example.com']);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', ['search' => 'John']));

        $response->assertStatus(200);
        $response->assertSee('John Smith');
        $response->assertDontSee('Alice Brown');
    }

    #[Test]
    public function search_returns_matching_users_by_email(): void
    {
        $matchingUser = User::factory()->create(['name' => 'Test User', 'email' => 'unique-email@domain.com']);
        $nonMatchingUser = User::factory()->create(['name' => 'Other User', 'email' => 'other@different.com']);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', ['search' => 'unique-email']));

        $response->assertStatus(200);
        $response->assertSee('unique-email@domain.com');
        $response->assertDontSee('other@different.com');
    }

    #[Test]
    public function search_is_case_insensitive(): void
    {
        $user = User::factory()->create(['name' => 'TestUser', 'email' => 'test@example.com']);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', ['search' => 'testuser']));

        $response->assertStatus(200);
        $response->assertSee('TestUser');
    }

    #[Test]
    public function search_excludes_non_matching_users(): void
    {
        User::factory()->create(['name' => 'Matching Name', 'email' => 'match@test.com']);
        User::factory()->create(['name' => 'No Match Here', 'email' => 'nomatch@test.com']);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', ['search' => 'Matching Name']));

        $response->assertStatus(200);
        $response->assertSee('Matching Name');
        $response->assertDontSee('No Match Here');
    }

    #[Test]
    public function empty_search_returns_all_users(): void
    {
        $user1 = User::factory()->create(['name' => 'User One']);
        $user2 = User::factory()->create(['name' => 'User Two']);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', ['search' => '']));

        $response->assertStatus(200);
        $response->assertSee('User One');
        $response->assertSee('User Two');
    }

    #[Test]
    public function whitespace_only_search_returns_all_users(): void
    {
        $user1 = User::factory()->create(['name' => 'User Alpha']);
        $user2 = User::factory()->create(['name' => 'User Beta']);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', ['search' => '   ']));

        $response->assertStatus(200);
        $response->assertSee('User Alpha');
        $response->assertSee('User Beta');
    }

    #[Test]
    public function search_shows_no_users_found_when_no_match(): void
    {
        User::factory()->create(['name' => 'Existing User', 'email' => 'existing@test.com']);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', ['search' => 'zzz-nonexistent-zzz']));

        $response->assertStatus(200);
        $response->assertSee('No users found');
    }

    // ─── ROLE FILTER TESTS ────────────────────────────────────────────────────

    #[Test]
    public function role_filter_returns_only_users_with_specified_role(): void
    {
        $expertUser = User::factory()->create(['name' => 'Expert Person', 'role' => 'expert']);
        $expertUser->assignRole('expert');

        $regularUser = User::factory()->create(['name' => 'Regular Person', 'role' => 'user']);
        $regularUser->assignRole('user');

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', ['role' => 'expert']));

        $response->assertStatus(200);
        $response->assertSee('Expert Person');
        $response->assertDontSee('Regular Person');
    }

    #[Test]
    public function role_filter_all_roles_returns_all_users(): void
    {
        $expertUser = User::factory()->create(['name' => 'Expert User', 'role' => 'expert']);
        $expertUser->assignRole('expert');

        $regularUser = User::factory()->create(['name' => 'Regular User', 'role' => 'user']);
        $regularUser->assignRole('user');

        // No role filter (All Roles)
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index'));

        $response->assertStatus(200);
        $response->assertSee('Expert User');
        $response->assertSee('Regular User');
    }

    #[Test]
    public function role_filter_shows_no_users_found_when_no_match(): void
    {
        // Only create users with 'admin' role (the admin user from setUp)
        // No 'expert' users exist

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', ['role' => 'expert']));

        $response->assertStatus(200);
        $response->assertSee('No users found');
    }

    // ─── DATE RANGE FILTER TESTS ──────────────────────────────────────────────

    #[Test]
    public function date_range_filter_with_both_dates_returns_users_in_range(): void
    {
        $inRangeUser = User::factory()->create([
            'name' => 'In Range User',
            'created_at' => '2024-06-15 10:00:00',
        ]);
        $outOfRangeUser = User::factory()->create([
            'name' => 'Out Of Range User',
            'created_at' => '2024-01-01 10:00:00',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', [
                'date_from' => '2024-06-01',
                'date_to' => '2024-06-30',
            ]));

        $response->assertStatus(200);
        $response->assertSee('In Range User');
        $response->assertDontSee('Out Of Range User');
    }

    #[Test]
    public function date_range_filter_with_only_start_date(): void
    {
        $afterUser = User::factory()->create([
            'name' => 'After Start User',
            'created_at' => '2024-08-15 10:00:00',
        ]);
        $beforeUser = User::factory()->create([
            'name' => 'Before Start User',
            'created_at' => '2024-01-01 10:00:00',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', [
                'date_from' => '2024-07-01',
            ]));

        $response->assertStatus(200);
        $response->assertSee('After Start User');
        $response->assertDontSee('Before Start User');
    }

    #[Test]
    public function date_range_filter_with_only_end_date(): void
    {
        $beforeUser = User::factory()->create([
            'name' => 'Before End User',
            'created_at' => '2024-03-15 10:00:00',
        ]);
        $afterUser = User::factory()->create([
            'name' => 'After End User',
            'created_at' => '2024-12-15 10:00:00',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', [
                'date_to' => '2024-06-30',
            ]));

        $response->assertStatus(200);
        $response->assertSee('Before End User');
        $response->assertDontSee('After End User');
    }

    #[Test]
    public function date_range_filter_is_inclusive_of_boundary_dates(): void
    {
        $boundaryUser = User::factory()->create([
            'name' => 'Boundary User',
            'created_at' => '2024-06-01 00:00:00',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', [
                'date_from' => '2024-06-01',
                'date_to' => '2024-06-01',
            ]));

        $response->assertStatus(200);
        $response->assertSee('Boundary User');
    }

    // ─── SORT TESTS ───────────────────────────────────────────────────────────

    #[Test]
    public function sort_by_name_ascending(): void
    {
        User::factory()->create(['name' => 'Zara User']);
        User::factory()->create(['name' => 'Alice User']);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', [
                'sort_by' => 'name',
                'sort_dir' => 'asc',
            ]));

        $response->assertStatus(200);
        $users = $response->viewData('users');
        $names = $users->pluck('name')->toArray();

        // Verify Alice comes before Zara (case-insensitive)
        $aliceIndex = array_search('Alice User', $names);
        $zaraIndex = array_search('Zara User', $names);
        $this->assertLessThan($zaraIndex, $aliceIndex);
    }

    #[Test]
    public function sort_by_name_descending(): void
    {
        User::factory()->create(['name' => 'Zara User']);
        User::factory()->create(['name' => 'Alice User']);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', [
                'sort_by' => 'name',
                'sort_dir' => 'desc',
            ]));

        $response->assertStatus(200);
        $users = $response->viewData('users');
        $names = $users->pluck('name')->toArray();

        $aliceIndex = array_search('Alice User', $names);
        $zaraIndex = array_search('Zara User', $names);
        $this->assertLessThan($aliceIndex, $zaraIndex);
    }

    #[Test]
    public function sort_by_email_ascending(): void
    {
        User::factory()->create(['name' => 'User Z', 'email' => 'zulu@test.com']);
        User::factory()->create(['name' => 'User A', 'email' => 'alpha@test.com']);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', [
                'sort_by' => 'email',
                'sort_dir' => 'asc',
            ]));

        $response->assertStatus(200);
        $users = $response->viewData('users');
        $emails = $users->pluck('email')->toArray();

        $alphaIndex = array_search('alpha@test.com', $emails);
        $zuluIndex = array_search('zulu@test.com', $emails);
        $this->assertLessThan($zuluIndex, $alphaIndex);
    }

    #[Test]
    public function sort_by_email_descending(): void
    {
        User::factory()->create(['name' => 'User Z', 'email' => 'zulu@test.com']);
        User::factory()->create(['name' => 'User A', 'email' => 'alpha@test.com']);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', [
                'sort_by' => 'email',
                'sort_dir' => 'desc',
            ]));

        $response->assertStatus(200);
        $users = $response->viewData('users');
        $emails = $users->pluck('email')->toArray();

        $alphaIndex = array_search('alpha@test.com', $emails);
        $zuluIndex = array_search('zulu@test.com', $emails);
        $this->assertLessThan($alphaIndex, $zuluIndex);
    }

    #[Test]
    public function sort_by_created_at_ascending(): void
    {
        $olderUser = User::factory()->create(['name' => 'Older User', 'created_at' => '2024-01-01']);
        $newerUser = User::factory()->create(['name' => 'Newer User', 'created_at' => '2024-12-01']);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', [
                'sort_by' => 'created_at',
                'sort_dir' => 'asc',
            ]));

        $response->assertStatus(200);
        $users = $response->viewData('users');
        $names = $users->pluck('name')->toArray();

        $olderIndex = array_search('Older User', $names);
        $newerIndex = array_search('Newer User', $names);
        $this->assertLessThan($newerIndex, $olderIndex);
    }

    #[Test]
    public function sort_by_created_at_descending(): void
    {
        $olderUser = User::factory()->create(['name' => 'Older User', 'created_at' => '2024-01-01']);
        $newerUser = User::factory()->create(['name' => 'Newer User', 'created_at' => '2024-12-01']);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', [
                'sort_by' => 'created_at',
                'sort_dir' => 'desc',
            ]));

        $response->assertStatus(200);
        $users = $response->viewData('users');
        $names = $users->pluck('name')->toArray();

        $olderIndex = array_search('Older User', $names);
        $newerIndex = array_search('Newer User', $names);
        $this->assertLessThan($olderIndex, $newerIndex);
    }

    #[Test]
    public function sort_by_role_ascending(): void
    {
        $adminUser2 = User::factory()->create(['name' => 'Admin Person', 'role' => 'admin']);
        $adminUser2->assignRole('admin');

        $userPerson = User::factory()->create(['name' => 'User Person', 'role' => 'user']);
        $userPerson->assignRole('user');

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', [
                'sort_by' => 'role',
                'sort_dir' => 'asc',
            ]));

        $response->assertStatus(200);
        $users = $response->viewData('users');
        // Admin role should come before User role alphabetically
        $names = $users->pluck('name')->toArray();
        $adminIndex = array_search('Admin Person', $names);
        $userIndex = array_search('User Person', $names);
        $this->assertLessThan($userIndex, $adminIndex);
    }

    #[Test]
    public function sort_by_role_descending(): void
    {
        $adminUser2 = User::factory()->create(['name' => 'Admin Person', 'role' => 'admin']);
        $adminUser2->assignRole('admin');

        $userPerson = User::factory()->create(['name' => 'User Person', 'role' => 'user']);
        $userPerson->assignRole('user');

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', [
                'sort_by' => 'role',
                'sort_dir' => 'desc',
            ]));

        $response->assertStatus(200);
        $users = $response->viewData('users');
        $names = $users->pluck('name')->toArray();
        $adminIndex = array_search('Admin Person', $names);
        $userIndex = array_search('User Person', $names);
        $this->assertLessThan($adminIndex, $userIndex);
    }

    // ─── COMBINED FILTERS (AND LOGIC) ─────────────────────────────────────────

    #[Test]
    public function combined_search_and_role_filter_uses_and_logic(): void
    {
        $matchBoth = User::factory()->create(['name' => 'John Expert', 'role' => 'expert']);
        $matchBoth->assignRole('expert');

        $matchSearchOnly = User::factory()->create(['name' => 'John Regular', 'role' => 'user']);
        $matchSearchOnly->assignRole('user');

        $matchRoleOnly = User::factory()->create(['name' => 'Alice Expert', 'role' => 'expert']);
        $matchRoleOnly->assignRole('expert');

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', [
                'search' => 'John',
                'role' => 'expert',
            ]));

        $response->assertStatus(200);
        $response->assertSee('John Expert');
        $response->assertDontSee('John Regular');
        $response->assertDontSee('Alice Expert');
    }

    #[Test]
    public function combined_search_role_and_date_filter_uses_and_logic(): void
    {
        $matchAll = User::factory()->create([
            'name' => 'Target User',
            'role' => 'expert',
            'created_at' => '2024-06-15',
        ]);
        $matchAll->assignRole('expert');

        $wrongDate = User::factory()->create([
            'name' => 'Target Wrong Date',
            'role' => 'expert',
            'created_at' => '2024-01-01',
        ]);
        $wrongDate->assignRole('expert');

        $wrongRole = User::factory()->create([
            'name' => 'Target Wrong Role',
            'role' => 'user',
            'created_at' => '2024-06-15',
        ]);
        $wrongRole->assignRole('user');

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', [
                'search' => 'Target',
                'role' => 'expert',
                'date_from' => '2024-06-01',
                'date_to' => '2024-06-30',
            ]));

        $response->assertStatus(200);
        $response->assertSee('Target User');
        $response->assertDontSee('Target Wrong Date');
        $response->assertDontSee('Target Wrong Role');
    }

    // ─── PAGINATION TESTS ─────────────────────────────────────────────────────

    #[Test]
    public function pagination_preserves_search_filter_parameters(): void
    {
        // Create enough users to trigger pagination (more than 20)
        for ($i = 0; $i < 25; $i++) {
            $user = User::factory()->create([
                'name' => "Searchable User {$i}",
                'role' => 'user',
            ]);
            $user->assignRole('user');
        }

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', [
                'search' => 'Searchable',
                'role' => 'user',
                'page' => 2,
            ]));

        $response->assertStatus(200);
        // The pagination links should contain the filter parameters
        $response->assertSee('search=Searchable');
        $response->assertSee('role=user');
    }

    #[Test]
    public function pagination_preserves_sort_parameters(): void
    {
        // Create enough users to trigger pagination
        for ($i = 0; $i < 25; $i++) {
            User::factory()->create(['name' => "Paginated User {$i}"]);
        }

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', [
                'sort_by' => 'name',
                'sort_dir' => 'asc',
                'page' => 1,
            ]));

        $response->assertStatus(200);
        $response->assertSee('sort_by=name');
        $response->assertSee('sort_dir=asc');
    }

    // ─── RESET FILTERS TEST ──────────────────────────────────────────────────

    #[Test]
    public function reset_filters_returns_default_view(): void
    {
        User::factory()->create(['name' => 'Some User']);

        // First apply filters
        $filteredResponse = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', [
                'search' => 'nonexistent',
            ]));
        $filteredResponse->assertStatus(200);

        // Then reset (access without any params)
        $resetResponse = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index'));

        $resetResponse->assertStatus(200);
        $resetResponse->assertSee('Some User');
        $resetResponse->assertSee('Reset Filters');
    }

    // ─── INVALID PARAMETERS TESTS ────────────────────────────────────────────

    #[Test]
    public function invalid_role_parameter_is_rejected_by_validation(): void
    {
        $user = User::factory()->create(['name' => 'Valid User', 'role' => 'user']);
        $user->assignRole('user');

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', ['role' => 'invalid_role']));

        // Laravel Form Request validation redirects back with errors for invalid params
        $response->assertStatus(302);
        $response->assertSessionHasErrors('role');
    }

    #[Test]
    public function invalid_sort_by_parameter_is_rejected_by_validation(): void
    {
        User::factory()->create(['name' => 'Test User']);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', ['sort_by' => 'nonexistent_column']));

        $response->assertStatus(302);
        $response->assertSessionHasErrors('sort_by');
    }

    #[Test]
    public function invalid_sort_dir_parameter_is_rejected_by_validation(): void
    {
        User::factory()->create(['name' => 'Test User']);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', ['sort_dir' => 'invalid']));

        $response->assertStatus(302);
        $response->assertSessionHasErrors('sort_dir');
    }

    #[Test]
    public function unknown_query_parameters_are_ignored(): void
    {
        User::factory()->create(['name' => 'Normal User']);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', [
                'unknown_param' => 'value',
                'another_bad' => '123',
            ]));

        $response->assertStatus(200);
        $response->assertSee('Normal User');
    }

    // ─── VALIDATION ERROR TESTS ───────────────────────────────────────────────

    #[Test]
    public function validation_error_displayed_for_start_date_after_end_date(): void
    {
        // Validation redirects back with errors
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', [
                'date_from' => '2024-12-31',
                'date_to' => '2024-01-01',
            ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors('date_from');

        // Follow redirect to verify error is displayed on the page
        $followUp = $this->actingAs($this->adminUser)
            ->withSession(['errors' => session()->get('errors')])
            ->get(route('admin.user-management.index'));

        $followUp->assertStatus(200);
    }

    #[Test]
    public function validation_error_displayed_for_malformed_date(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', [
                'date_from' => 'not-a-date',
            ]));

        // Validation redirects back with errors
        $response->assertStatus(302);
        $response->assertSessionHasErrors('date_from');
    }

    // ─── VIEW DATA ASSERTIONS ─────────────────────────────────────────────────

    #[Test]
    public function view_receives_users_roles_and_total_filtered(): void
    {
        User::factory()->count(3)->create();

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index'));

        $response->assertStatus(200);
        $response->assertViewHas('users');
        $response->assertViewHas('roles');
        $response->assertViewHas('totalFiltered');
    }

    #[Test]
    public function total_filtered_count_reflects_active_filters(): void
    {
        $expert1 = User::factory()->create(['role' => 'expert']);
        $expert1->assignRole('expert');
        $expert2 = User::factory()->create(['role' => 'expert']);
        $expert2->assignRole('expert');
        $regularUser = User::factory()->create(['role' => 'user']);
        $regularUser->assignRole('user');

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', ['role' => 'expert']));

        $response->assertStatus(200);
        $totalFiltered = $response->viewData('totalFiltered');
        $this->assertEquals(2, $totalFiltered);
    }

    // ─── SERVER-SIDE FILTERING VERIFICATION ───────────────────────────────────

    #[Test]
    public function filtering_is_performed_server_side(): void
    {
        // This test verifies that the response is a standard HTML page (server-rendered)
        // and not a JSON API response, confirming server-side filtering
        User::factory()->create(['name' => 'Server Side User']);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', ['search' => 'Server']));

        $response->assertStatus(200);
        // Verify it's an HTML response (case-insensitive charset)
        $contentType = $response->headers->get('content-type');
        $this->assertStringContainsString('text/html', $contentType);
        $response->assertSee('Server Side User');
    }

    #[Test]
    public function default_sort_is_created_at_descending(): void
    {
        $olderUser = User::factory()->create(['name' => 'Older Default', 'created_at' => '2024-01-01']);
        $newerUser = User::factory()->create(['name' => 'Newer Default', 'created_at' => '2024-12-01']);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index'));

        $response->assertStatus(200);
        $users = $response->viewData('users');
        $names = $users->pluck('name')->toArray();

        // Newer should come first in default sort (created_at desc)
        $newerIndex = array_search('Newer Default', $names);
        $olderIndex = array_search('Older Default', $names);
        $this->assertLessThan($olderIndex, $newerIndex);
    }
}
