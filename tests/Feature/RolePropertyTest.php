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
 * Property-based tests for role operations.
 *
 * Properties tested:
 * - Property 1: Role search filtering
 * - Property 3: Role creation with permission assignment
 * - Property 4: Role name uniqueness enforcement
 * - Property 6: Permission sync to role is exact
 * - Property 8: Role deletion cascades to user assignments
 *
 * Validates: Requirements 1.3, 2.1, 2.2, 2.3, 3.2, 3.3, 4.1, 4.2, 9.1
 */
class RolePropertyTest extends TestCase
{
    use RefreshDatabase;

    private const ITERATIONS = 50;

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
     * Generate a random permission name.
     */
    private function generateRandomPermissionName(): string
    {
        $verbs = ['view', 'edit', 'delete', 'create', 'manage', 'export', 'import', 'approve', 'publish', 'access'];
        $resources = ['users', 'posts', 'comments', 'settings', 'reports', 'orders', 'products', 'pages', 'media', 'logs'];

        return $verbs[array_rand($verbs)] . ' ' . $resources[array_rand($resources)] . ' ' . mt_rand(100, 999);
    }

    /**
     * Generate a random search string from existing role names.
     */
    private function generateSearchString(array $roleNames): string
    {
        if (empty($roleNames)) {
            return 'nonexistent';
        }

        // Pick a random role name and extract a substring
        $name = $roleNames[array_rand($roleNames)];
        $len = strlen($name);

        if ($len <= 2) {
            return $name;
        }

        $start = mt_rand(0, max(0, $len - 3));
        $length = mt_rand(2, min(5, $len - $start));

        return substr($name, $start, $length);
    }

