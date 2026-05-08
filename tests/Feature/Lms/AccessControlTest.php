<?php

namespace Tests\Feature\Lms;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Feature tests for LMS access control.
 *
 * Validates: Requirements 1.1, 1.2, 1.3
 */
class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure the 'access lms' permission exists
        Permission::firstOrCreate(['name' => 'access lms', 'guard_name' => 'web']);
    }

    /**
     * Test that an unauthenticated user is redirected to the login page.
     *
     * Validates: Requirement 1.1
     */
    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/lms');

        $response->assertRedirect('/login');
    }

    /**
     * Test that an authenticated user without the 'access lms' permission gets a 403 response.
     *
     * Validates: Requirement 1.2
     */
    public function test_authenticated_user_without_permission_gets_403(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/lms');

        $response->assertStatus(403);
    }

    /**
     * Test that an authenticated user with the 'access lms' permission can access the LMS dashboard.
     *
     * Validates: Requirement 1.3
     */
    public function test_authenticated_user_with_permission_can_access_lms(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('access lms');

        $response = $this->actingAs($user)->get('/lms');

        $response->assertStatus(200);
    }

    /**
     * Test that an unauthenticated user is redirected when accessing the leads route.
     *
     * Validates: Requirement 1.1
     */
    public function test_unauthenticated_user_is_redirected_from_leads_route(): void
    {
        $response = $this->get('/lms/leads');

        $response->assertRedirect('/login');
    }

    /**
     * Test that an authenticated user without permission gets 403 on leads route.
     *
     * Validates: Requirement 1.2
     */
    public function test_user_without_permission_gets_403_on_leads_route(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/lms/leads');

        $response->assertStatus(403);
    }
}
