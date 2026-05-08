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
 * Feature: admin-permission-management
 * Property 13: Granular permission enforcement per section
 *
 * For any admin user (with role === 'admin') whose ID is NOT 1, and for any
 * Permission_Manager section (permissions, roles, user-roles), access SHALL be
 * granted if and only if the user has the corresponding granular permission
 * ("manage permissions", "manage roles", or "manage user-roles" respectively).
 * If the user lacks the required permission, the system SHALL return a 403 response.
 *
 * Validates: Requirements 11.3, 11.4, 11.5, 11.6
 */
class PermissionManagerMiddlewarePropertyTest extends TestCase
{
    use RefreshDatabase;

    private const ITERATIONS = 100;

    /**
     * The three sections and their required granular permissions.
     */
    private const SECTIONS = [
        'roles' => 'manage roles',
        'permissions' => 'manage permissions',
        'user-roles' => 'manage user-roles',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        // Reset cached permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create a placeholder user with ID 1 (Super Admin) so test users don't get ID 1
        User::factory()->create(['id' => 1, 'role' => 'admin']);

        // Seed the three granular permissions
        foreach (self::SECTIONS as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Get a route URL for a given section that can be used for access testing.
     */
    private function getRouteForSection(string $section): array
    {
        return match ($section) {
            'roles' => ['method' => 'GET', 'url' => route('admin.roles.index')],
            'permissions' => ['method' => 'GET', 'url' => route('admin.permissions.index')],
            'user-roles' => ['method' => 'GET', 'url' => route('admin.user-roles.index')],
        };
    }

    /**
     * Generate a random subset of the three granular permissions.
     *
     * @return array<string> Permission names to assign
     */
    private function generateRandomPermissionSubset(): array
    {
        $allPermissions = array_values(self::SECTIONS);
        $subset = [];

        foreach ($allPermissions as $permission) {
            if (mt_rand(0, 1) === 1) {
                $subset[] = $permission;
            }
        }

        return $subset;
    }

    /**
     * Property 13: Granular permission enforcement per section
     *
     * For any admin user (ID !== 1) and any Permission_Manager section,
     * access is granted if and only if the user has the corresponding
     * granular permission.
     *
     * **Validates: Requirements 11.3, 11.4, 11.5, 11.6**
     */
    #[Test]
    public function property_granular_permission_enforcement_per_section(): void
    {
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            // Create an admin user with ID !== 1
            $adminUser = User::factory()->create(['role' => 'admin']);
            $this->assertNotEquals(1, $adminUser->id, "Test user should not have ID 1");

            // Assign a random subset of granular permissions
            $assignedPermissions = $this->generateRandomPermissionSubset();

            if (!empty($assignedPermissions)) {
                $adminUser->givePermissionTo($assignedPermissions);
            }

            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            // Pick a random section to test
            $sections = array_keys(self::SECTIONS);
            $section = $sections[array_rand($sections)];
            $requiredPermission = self::SECTIONS[$section];
            $route = $this->getRouteForSection($section);

            // Determine expected outcome
            $hasPermission = in_array($requiredPermission, $assignedPermissions);

            // Make the request
            $response = $this->actingAs($adminUser)
                ->call($route['method'], $route['url']);

            if ($hasPermission) {
                // Access should be granted (200 OK)
                $this->assertNotEquals(
                    403,
                    $response->getStatusCode(),
                    "Iteration {$i}: Admin user with '{$requiredPermission}' permission should NOT get 403 on {$section} section. "
                    . "Assigned permissions: [" . implode(', ', $assignedPermissions) . "]"
                );
            } else {
                // Access should be denied (403 Forbidden)
                $this->assertEquals(
                    403,
                    $response->getStatusCode(),
                    "Iteration {$i}: Admin user WITHOUT '{$requiredPermission}' permission should get 403 on {$section} section. "
                    . "Assigned permissions: [" . implode(', ', $assignedPermissions) . "]. "
                    . "Got status: {$response->getStatusCode()}"
                );
            }

            // Clean up user permissions for next iteration
            $adminUser->revokePermissionTo($assignedPermissions);
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
        }
    }
}