    /**
     * Property 1: Role search filtering
     *
     * For any set of roles and search string, verify all returned roles contain
     * the search string and no matching role is excluded.
     *
     * **Validates: Requirements 1.3**
     */
    #[Test]
    public function property_role_search_filtering(): void
    {
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            // Clean up roles from previous iteration
            Role::query()->delete();
            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            // Create a random set of roles
            $roleCount = mt_rand(1, 10);
            $roleNames = [];
            for ($j = 0; $j < $roleCount; $j++) {
                $name = $this->generateRandomRoleName();
                $roleNames[] = $name;
                Role::create(['name' => $name, 'guard_name' => 'web']);
            }

            // Generate a search string
            $search = $this->generateSearchString($roleNames);

            // Perform the search via the controller
            $response = $this->actingAs($this->adminUser)
                ->get(route('admin.roles.index', ['search' => $search]));

            $response->assertStatus(200);

            // Get the roles from the response view data
            $returnedRoles = $response->viewData('roles');

            // Property: All returned roles contain the search string (case-insensitive)
            foreach ($returnedRoles as $role) {
                $this->assertTrue(
                    str_contains(strtolower($role->name), strtolower($search)),
                    "Iteration {$i}: Role '{$role->name}' returned by search but does not contain '{$search}'"
                );
            }

            // Property: No matching role is excluded
            $expectedMatches = collect($roleNames)->filter(function ($name) use ($search) {
                return str_contains(strtolower($name), strtolower($search));
            });

            $returnedNames = $returnedRoles->pluck('name');
            foreach ($expectedMatches as $expectedName) {
                $this->assertTrue(
                    $returnedNames->contains($expectedName),
                    "Iteration {$i}: Role '{$expectedName}' matches search '{$search}' but was not returned"
                );
            }
        }
    }

    /**
     * Property 3: Role creation with permission assignment
     *
     * For any valid unique name and permission subset, verify role is created
     * with exactly those permissions.
     *
     * **Validates: Requirements 2.1, 2.2**
     */
    #[Test]
    public function property_role_creation_with_permission_assignment(): void
    {
        // Create a pool of permissions to pick from
        $permissionPool = [];
        for ($p = 0; $p < 15; $p++) {
            $perm = Permission::create([
                'name' => $this->generateRandomPermissionName(),
                'guard_name' => 'web',
            ]);
            $permissionPool[] = $perm;
        }

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $roleName = $this->generateRandomRoleName();

            // Pick a random subset of permissions
            $subsetSize = mt_rand(0, count($permissionPool));
            $shuffled = $permissionPool;
            shuffle($shuffled);
            $selectedPermissions = array_slice($shuffled, 0, $subsetSize);
            $selectedIds = array_map(fn($p) => $p->id, $selectedPermissions);

            // Create role via the store action
            $response = $this->actingAs($this->adminUser)
                ->post(route('admin.roles.store'), [
                    'name' => $roleName,
                    'guard_name' => 'web',
                    'permissions' => $selectedIds,
                ]);

            $response->assertRedirect(route('admin.roles.index'));

            // Verify role exists
            $role = Role::where('name', $roleName)->first();
            $this->assertNotNull($role, "Iteration {$i}: Role '{$roleName}' was not created");

            // Property: Role has exactly the selected permissions
            $actualPermissionIds = $role->permissions->pluck('id')->sort()->values()->toArray();
            $expectedPermissionIds = collect($selectedIds)->sort()->values()->toArray();

            $this->assertEquals(
                $expectedPermissionIds,
                $actualPermissionIds,
                "Iteration {$i}: Role '{$roleName}' does not have exactly the expected permissions"
            );
        }
    }

    /**
     * Property 4: Role name uniqueness enforcement
     *
     * For any existing role name on same guard, verify creation/rename fails validation.
     *
     * **Validates: Requirements 2.3, 3.3**
     */
    #[Test]
    public function property_role_name_uniqueness_enforcement(): void
    {
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $roleName = $this->generateRandomRoleName();

            // Create the initial role
            $existingRole = Role::create(['name' => $roleName, 'guard_name' => 'web']);

            // Attempt to create another role with the same name (should fail validation)
            $response = $this->actingAs($this->adminUser)
                ->post(route('admin.roles.store'), [
                    'name' => $roleName,
                    'guard_name' => 'web',
                ]);

            $response->assertSessionHasErrors('name');

            // Also test rename: create a second role and try to rename it to the existing name
            $secondRoleName = $this->generateRandomRoleName();
            $secondRole = Role::create(['name' => $secondRoleName, 'guard_name' => 'web']);

            $response = $this->actingAs($this->adminUser)
                ->put(route('admin.roles.update', $secondRole), [
                    'name' => $roleName,
                    'guard_name' => 'web',
                ]);

            $response->assertSessionHasErrors('name');

            // Clean up for next iteration
            $existingRole->delete();
            $secondRole->delete();
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
        }
    }

    /**
     * Property 6: Permission sync to role is exact
     *
     * For any role and permission subset, after sync the role has exactly those permissions.
     *
     * **Validates: Requirements 3.2, 9.1**
     */
    #[Test]
    public function property_permission_sync_to_role_is_exact(): void
    {
        // Create a pool of permissions
        $permissionPool = [];
        for ($p = 0; $p < 15; $p++) {
            $perm = Permission::create([
                'name' => 'sync-test-' . $this->generateRandomPermissionName(),
                'guard_name' => 'web',
            ]);
            $permissionPool[] = $perm;
        }

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            // Create a role with some initial permissions
            $roleName = $this->generateRandomRoleName();
            $role = Role::create(['name' => $roleName, 'guard_name' => 'web']);

            // Assign some initial permissions
            $initialSize = mt_rand(0, count($permissionPool));
            $shuffled = $permissionPool;
            shuffle($shuffled);
            $initialPermissions = array_slice($shuffled, 0, $initialSize);
            $role->syncPermissions(array_map(fn($p) => $p->id, $initialPermissions));

            // Now sync a different subset via the update action
            $newSubsetSize = mt_rand(0, count($permissionPool));
            shuffle($shuffled);
            $newPermissions = array_slice($shuffled, 0, $newSubsetSize);
            $newPermissionIds = array_map(fn($p) => $p->id, $newPermissions);

            $response = $this->actingAs($this->adminUser)
                ->put(route('admin.roles.update', $role), [
                    'name' => $roleName,
                    'guard_name' => 'web',
                    'permissions' => $newPermissionIds,
                ]);

            $response->assertRedirect(route('admin.roles.index'));

            // Reload role and check permissions
            $role->refresh();
            $role->load('permissions');

            $actualPermissionIds = $role->permissions->pluck('id')->sort()->values()->toArray();
            $expectedPermissionIds = collect($newPermissionIds)->sort()->values()->toArray();

            $this->assertEquals(
                $expectedPermissionIds,
                $actualPermissionIds,
                "Iteration {$i}: After sync, role '{$roleName}' does not have exactly the expected permissions"
            );

            // Clean up
            $role->delete();
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
        }
    }

    /**
     * Property 8: Role deletion cascades to user assignments
     *
     * For any role assigned to users, after deletion no user has that role.
     *
     * **Validates: Requirements 4.1, 4.2**
     */
    #[Test]
    public function property_role_deletion_cascades_to_user_assignments(): void
    {
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $roleName = $this->generateRandomRoleName();
            $role = Role::create(['name' => $roleName, 'guard_name' => 'web']);

            // Assign the role to a random number of users
            $userCount = mt_rand(1, 5);
            $users = [];
            for ($u = 0; $u < $userCount; $u++) {
                $user = User::factory()->create(['role' => 'user']);
                $user->assignRole($role);
                $users[] = $user;
            }

            // Verify users have the role before deletion
            foreach ($users as $user) {
                $this->assertTrue(
                    $user->hasRole($roleName),
                    "Iteration {$i}: User should have role '{$roleName}' before deletion"
                );
            }

            // Delete the role via the destroy action
            $response = $this->actingAs($this->adminUser)
                ->delete(route('admin.roles.destroy', $role));

            $response->assertRedirect(route('admin.roles.index'));

            // Property: Role no longer exists
            $this->assertNull(
                Role::where('name', $roleName)->first(),
                "Iteration {$i}: Role '{$roleName}' should not exist after deletion"
            );

            // Property: No user has the deleted role
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
            foreach ($users as $user) {
                $user->refresh();
                $this->assertFalse(
                    $user->hasRole($roleName),
                    "Iteration {$i}: User should not have role '{$roleName}' after deletion"
                );
            }
        }
    }
}
