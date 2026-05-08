<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * Feature tests for PermissionManagerMiddleware.
 *
 * Tests the two-layer access control:
 * - Layer 1: AdminMiddleware (role === 'admin')
 * - Layer 2: PermissionManagerMiddleware (granular permissions + Super Admin bypass)
 *
 * Validates: Requirements 11.3, 11.4, 11.5, 11.6, 13.1, 13.2, 13.3, 13.4
 */
class PermissionManagerMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create a placeholder user with ID 1 to reserve it for Super Admin tests only.
        // This prevents other test users from accidentally getting ID 1 and bypassing
        // the PermissionManagerMiddleware's Super Admin check.
        User::factory()->create(['id' => 1, 'role' => 'admin']);

        // Seed the three granular permissions
        Permission::firstOrCreate(['name' => 'manage roles', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage permissions', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage user-roles', 'guard_name' => 'web']);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    // ─── SUPER ADMIN BYPASS (ID === 1) ────────────────────────────────────────

    #[Test]
    public function super_admin_can_access_roles_without_granular_permissions(): void
    {
        // The super admin (ID 1) is created in setUp without any granular permissions
        $superAdmin = User::find(1);

        $response = $this->actingAs($superAdmin)->get(route('admin.roles.index'));

        $response->assertStatus(200);
    }

    #[Test]
    public function super_admin_can_access_permissions_without_granular_permissions(): void
    {
        $superAdmin = User::find(1);

        $response = $this->actingAs($superAdmin)->get(route('admin.permissions.index'));

        $response->assertStatus(200);
    }

    #[Test]
    public function super_admin_can_access_user_roles_without_granular_permissions(): void
    {
        $superAdmin = User::find(1);

        $response = $this->actingAs($superAdmin)->get(route('admin.user-roles.index'));

        $response->assertStatus(200);
    }

    // ─── ADMIN WITH "MANAGE ROLES" ONLY ───────────────────────────────────────

    #[Test]
    public function admin_with_manage_roles_can_access_roles(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->givePermissionTo('manage roles');

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $response = $this->actingAs($admin)->get(route('admin.roles.index'));

        $response->assertStatus(200);
    }

    #[Test]
    public function admin_with_manage_roles_gets_403_on_permissions(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->givePermissionTo('manage roles');

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $response = $this->actingAs($admin)->get(route('admin.permissions.index'));

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_with_manage_roles_gets_403_on_user_roles(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->givePermissionTo('manage roles');

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $response = $this->actingAs($admin)->get(route('admin.user-roles.index'));

        $response->assertStatus(403);
    }

    // ─── ADMIN WITH "MANAGE PERMISSIONS" ONLY ─────────────────────────────────

    #[Test]
    public function admin_with_manage_permissions_can_access_permissions(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->givePermissionTo('manage permissions');

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $response = $this->actingAs($admin)->get(route('admin.permissions.index'));

        $response->assertStatus(200);
    }

    #[Test]
    public function admin_with_manage_permissions_gets_403_on_roles(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->givePermissionTo('manage permissions');

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $response = $this->actingAs($admin)->get(route('admin.roles.index'));

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_with_manage_permissions_gets_403_on_user_roles(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->givePermissionTo('manage permissions');

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $response = $this->actingAs($admin)->get(route('admin.user-roles.index'));

        $response->assertStatus(403);
    }

    // ─── ADMIN WITH "MANAGE USER-ROLES" ONLY ──────────────────────────────────

    #[Test]
    public function admin_with_manage_user_roles_can_access_user_roles(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->givePermissionTo('manage user-roles');

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $response = $this->actingAs($admin)->get(route('admin.user-roles.index'));

        $response->assertStatus(200);
    }

    #[Test]
    public function admin_with_manage_user_roles_gets_403_on_roles(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->givePermissionTo('manage user-roles');

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $response = $this->actingAs($admin)->get(route('admin.roles.index'));

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_with_manage_user_roles_gets_403_on_permissions(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->givePermissionTo('manage user-roles');

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $response = $this->actingAs($admin)->get(route('admin.permissions.index'));

        $response->assertStatus(403);
    }

    // ─── ADMIN WITH ALL THREE GRANULAR PERMISSIONS ────────────────────────────

    #[Test]
    public function admin_with_all_granular_permissions_can_access_roles(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->givePermissionTo(['manage roles', 'manage permissions', 'manage user-roles']);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $response = $this->actingAs($admin)->get(route('admin.roles.index'));

        $response->assertStatus(200);
    }

    #[Test]
    public function admin_with_all_granular_permissions_can_access_permissions(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->givePermissionTo(['manage roles', 'manage permissions', 'manage user-roles']);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $response = $this->actingAs($admin)->get(route('admin.permissions.index'));

        $response->assertStatus(200);
    }

    #[Test]
    public function admin_with_all_granular_permissions_can_access_user_roles(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->givePermissionTo(['manage roles', 'manage permissions', 'manage user-roles']);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $response = $this->actingAs($admin)->get(route('admin.user-roles.index'));

        $response->assertStatus(200);
    }

    // ─── ADMIN WITHOUT ANY GRANULAR PERMISSIONS ───────────────────────────────

    #[Test]
    public function admin_without_granular_permissions_gets_403_on_roles(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.roles.index'));

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_without_granular_permissions_gets_403_on_permissions(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.permissions.index'));

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_without_granular_permissions_gets_403_on_user_roles(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.user-roles.index'));

        $response->assertStatus(403);
    }

    // ─── NON-ADMIN USER (AdminMiddleware blocks first) ────────────────────────

    #[Test]
    public function non_admin_user_gets_403_on_roles_regardless_of_granular_permissions(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $user->givePermissionTo('manage roles');

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $response = $this->actingAs($user)->get(route('admin.roles.index'));

        $response->assertStatus(403);
    }

    #[Test]
    public function non_admin_user_gets_403_on_permissions_regardless_of_granular_permissions(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $user->givePermissionTo('manage permissions');

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $response = $this->actingAs($user)->get(route('admin.permissions.index'));

        $response->assertStatus(403);
    }

    #[Test]
    public function non_admin_user_gets_403_on_user_roles_regardless_of_granular_permissions(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $user->givePermissionTo('manage user-roles');

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $response = $this->actingAs($user)->get(route('admin.user-roles.index'));

        $response->assertStatus(403);
    }
}
