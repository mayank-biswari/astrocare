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
 * Feature tests for RoleController.
 *
 * Validates: Requirements 1.1, 1.2, 1.3, 2.1, 2.2, 2.3, 2.4, 3.1, 3.2, 3.3, 4.1, 4.2, 4.3, 11.2
 */
class RoleControllerTest extends TestCase
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
    public function index_displays_paginated_roles(): void
    {
        // Create 20 roles to trigger pagination (default 15 per page)
        for ($i = 1; $i <= 20; $i++) {
            Role::create(['name' => "role-{$i}", 'guard_name' => 'web']);
        }

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.roles.index'));

        $response->assertStatus(200);
        $response->assertViewHas('roles');

        $roles = $response->viewData('roles');
        $this->assertEquals(15, $roles->count());
        $this->assertEquals(20, $roles->total());
    }

    #[Test]
    public function index_filters_roles_by_search(): void
    {
        Role::create(['name' => 'content-editor', 'guard_name' => 'web']);
        Role::create(['name' => 'content-manager', 'guard_name' => 'web']);
        Role::create(['name' => 'super-admin', 'guard_name' => 'web']);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.roles.index', ['search' => 'content']));

        $response->assertStatus(200);

        $roles = $response->viewData('roles');
        $this->assertEquals(2, $roles->count());
        $this->assertTrue($roles->pluck('name')->contains('content-editor'));
        $this->assertTrue($roles->pluck('name')->contains('content-manager'));
        $this->assertFalse($roles->pluck('name')->contains('super-admin'));
    }

    #[Test]
    public function index_shows_user_and_permission_counts(): void
    {
        $permission = Permission::create(['name' => 'view dashboard', 'guard_name' => 'web']);
        $role = Role::create(['name' => 'viewer', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);

        $user = User::factory()->create(['role' => 'user']);
        $user->assignRole($role);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.roles.index'));

        $response->assertStatus(200);

        $roles = $response->viewData('roles');
        $viewerRole = $roles->firstWhere('name', 'viewer');
        $this->assertEquals(1, $viewerRole->users_count);
        $this->assertEquals(1, $viewerRole->permissions_count);
    }

    // ─── STORE ────────────────────────────────────────────────────────────────

    #[Test]
    public function store_creates_role_with_valid_data(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.roles.store'), [
                'name' => 'new-editor',
                'guard_name' => 'web',
            ]);

        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('roles', [
            'name' => 'new-editor',
            'guard_name' => 'web',
        ]);
    }

    #[Test]
    public function store_creates_role_with_permissions(): void
    {
        $perm1 = Permission::create(['name' => 'view posts', 'guard_name' => 'web']);
        $perm2 = Permission::create(['name' => 'edit posts', 'guard_name' => 'web']);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.roles.store'), [
                'name' => 'post-editor',
                'guard_name' => 'web',
                'permissions' => [$perm1->id, $perm2->id],
            ]);

        $response->assertRedirect(route('admin.roles.index'));

        $role = Role::where('name', 'post-editor')->first();
        $this->assertNotNull($role);
        $this->assertTrue($role->hasPermissionTo('view posts'));
        $this->assertTrue($role->hasPermissionTo('edit posts'));
    }

    #[Test]
    public function store_fails_with_duplicate_name(): void
    {
        Role::create(['name' => 'existing-role', 'guard_name' => 'web']);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.roles.store'), [
                'name' => 'existing-role',
                'guard_name' => 'web',
            ]);

        $response->assertSessionHasErrors('name');
    }

    #[Test]
    public function store_fails_with_empty_name(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.roles.store'), [
                'name' => '',
                'guard_name' => 'web',
            ]);

        $response->assertSessionHasErrors('name');
    }

    // ─── UPDATE ───────────────────────────────────────────────────────────────

    #[Test]
    public function update_renames_role_with_valid_data(): void
    {
        $role = Role::create(['name' => 'old-name', 'guard_name' => 'web']);

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.roles.update', $role), [
                'name' => 'new-name',
                'guard_name' => 'web',
            ]);

        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('success');

        $role->refresh();
        $this->assertEquals('new-name', $role->name);
    }

    #[Test]
    public function update_syncs_permissions(): void
    {
        $perm1 = Permission::create(['name' => 'perm-a', 'guard_name' => 'web']);
        $perm2 = Permission::create(['name' => 'perm-b', 'guard_name' => 'web']);
        $perm3 = Permission::create(['name' => 'perm-c', 'guard_name' => 'web']);

        $role = Role::create(['name' => 'sync-test', 'guard_name' => 'web']);
        $role->givePermissionTo($perm1, $perm2);

        // Sync to only perm2 and perm3 (remove perm1, add perm3)
        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.roles.update', $role), [
                'name' => 'sync-test',
                'guard_name' => 'web',
                'permissions' => [$perm2->id, $perm3->id],
            ]);

        $response->assertRedirect(route('admin.roles.index'));

        $role->refresh();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->assertFalse($role->hasPermissionTo('perm-a'));
        $this->assertTrue($role->hasPermissionTo('perm-b'));
        $this->assertTrue($role->hasPermissionTo('perm-c'));
    }

    #[Test]
    public function update_fails_with_conflicting_name(): void
    {
        Role::create(['name' => 'taken-name', 'guard_name' => 'web']);
        $role = Role::create(['name' => 'my-role', 'guard_name' => 'web']);

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.roles.update', $role), [
                'name' => 'taken-name',
                'guard_name' => 'web',
            ]);

        $response->assertSessionHasErrors('name');
    }

    #[Test]
    public function update_allows_keeping_same_name(): void
    {
        $role = Role::create(['name' => 'keep-name', 'guard_name' => 'web']);

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.roles.update', $role), [
                'name' => 'keep-name',
                'guard_name' => 'web',
            ]);

        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHasNoErrors();
    }

    // ─── DESTROY ──────────────────────────────────────────────────────────────

    #[Test]
    public function destroy_removes_role(): void
    {
        $role = Role::create(['name' => 'to-delete', 'guard_name' => 'web']);

        $response = $this->actingAs($this->adminUser)
            ->delete(route('admin.roles.destroy', $role));

        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('roles', ['name' => 'to-delete']);
    }

    #[Test]
    public function destroy_cascades_role_removal_from_users(): void
    {
        $role = Role::create(['name' => 'cascade-role', 'guard_name' => 'web']);
        $user = User::factory()->create(['role' => 'user']);
        $user->assignRole($role);

        $this->assertTrue($user->hasRole('cascade-role'));

        $this->actingAs($this->adminUser)
            ->delete(route('admin.roles.destroy', $role));

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $user->refresh();

        $this->assertFalse($user->hasRole('cascade-role'));
    }

    // ─── ACCESS CONTROL ───────────────────────────────────────────────────────

    #[Test]
    public function non_admin_user_gets_403_on_index(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('admin.roles.index'));

        $response->assertStatus(403);
    }

    #[Test]
    public function non_admin_user_gets_403_on_store(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->post(route('admin.roles.store'), [
                'name' => 'hacker-role',
                'guard_name' => 'web',
            ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function non_admin_user_gets_403_on_update(): void
    {
        $role = Role::create(['name' => 'protected-role', 'guard_name' => 'web']);

        $response = $this->actingAs($this->regularUser)
            ->put(route('admin.roles.update', $role), [
                'name' => 'hacked-role',
                'guard_name' => 'web',
            ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function non_admin_user_gets_403_on_destroy(): void
    {
        $role = Role::create(['name' => 'safe-role', 'guard_name' => 'web']);

        $response = $this->actingAs($this->regularUser)
            ->delete(route('admin.roles.destroy', $role));

        $response->assertStatus(403);
    }

    #[Test]
    public function unauthenticated_user_cannot_access_roles(): void
    {
        $response = $this->get(route('admin.roles.index'));

        // The auth middleware redirects unauthenticated users before AdminMiddleware runs
        $response->assertStatus(302);
    }
}
