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
 * Property-based tests for permission operations.
 *
 * Properties tested:
 * - Property 2: Permission search filtering
 * - Property 5: Permission name uniqueness enforcement
 * - Property 9: Permission deletion cascades to role and user assignments
 * - Property 10: Permission rename preserves assignments
 *
 * Validates: Requirements 5.3, 6.2, 7.2, 7.3, 8.1, 8.2
 */
class PermissionPropertyTest extends TestCase
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
     * Generate a random unique permission name.
     */
    private function generateRandomPermissionName(): string
    {
        $verbs = ['view', 'edit', 'delete', 'create', 'manage', 'export', 'import', 'approve', 'publish', 'access'];
        $resources = ['users', 'posts', 'comments', 'settings', 'reports', 'orders', 'products', 'pages', 'media', 'logs'];

        return $verbs[array_rand($verbs)] . ' ' . $resources[array_rand($resources)] . ' ' . mt_rand(1000, 9999);
    }

    /**
     * Generate a random search string from existing permission names.
     * Uses only alphabetic substrings to avoid space-boundary edge cases with SQL LIKE.
     */
    private function generateSearchString(array $permissionNames): string
    {
        if (empty($permissionNames)) {
            return 'nonexistent';
        }

        // Pick a random permission name and extract a word or word fragment
        $name = $permissionNames[array_rand($permissionNames)];
        $words = explode(' ', $name);

        // Pick a random word from the name
        $word = $words[array_rand($words)];
        $len = strlen($word);

        if ($len <= 2) {
            return $word;
        }

        // Extract a substring from the word (no spaces)
        $start = mt_rand(0, max(0, $len - 3));
        $length = mt_rand(2, min(5, $len - $start));

        return substr($word, $start, $length);
    }

    /**
     * Generate a random role name.
     */
    private function generateRandomRoleName(): string
    {
        $adjectives = ['senior', 'junior', 'lead', 'chief', 'assistant', 'deputy', 'head', 'main', 'sub', 'super'];
        $nouns = ['editor', 'manager', 'viewer', 'moderator', 'analyst', 'operator', 'coordinator', 'specialist', 'director', 'supervisor'];

        return $adjectives[array_rand($adjectives)] . '-' . $nouns[array_rand($nouns)] . '-' . mt_rand(1000, 9999);
    }

    /**
     * Property 2: Permission search filtering
     *
     * For any set of permissions and search string, verify all returned permissions
     * contain the search string and no matching permission is excluded.
     *
     * **Validates: Requirements 5.3**
     */
    #[Test]
    public function property_permission_search_filtering(): void
    {
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            // Clean up permissions from previous iteration
            Permission::query()->delete();
            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            // Create a random set of permissions (keep within one page to avoid pagination issues)
            $permCount = mt_rand(1, 10);
            $permNames = [];
            for ($j = 0; $j < $permCount; $j++) {
                $name = $this->generateRandomPermissionName();
                $permNames[] = $name;
                Permission::create(['name' => $name, 'guard_name' => 'web']);
            }

            // Generate a search string
            $search = $this->generateSearchString($permNames);

            // Perform the search via the controller
            $response = $this->actingAs($this->adminUser)
                ->get(route('admin.permissions.index', ['search' => $search]));

            $response->assertStatus(200);

            // Get the permissions from the response view data
            $returnedPermissions = $response->viewData('permissions');

            // Property: All returned permissions contain the search string (case-insensitive)
            foreach ($returnedPermissions as $permission) {
                $this->assertTrue(
                    str_contains(strtolower($permission->name), strtolower($search)),
                    "Iteration {$i}: Permission '{$permission->name}' returned by search but does not contain '{$search}'"
                );
            }

            // Property: No matching permission is excluded
            $expectedMatches = collect($permNames)->filter(function ($name) use ($search) {
                return str_contains(strtolower($name), strtolower($search));
            });

            $returnedNames = $returnedPermissions->pluck('name');
            foreach ($expectedMatches as $expectedName) {
                $this->assertTrue(
                    $returnedNames->contains($expectedName),
                    "Iteration {$i}: Permission '{$expectedName}' matches search '{$search}' but was not returned"
                );
            }
        }
    }

    /**
     * Property 5: Permission name uniqueness enforcement
     *
     * For any existing permission name on same guard, verify creation/rename fails validation.
     *
     * **Validates: Requirements 6.2, 7.2**
     */
    #[Test]
    public function property_permission_name_uniqueness_enforcement(): void
    {
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $permName = $this->generateRandomPermissionName();

            // Create the initial permission
            $existingPermission = Permission::create(['name' => $permName, 'guard_name' => 'web']);

            // Attempt to create another permission with the same name (should fail validation)
            $response = $this->actingAs($this->adminUser)
                ->post(route('admin.permissions.store'), [
                    'name' => $permName,
                    'guard_name' => 'web',
                ]);

            $response->assertSessionHasErrors('name');

            // Also test rename: create a second permission and try to rename it to the existing name
            $secondPermName = $this->generateRandomPermissionName();
            $secondPermission = Permission::create(['name' => $secondPermName, 'guard_name' => 'web']);

            $response = $this->actingAs($this->adminUser)
                ->put(route('admin.permissions.update', $secondPermission), [
                    'name' => $permName,
                    'guard_name' => 'web',
                ]);

            $response->assertSessionHasErrors('name');

            // Clean up for next iteration
            $existingPermission->delete();
            $secondPermission->delete();
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
        }
    }

    /**
     * Property 9: Permission deletion cascades to role and user assignments
     *
     * For any permission assigned to roles/users, after deletion no role or user
     * has that permission.
     *
     * **Validates: Requirements 8.1, 8.2**
     */
    #[Test]
    public function property_permission_deletion_cascades_to_role_and_user_assignments(): void
    {
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $permName = $this->generateRandomPermissionName();
            $permission = Permission::create(['name' => $permName, 'guard_name' => 'web']);

            // Assign the permission to a random number of roles
            $roleCount = mt_rand(1, 3);
            $roles = [];
            for ($r = 0; $r < $roleCount; $r++) {
                $role = Role::create(['name' => $this->generateRandomRoleName(), 'guard_name' => 'web']);
                $role->givePermissionTo($permission);
                $roles[] = $role;
            }

            // Assign the permission directly to a random number of users
            $userCount = mt_rand(1, 3);
            $users = [];
            for ($u = 0; $u < $userCount; $u++) {
                $user = User::factory()->create(['role' => 'user']);
                $user->givePermissionTo($permission);
                $users[] = $user;
            }

            // Verify assignments exist before deletion
            foreach ($roles as $role) {
                $this->assertTrue(
                    $role->hasPermissionTo($permName),
                    "Iteration {$i}: Role should have permission '{$permName}' before deletion"
                );
            }
            foreach ($users as $user) {
                $this->assertTrue(
                    $user->hasDirectPermission($permName),
                    "Iteration {$i}: User should have direct permission '{$permName}' before deletion"
                );
            }

            // Delete the permission via the destroy action
            $response = $this->actingAs($this->adminUser)
                ->delete(route('admin.permissions.destroy', $permission));

            $response->assertRedirect(route('admin.permissions.index'));

            // Property: Permission no longer exists
            $this->assertNull(
                Permission::where('name', $permName)->first(),
                "Iteration {$i}: Permission '{$permName}' should not exist after deletion"
            );

            // Property: No role has the deleted permission
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
            foreach ($roles as $role) {
                $role->refresh();
                $role->load('permissions');
                $this->assertFalse(
                    $role->permissions->contains('name', $permName),
                    "Iteration {$i}: Role '{$role->name}' should not have permission '{$permName}' after deletion"
                );
            }

            // Property: No user has the deleted permission
            foreach ($users as $user) {
                $user->refresh();
                $this->assertFalse(
                    $user->permissions->contains('name', $permName),
                    "Iteration {$i}: User should not have permission '{$permName}' after deletion"
                );
            }

            // Clean up roles
            foreach ($roles as $role) {
                $role->delete();
            }
        }
    }

    /**
     * Property 10: Permission rename preserves assignments
     *
     * For any permission with assignments, after rename all assignments are preserved.
     *
     * **Validates: Requirements 7.3**
     */
    #[Test]
    public function property_permission_rename_preserves_assignments(): void
    {
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $originalName = $this->generateRandomPermissionName();
            $permission = Permission::create(['name' => $originalName, 'guard_name' => 'web']);

            // Assign the permission to a random number of roles
            $roleCount = mt_rand(1, 3);
            $roles = [];
            for ($r = 0; $r < $roleCount; $r++) {
                $role = Role::create(['name' => $this->generateRandomRoleName(), 'guard_name' => 'web']);
                $role->givePermissionTo($permission);
                $roles[] = $role;
            }

            // Assign the permission directly to a random number of users
            $userCount = mt_rand(1, 3);
            $users = [];
            for ($u = 0; $u < $userCount; $u++) {
                $user = User::factory()->create(['role' => 'user']);
                $user->givePermissionTo($permission);
                $users[] = $user;
            }

            // Record the role and user IDs that have this permission
            $assignedRoleIds = collect($roles)->pluck('id')->sort()->values()->toArray();
            $assignedUserIds = collect($users)->pluck('id')->sort()->values()->toArray();

            // Rename the permission via the update action
            $newName = $this->generateRandomPermissionName();
            $response = $this->actingAs($this->adminUser)
                ->put(route('admin.permissions.update', $permission), [
                    'name' => $newName,
                    'guard_name' => 'web',
                ]);

            $response->assertRedirect(route('admin.permissions.index'));

            // Reload the permission
            $permission->refresh();
            $this->assertEquals($newName, $permission->name, "Iteration {$i}: Permission name should be updated");

            // Property: All role assignments are preserved
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
            $permission->load('roles');
            $currentRoleIds = $permission->roles->pluck('id')->sort()->values()->toArray();
            $this->assertEquals(
                $assignedRoleIds,
                $currentRoleIds,
                "Iteration {$i}: Role assignments should be preserved after rename"
            );

            // Property: All user assignments are preserved
            $permission->load('users');
            $currentUserIds = $permission->users->pluck('id')->sort()->values()->toArray();
            $this->assertEquals(
                $assignedUserIds,
                $currentUserIds,
                "Iteration {$i}: User assignments should be preserved after rename"
            );

            // Clean up
            foreach ($roles as $role) {
                $role->delete();
            }
            $permission->delete();
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
        }
    }
}
