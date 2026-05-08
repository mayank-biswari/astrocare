<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * Feature: admin-permission-management
 * Property-based tests for user role operations.
 *
 * Properties tested:
 * - Property 7: Role sync to user is exact
 * - Property 11: Non-admin access denied
 *
 * Validates: Requirements 10.1, 11.2
 */
class UserRolePropertyTest extends TestCase
{
    use RefreshDatabase;

    private const ITERATIONS = 100;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Reset cached permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create an admin user that passes AdminMiddleware
        $this->adminUser = User::factory()->create(['role' => 'admin']);
    }

    /**
     * Generate a random unique role name.
     */
    private function generateRandomRoleName(): string
    {
        $adjectives = ['senior', 'junior', 'lead', 'chief', 'assistant', 'deputy', 'head', 'main', 'sub', 'super'];
        $nouns = ['editor', 'manager', 'viewer', 'moderator', 'analyst', 'operator', 'coordinator', 'specialist', 'director', 'supervisor'];

        return $adjectives[array_rand($adjectives)] . '-' . $nouns[array_rand($nouns)] . '-' . mt_rand(1000, 9999);
    }

    /**
     * Property 7: Role sync to user is exact
     *
     * For any user and role subset, after sync the user has exactly those roles — no more, no less.
     *
     * **Validates: Requirements 10.1**
     */
    #[Test]
    public function property_role_sync_to_user_is_exact(): void
    {
        // Create a pool of roles to pick from
        $rolePool = [];
        for ($r = 0; $r < 10; $r++) {
            $role = Role::create(['name' => $this->generateRandomRoleName(), 'guard_name' => 'web']);
            $rolePool[] = $role;
        }

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            // Create a target user (non-admin so we don't trigger self-lockout prevention)
            $user = User::factory()->create(['role' => 'user']);

            // Assign some initial roles to the user
            $initialSize = mt_rand(0, count($rolePool));
            $shuffled = $rolePool;
            shuffle($shuffled);
            $initialRoles = array_slice($shuffled, 0, $initialSize);
            $user->syncRoles(array_map(fn($r) => $r->id, $initialRoles));

            // Now sync a different random subset via the update endpoint
            $newSubsetSize = mt_rand(0, count($rolePool));
            shuffle($shuffled);
            $newRoles = array_slice($shuffled, 0, $newSubsetSize);
            $newRoleIds = array_map(fn($r) => (string) $r->id, $newRoles);

            $response = $this->actingAs($this->adminUser)
                ->put(route('admin.user-roles.update', $user), [
                    'roles' => $newRoleIds,
                ]);

            $response->assertRedirect(route('admin.user-roles.index'));

            // Reload user and check roles
            $user->refresh();
            $user->load('roles');

            $actualRoleIds = $user->roles->pluck('id')->sort()->values()->toArray();
            $expectedRoleIds = collect($newRoleIds)->map(fn($id) => (int) $id)->sort()->values()->toArray();

            $this->assertEquals(
                $expectedRoleIds,
                $actualRoleIds,
                "Iteration {$i}: After sync, user does not have exactly the expected roles. Expected: [" . implode(',', $expectedRoleIds) . "] Got: [" . implode(',', $actualRoleIds) . "]"
            );

            // Clear permission cache for next iteration
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
        }
    }

    /**
     * Property 11: Non-admin access denied
     *
     * For any non-admin user (without admin-level prediction permissions),
     * accessing any Permission_Manager route returns 403.
     *
     * **Validates: Requirements 11.2**
     */
    #[Test]
    public function property_non_admin_access_denied(): void
    {
        // Create a role and permission for route parameter usage
        $testRole = Role::create(['name' => 'test-role-for-access', 'guard_name' => 'web']);
        $testPermission = Permission::create(['name' => 'test permission for access', 'guard_name' => 'web']);
        $testUser = User::factory()->create(['role' => 'user']);

        // All Permission_Manager routes to test
        $routes = [
            ['method' => 'GET', 'route' => route('admin.roles.index')],
            ['method' => 'GET', 'route' => route('admin.roles.create')],
            ['method' => 'POST', 'route' => route('admin.roles.store')],
            ['method' => 'GET', 'route' => route('admin.roles.edit', $testRole)],
            ['method' => 'PUT', 'route' => route('admin.roles.update', $testRole)],
            ['method' => 'DELETE', 'route' => route('admin.roles.destroy', $testRole)],
            ['method' => 'GET', 'route' => route('admin.permissions.index')],
            ['method' => 'GET', 'route' => route('admin.permissions.create')],
            ['method' => 'POST', 'route' => route('admin.permissions.store')],
            ['method' => 'GET', 'route' => route('admin.permissions.edit', $testPermission)],
            ['method' => 'PUT', 'route' => route('admin.permissions.update', $testPermission)],
            ['method' => 'DELETE', 'route' => route('admin.permissions.destroy', $testPermission)],
            ['method' => 'GET', 'route' => route('admin.user-roles.index')],
            ['method' => 'GET', 'route' => route('admin.user-roles.edit', $testUser)],
            ['method' => 'PUT', 'route' => route('admin.user-roles.update', $testUser)],
        ];

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            // Create a non-admin user without any admin-level prediction permissions
            $nonAdminUser = User::factory()->create(['role' => 'user']);

            // Pick a random route to test
            $routeToTest = $routes[array_rand($routes)];

            $response = $this->actingAs($nonAdminUser)
                ->call($routeToTest['method'], $routeToTest['route']);

            $this->assertEquals(
                403,
                $response->getStatusCode(),
                "Iteration {$i}: Non-admin user should get 403 on {$routeToTest['method']} {$routeToTest['route']}, got {$response->getStatusCode()}"
            );
        }
    }
}
