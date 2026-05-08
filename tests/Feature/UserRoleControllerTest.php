<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * Feature tests for UserRoleController.
 *
 * Validates: Requirements 10.1, 10.2, 10.3, 10.4, 10.5, 11.2
 */
class UserRoleControllerTest extends TestCase
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
    public function index_lists_users_with_roles(): void
    {
        $role = Role::create(['name' => 'editor', 'guard_name' => 'web']);
        $this->regularUser->assignRole($role);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-roles.index'));

        $response->assertStatus(200);
        $response->assertViewHas('users');

        $users = $response->viewData('users');
        $this->assertGreaterThanOrEqual(2, $users->total());

        // Verify the user with role is in the list and roles are loaded
        $userInList = $users->firstWhere('id', $this->regularUser->id);
        $this->assertNotNull($userInList);
        $this->assertTrue($userInList->relationLoaded('roles'));
        $this->assertTrue($userInList->roles->pluck('name')->contains('editor'));
    }

    #[Test]
    public function index_paginates_users(): void
    {
        // Create enough users to trigger pagination (15 per page)
        User::factory()->count(20)->create(['role' => 'user']);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-roles.index'));

        $response->assertStatus(200);

        $users = $response->viewData('users');
        $this->assertEquals(15, $users->count());
        $this->assertGreaterThan(15, $users->total());
    }

    #[Test]
    public function index_filters_users_by_search(): void
    {
        $searchableUser = User::factory()->create([
            'role' => 'user',
            'name' => 'UniqueSearchName',
            'email' => 'unique-search@example.com',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-roles.index', ['search' => 'UniqueSearchName']));

        $response->assertStatus(200);

        $users = $response->viewData('users');
        $this->assertEquals(1, $users->count());
        $this->assertEquals($searchableUser->id, $users->first()->id);
    }

    // ─── EDIT ─────────────────────────────────────────────────────────────────

    #[Test]
    public function edit_shows_role_checkboxes_with_current_assignments(): void
    {
        $role1 = Role::create(['name' => 'editor', 'guard_name' => 'web']);
        $role2 = Role::create(['name' => 'moderator', 'guard_name' => 'web']);
        $this->regularUser->assignRole($role1);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-roles.edit', $this->regularUser));

        $response->assertStatus(200);
        $response->assertViewHas('user');
        $response->assertViewHas('roles');
        $response->assertViewHas('userRoleIds');

        $viewUser = $response->viewData('user');
        $roles = $response->viewData('roles');
        $userRoleIds = $response->viewData('userRoleIds');

        // Verify the user is correct
        $this->assertEquals($this->regularUser->id, $viewUser->id);

        // Verify all roles are passed to the view
        $this->assertTrue($roles->pluck('name')->contains('editor'));
        $this->assertTrue($roles->pluck('name')->contains('moderator'));

        // Verify current role assignments are indicated
        $this->assertContains($role1->id, $userRoleIds);
        $this->assertNotContains($role2->id, $userRoleIds);
    }

    // ─── UPDATE ───────────────────────────────────────────────────────────────

    #[Test]
    public function update_syncs_roles_correctly(): void
    {
        $role1 = Role::create(['name' => 'editor', 'guard_name' => 'web']);
        $role2 = Role::create(['name' => 'moderator', 'guard_name' => 'web']);
        $role3 = Role::create(['name' => 'viewer', 'guard_name' => 'web']);

        // Assign initial roles
        $this->regularUser->assignRole($role1, $role2);

        // Sync to only role2 and role3 (remove role1, add role3)
        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.user-roles.update', $this->regularUser), [
                'roles' => [$role2->id, $role3->id],
            ]);

        $response->assertRedirect(route('admin.user-roles.index'));
        $response->assertSessionHas('success');

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->regularUser->refresh();

        $this->assertFalse($this->regularUser->hasRole('editor'));
        $this->assertTrue($this->regularUser->hasRole('moderator'));
        $this->assertTrue($this->regularUser->hasRole('viewer'));
    }

    #[Test]
    public function update_can_remove_all_roles(): void
    {
        $role = Role::create(['name' => 'editor', 'guard_name' => 'web']);
        $this->regularUser->assignRole($role);

        // Submit with no roles (empty array)
        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.user-roles.update', $this->regularUser), [
                'roles' => [],
            ]);

        $response->assertRedirect(route('admin.user-roles.index'));

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->regularUser->refresh();

        $this->assertCount(0, $this->regularUser->roles);
    }

    #[Test]
    public function update_can_handle_null_roles(): void
    {
        $role = Role::create(['name' => 'editor', 'guard_name' => 'web']);
        $this->regularUser->assignRole($role);

        // Submit without roles key at all
        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.user-roles.update', $this->regularUser), []);

        $response->assertRedirect(route('admin.user-roles.index'));

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->regularUser->refresh();

        $this->assertCount(0, $this->regularUser->roles);
    }

    // ─── SELF-LOCKOUT PREVENTION ──────────────────────────────────────────────

    #[Test]
    public function admin_cannot_remove_own_admin_role(): void
    {
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $editorRole = Role::create(['name' => 'editor', 'guard_name' => 'web']);

        $this->adminUser->assignRole($adminRole);

        // Admin tries to update their own roles without including admin role
        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.user-roles.update', $this->adminUser), [
                'roles' => [$editorRole->id],
            ]);

        // Should redirect back with error (not to index)
        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Admin role should still be assigned
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->adminUser->refresh();

        $this->assertTrue($this->adminUser->hasRole('admin'));
    }

    #[Test]
    public function admin_can_update_own_roles_if_admin_role_included(): void
    {
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $editorRole = Role::create(['name' => 'editor', 'guard_name' => 'web']);

        $this->adminUser->assignRole($adminRole);

        // Admin updates their own roles but keeps admin role
        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.user-roles.update', $this->adminUser), [
                'roles' => [$adminRole->id, $editorRole->id],
            ]);

        $response->assertRedirect(route('admin.user-roles.index'));
        $response->assertSessionHas('success');

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->adminUser->refresh();

        $this->assertTrue($this->adminUser->hasRole('admin'));
        $this->assertTrue($this->adminUser->hasRole('editor'));
    }

    // ─── ACCESS CONTROL ───────────────────────────────────────────────────────

    #[Test]
    public function non_admin_user_gets_403_on_index(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('admin.user-roles.index'));

        $response->assertStatus(403);
    }

    #[Test]
    public function non_admin_user_gets_403_on_edit(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('admin.user-roles.edit', $this->adminUser));

        $response->assertStatus(403);
    }

    #[Test]
    public function non_admin_user_gets_403_on_update(): void
    {
        $role = Role::create(['name' => 'editor', 'guard_name' => 'web']);

        $response = $this->actingAs($this->regularUser)
            ->put(route('admin.user-roles.update', $this->adminUser), [
                'roles' => [$role->id],
            ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function unauthenticated_user_cannot_access_user_roles(): void
    {
        $response = $this->get(route('admin.user-roles.index'));

        // The auth middleware redirects unauthenticated users before AdminMiddleware runs
        $response->assertStatus(302);
    }
}
