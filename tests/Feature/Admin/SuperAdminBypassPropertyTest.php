<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * Feature: admin-permission-management, Property 14: Super Admin bypass
 *
 * Property-based test for Super Admin bypass behavior.
 *
 * Property 14: For any Permission_Manager route, the authenticated admin user
 * with database ID equal to 1 SHALL always be granted access, regardless of
 * whether they have any granular permissions assigned, any roles assigned,
 * or any permissions revoked.
 *
 * Validates: Requirements 13.1, 13.2, 13.3, 13.4
 */
class SuperAdminBypassPropertyTest extends TestCase
{
    use RefreshDatabase;

    private const ITERATIONS = 100;

    private User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        // Reset cached permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create the Super Admin user with ID === 1
        $this->superAdmin = User::factory()->create([
            'id' => 1,
            'role' => 'admin',
        ]);
    }

    /**
     * Get all Permission_Manager routes to test.
     *
     * @return array<array{method: string, route: string, label: string}>
     */
    private function getPermissionManagerRoutes(): array
    {
        // Create test entities for route parameters
        $testRole = Role::firstOrCreate(
            ['name' => 'super-admin-test-role', 'guard_name' => 'web']
        );
        $testPermission = Permission::firstOrCreate(
            ['name' => 'super admin test permission', 'guard_name' => 'web']
        );
        $testUser = User::factory()->create(['role' => 'user']);

        return [
            ['method' => 'GET', 'route' => route('admin.roles.index'), 'label' => 'roles.index'],
            ['method' => 'GET', 'route' => route('admin.roles.create'), 'label' => 'roles.create'],
            ['method' => 'GET', 'route' => route('admin.roles.edit', $testRole), 'label' => 'roles.edit'],
            ['method' => 'GET', 'route' => route('admin.permissions.index'), 'label' => 'permissions.index'],
            ['method' => 'GET', 'route' => route('admin.permissions.create'), 'label' => 'permissions.create'],
            ['method' => 'GET', 'route' => route('admin.permissions.edit', $testPermission), 'label' => 'permissions.edit'],
            ['method' => 'GET', 'route' => route('admin.user-roles.index'), 'label' => 'user-roles.index'],
            ['method' => 'GET', 'route' => route('admin.user-roles.edit', $testUser), 'label' => 'user-roles.edit'],
        ];
    }

    /**
     * Generate a random subset of granular permissions to assign (or not assign).
     *
     * @return array<string> Permission names to assign
     */
    private function generateRandomPermissionState(): array
    {
        $granularPermissions = ['manage permissions', 'manage roles', 'manage user-roles'];
        $subsetSize = mt_rand(0, count($granularPermissions));

        shuffle($granularPermissions);

        return array_slice($granularPermissions, 0, $subsetSize);
    }

    /**
     * Generate a random subset of roles to assign (or not assign).
     *
     * @param array<Role> $rolePool
     * @return array<Role>
     */
    private function generateRandomRoleState(array $rolePool): array
    {
        $subsetSize = mt_rand(0, count($rolePool));

        $shuffled = $rolePool;
        shuffle($shuffled);

        return array_slice($shuffled, 0, $subsetSize);
    }

    /**
     * Property 14: Super Admin bypass
     *
     * For any Permission_Manager route, the admin user with ID === 1 is always
     * granted access regardless of assigned granular permissions.
     *
     * Tests with various combinations:
     * - No permissions assigned at all
     * - Some permissions assigned
     * - All permissions assigned
     * - Random role combinations
     *
     * **Validates: Requirements 13.1, 13.2, 13.3, 13.4**
     */
    #[Test]
    public function property_super_admin_always_granted_access(): void
    {
        // Ensure the granular permissions exist in the system
        $granularPermissions = ['manage permissions', 'manage roles', 'manage user-roles'];
        foreach ($granularPermissions as $permName) {
            Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
        }

        // Create a pool of roles for random assignment
        $rolePool = [];
        for ($r = 0; $r < 5; $r++) {
            $rolePool[] = Role::create([
                'name' => 'bypass-test-role-' . $r . '-' . mt_rand(1000, 9999),
                'guard_name' => 'web',
            ]);
        }

        // Get all routes to test
        $routes = $this->getPermissionManagerRoutes();

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            // Reset the Super Admin's permissions and roles each iteration
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
            $this->superAdmin->syncPermissions([]);
            $this->superAdmin->syncRoles([]);
            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            // Generate a random permission state (none, some, or all granular permissions)
            $permissionsToAssign = $this->generateRandomPermissionState();
            if (!empty($permissionsToAssign)) {
                $this->superAdmin->syncPermissions($permissionsToAssign);
            }

            // Generate a random role state
            $rolesToAssign = $this->generateRandomRoleState($rolePool);
            if (!empty($rolesToAssign)) {
                $this->superAdmin->syncRoles(array_map(fn($r) => $r->id, $rolesToAssign));
            }

            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            // Pick a random route to test
            $routeToTest = $routes[array_rand($routes)];

            // Super Admin (ID === 1) should ALWAYS get access (not 403)
            $response = $this->actingAs($this->superAdmin)
                ->call($routeToTest['method'], $routeToTest['route']);

            $statusCode = $response->getStatusCode();

            // The response should NOT be 403 (forbidden)
            // Valid responses are 200 (success) or 302 (redirect, e.g., after store/update)
            $this->assertNotEquals(
                403,
                $statusCode,
                "Iteration {$i}: Super Admin (ID=1) should NEVER get 403 on {$routeToTest['label']}. "
                . "Permissions assigned: [" . implode(', ', $permissionsToAssign) . "]. "
                . "Roles assigned: [" . implode(', ', array_map(fn($r) => $r->name, $rolesToAssign)) . "]. "
                . "Got status: {$statusCode}"
            );

            // Additionally verify the response is a successful one (200 or redirect)
            $this->assertTrue(
                in_array($statusCode, [200, 301, 302]),
                "Iteration {$i}: Super Admin should get 200 or redirect on {$routeToTest['label']}, got {$statusCode}. "
                . "Permissions: [" . implode(', ', $permissionsToAssign) . "]"
            );
        }
    }
}
