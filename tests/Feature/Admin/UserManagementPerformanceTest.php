<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * Performance tests for User Management search, filter, and sort with large datasets.
 *
 * Validates: Requirements 6.1
 *
 * @group performance
 */
#[Group('performance')]
class UserManagementPerformanceTest extends TestCase
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
        $this->adminUser = User::factory()->create([
            'role' => 'admin',
            'name' => 'Admin User',
            'email' => 'admin@test.com',
        ]);
        $this->adminUser->assignRole('admin');

        // Seed 10,000 users for performance testing
        User::factory()->count(10000)->create();
    }

    #[Test]
    public function search_completes_within_2_seconds_with_10000_users(): void
    {
        $start = microtime(true);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', ['search' => 'test']));

        $elapsed = microtime(true) - $start;

        $response->assertStatus(200);
        $this->assertLessThan(2.0, $elapsed, "Search operation took {$elapsed} seconds, exceeding the 2-second threshold.");
    }

    #[Test]
    public function role_filter_completes_within_2_seconds_with_10000_users(): void
    {
        $start = microtime(true);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', ['role' => 'user']));

        $elapsed = microtime(true) - $start;

        $response->assertStatus(200);
        $this->assertLessThan(2.0, $elapsed, "Role filter operation took {$elapsed} seconds, exceeding the 2-second threshold.");
    }

    #[Test]
    public function date_range_filter_completes_within_2_seconds_with_10000_users(): void
    {
        $start = microtime(true);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', [
                'date_from' => '2024-01-01',
                'date_to' => '2024-12-31',
            ]));

        $elapsed = microtime(true) - $start;

        $response->assertStatus(200);
        $this->assertLessThan(2.0, $elapsed, "Date range filter operation took {$elapsed} seconds, exceeding the 2-second threshold.");
    }

    #[Test]
    public function sort_completes_within_2_seconds_with_10000_users(): void
    {
        $start = microtime(true);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', [
                'sort_by' => 'name',
                'sort_dir' => 'asc',
            ]));

        $elapsed = microtime(true) - $start;

        $response->assertStatus(200);
        $this->assertLessThan(2.0, $elapsed, "Sort operation took {$elapsed} seconds, exceeding the 2-second threshold.");
    }

    #[Test]
    public function combined_filters_complete_within_2_seconds_with_10000_users(): void
    {
        $start = microtime(true);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-management.index', [
                'search' => 'a',
                'role' => 'user',
                'date_from' => '2024-01-01',
                'date_to' => '2024-12-31',
                'sort_by' => 'name',
                'sort_dir' => 'asc',
            ]));

        $elapsed = microtime(true) - $start;

        $response->assertStatus(200);
        $this->assertLessThan(2.0, $elapsed, "Combined filters operation took {$elapsed} seconds, exceeding the 2-second threshold.");
    }
}
