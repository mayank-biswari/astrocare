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
 * Feature tests for PermissionController.
 *
 * Validates: Requirements 5.1, 5.2, 5.3, 6.1, 6.2, 6.3, 7.1, 7.2, 7.3, 8.1, 8.2, 11.2
 */
class PermissionControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->adminUser = User::factory()->create(['role' => 'admin']);
        $this->regularUser = User::factory()->create(['role' => 'user']);
    }

    // ─── INDEX ────────────────────────────────────────────────────────────────

    #[Test]
    public function index_displays_paginated_permissions(): void
    {
        $existingCount = Permission::count();

        // Create enough permissions to trigger pagination (default 15 per page)
        $toCreate = max(0, 20 - $existingCount);
        for ($i = 1; $i <= $toCreate; $i++) {
            Permission::create(['name' => "test-permission-{$i}", 'guard_name' => 'web']);
        }

        $totalExpected = $existingCount + $toCreate;

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.permissions.index'));

        $response->assertStatus(200);
        $response->assertViewHas('permissions');

        $permissions = $response->viewData('permissions');
        $this->assertEquals(15, $permissions->count());
        $this->assertEquals($totalExpected, $permissions->total());
    }

    #[Test]
    public function index_filters_permissions_by_search(): void
    {
        Permission::create(['name' => 'zview xposts', 'guard_name' => 'web']);
        Permission::create(['name' => 'zview xusers', 'guard_name' => 'web']);
        Permission::create(['name' => 'zedit xposts', 'guard_name' => 'web']);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.permissions.index', ['search' => 'zview']));

        $response->assertStatus(200);

        $permissions = $response->viewData('permissions');
        $this->assertEquals(2, $permissions->count());
        $this->assertTrue($permissions->pluck('name')->contains('zview xposts'));
        $this->assertTrue($permissions->pluck('name')->contains('zview xusers'));
        $this->assertFalse($permissions->pluck('name')->contains('zedit xposts'));
    }

    #[Test]
    public function index_shows_role_counts(): void
    {
        $permission = Permission::create(['name' => 'view dashboard', 'guard_name' => 'web']);
        $role = Role::create(['name' => 'viewer', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.permissions.index'));

        $response->assertStatus(200);

        $permissions = $response->viewData('permissions');
        $dashboardPerm = $permissions->firstWhere('name', 'view dashboard');
        $this->assertEquals(1, $dashboardPerm->roles_count);
    }

    // ─── STORE ────────────────────────────────────────────────────────────────

    #[Test]
    public function store_creates_permission_with_valid_data(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.permissions.store'), [
                'name' => 'create articles',
                'guard_name' => 'web',
            ]);

        $response->assertRedirect(route('admin.permissions.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('permissions', [
            'name' => 'create articles',
            'guard_name' => 'web',
        ]);
    }

    #[Test]
    public function store_defaults_guard_to_web(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.permissions.store'), [
                'name' => 'manage settings',
            ]);

        $response->assertRedirect(route('admin.permissions.index'));

        $this->assertDatabaseHas('permissions', [
            'name' => 'manage settings',
            'guard_name' => 'web',
        ]);
    }

    #[Test]
    public function store_fails_with_duplicate_name(): void
    {
        Permission::create(['name' => 'existing-permission', 'guard_name' => 'web']);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.permissions.store'), [
                'name' => 'existing-permission',
                'guard_name' => 'web',
            ]);

        $response->assertSessionHasErrors('name');
    }

    #[Test]
    public function store_fails_with_empty_name(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.permissions.store'), [
                'name' => '',
                'guard_name' => 'web',
            ]);

        $response->assertSessionHasErrors('name');
    }

    // ─── UPDATE ───────────────────────────────────────────────────────────────

    #[Test]
    public function update_renames_permission_with_valid_data(): void
    {
        $permission = Permission::create(['name' => 'old-permission', 'guard_name' => 'web']);

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.permissions.update', $permission), [
                'name' => 'new-permission',
                'guard_name' => 'web',
            ]);

        $response->assertRedirect(route('admin.permissions.index'));
        $response->assertSessionHas('success');

        $permission->refresh();
        $this->assertEquals('new-permission', $permission->name);
    }

    #[Test]
    public function update_preserves_role_assignments(): void
    {
        $permission = Permission::create(['name' => 'edit articles', 'guard_name' => 'web']);
        $role1 = Role::create(['name' => 'editor', 'guard_name' => 'web']);
        $role2 = Role::create(['name' => 'manager', 'guard_name' => 'web']);
        $role1->givePermissionTo($permission);
        $role2->givePermissionTo($permission);

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.permissions.update', $permission), [
                'name' => 'edit blog-articles',
                'guard_name' => 'web',
            ]);

        $response->assertRedirect(route('admin.permissions.index'));

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $permission->refresh();

        // Verify the permission was renamed
        $this->assertEquals('edit blog-articles', $permission->name);

        // Verify role assignments are preserved
        $role1->refresh();
        $role2->refresh();
        $this->assertTrue($role1->hasPermissionTo('edit blog-articles'));
        $this->assertTrue($role2->hasPermissionTo('edit blog-articles'));
    }

    #[Test]
    public function update_fails_with_conflicting_name(): void
    {
        Permission::create(['name' => 'taken-permission', 'guard_name' => 'web']);
        $permission = Permission::create(['name' => 'my-permission', 'guard_name' => 'web']);

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.permissions.update', $permission), [
                'name' => 'taken-permission',
                'guard_name' => 'web',
            ]);

        $response->assertSessionHasErrors('name');
    }

    #[Test]
    public function update_allows_keeping_same_name(): void
    {
        $permission = Permission::create(['name' => 'keep-name', 'guard_name' => 'web']);

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.permissions.update', $permission), [
                'name' => 'keep-name',
                'guard_name' => 'web',
            ]);

        $response->assertRedirect(route('admin.permissions.index'));
        $response->assertSessionHasNoErrors();
    }

    // ─── DESTROY ──────────────────────────────────────────────────────────────

    #[Test]
    public function destroy_removes_permission(): void
    {
        $permission = Permission::create(['name' => 'to-delete', 'guard_name' => 'web']);

        $response = $this->actingAs($this->adminUser)
            ->delete(route('admin.permissions.destroy', $permission));

        $response->assertRedirect(route('admin.permissions.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('permissions', ['name' => 'to-delete']);
    }

    #[Test]
    public function destroy_cascades_permission_removal_from_roles(): void
    {
        $permission = Permission::create(['name' => 'cascade-perm', 'guard_name' => 'web']);
        $role = Role::create(['name' => 'cascade-role', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);

        $this->assertTrue($role->hasPermissionTo('cascade-perm'));

        $this->actingAs($this->adminUser)
            ->delete(route('admin.permissions.destroy', $permission));

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // After deletion, the permission no longer exists in the DB
        $this->assertDatabaseMissing('permissions', ['name' => 'cascade-perm']);
        // The pivot record should also be removed (cascade)
        $this->assertDatabaseMissing('role_has_permissions', [
            'permission_id' => $permission->id,
            'role_id' => $role->id,
        ]);
    }

    #[Test]
    public function destroy_cascades_permission_removal_from_users(): void
    {
        $permission = Permission::create(['name' => 'direct-perm', 'guard_name' => 'web']);
        $user = User::factory()->create(['role' => 'user']);
        $user->givePermissionTo($permission);

        $this->assertTrue($user->hasPermissionTo('direct-perm'));

        $this->actingAs($this->adminUser)
            ->delete(route('admin.permissions.destroy', $permission));

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // After deletion, the permission no longer exists in the DB
        $this->assertDatabaseMissing('permissions', ['name' => 'direct-perm']);
        // The pivot record should also be removed (cascade)
        $this->assertDatabaseMissing('model_has_permissions', [
            'permission_id' => $permission->id,
            'model_id' => $user->id,
            'model_type' => get_class($user),
        ]);
    }

    // ─── ACCESS CONTROL ───────────────────────────────────────────────────────

    #[Test]
    public function non_admin_user_gets_403_on_index(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('admin.permissions.index'));

        $response->assertStatus(403);
    }

    #[Test]
    public function non_admin_user_gets_403_on_store(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->post(route('admin.permissions.store'), [
                'name' => 'hacker-permission',
                'guard_name' => 'web',
            ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function non_admin_user_gets_403_on_update(): void
    {
        $permission = Permission::create(['name' => 'protected-perm', 'guard_name' => 'web']);

        $response = $this->actingAs($this->regularUser)
            ->put(route('admin.permissions.update', $permission), [
                'name' => 'hacked-perm',
                'guard_name' => 'web',
            ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function non_admin_user_gets_403_on_destroy(): void
    {
        $permission = Permission::create(['name' => 'safe-perm', 'guard_name' => 'web']);

        $response = $this->actingAs($this->regularUser)
            ->delete(route('admin.permissions.destroy', $permission));

        $response->assertStatus(403);
    }

    #[Test]
    public function unauthenticated_user_cannot_access_permissions(): void
    {
        $response = $this->get(route('admin.permissions.index'));

        // The auth middleware redirects unauthenticated users before AdminMiddleware runs
        $response->assertStatus(302);
    }
}
