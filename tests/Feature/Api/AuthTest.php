<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed the 'user' role required by the AuthController register action.
        Role::create(['name' => 'user']);
    }

    /**
     * Valid payload for user registration.
     */
    private function validRegistrationPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'Secret1!',
        ], $overrides);
    }

    // ---------------------------------------------------------------
    // Registration
    // ---------------------------------------------------------------

    /**
     * Validates: Requirements 5.1, 5.2, 5.5
     * Successful registration returns 201 with token and user data.
     */
    public function test_registration_with_valid_data_returns_201_with_token_and_user(): void
    {
        $response = $this->postJson('/api/auth/register', $this->validRegistrationPayload());

        $response->assertStatus(201)
            ->assertJsonStructure([
                'token',
                'user' => ['id', 'name', 'email'],
            ])
            ->assertJson([
                'user' => [
                    'name' => 'Jane Doe',
                    'email' => 'jane@example.com',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com',
        ]);
    }

    /**
     * Validates: Requirements 5.2
     * Registration with a duplicate email returns 422.
     */
    public function test_registration_with_duplicate_email_returns_422(): void
    {
        User::factory()->create(['email' => 'jane@example.com']);

        $response = $this->postJson('/api/auth/register', $this->validRegistrationPayload());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Validates: Requirements 5.3
     * Password complexity validation rejects weak passwords.
     */
    public function test_password_complexity_validation_rejects_weak_passwords(): void
    {
        // Too short
        $response = $this->postJson('/api/auth/register', $this->validRegistrationPayload([
            'password' => 'Ab1!',
        ]));
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);

        // Missing uppercase
        $response = $this->postJson('/api/auth/register', $this->validRegistrationPayload([
            'password' => 'abcdefg1!',
        ]));
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);

        // Missing lowercase
        $response = $this->postJson('/api/auth/register', $this->validRegistrationPayload([
            'password' => 'ABCDEFG1!',
        ]));
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);

        // Missing digit
        $response = $this->postJson('/api/auth/register', $this->validRegistrationPayload([
            'password' => 'Abcdefgh!',
        ]));
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);

        // Missing special character
        $response = $this->postJson('/api/auth/register', $this->validRegistrationPayload([
            'password' => 'Abcdefg1',
        ]));
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    // ---------------------------------------------------------------
    // Login
    // ---------------------------------------------------------------

    /**
     * Validates: Requirements 6.1
     * Login with valid credentials returns 200 with token and user.
     */
    public function test_login_with_valid_credentials_returns_200_with_token(): void
    {
        User::factory()->create([
            'email' => 'jane@example.com',
            'password' => bcrypt('Secret1!'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'jane@example.com',
            'password' => 'Secret1!',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'user' => ['id', 'name', 'email'],
            ])
            ->assertJson([
                'user' => [
                    'email' => 'jane@example.com',
                ],
            ]);
    }

    /**
     * Validates: Requirements 6.2
     * Login with invalid credentials returns 401.
     */
    public function test_login_with_invalid_credentials_returns_401(): void
    {
        User::factory()->create([
            'email' => 'jane@example.com',
            'password' => bcrypt('Secret1!'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'jane@example.com',
            'password' => 'WrongPassword1!',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'The provided credentials are incorrect.',
            ]);
    }

    // ---------------------------------------------------------------
    // Forgot Password
    // ---------------------------------------------------------------

    /**
     * Validates: Requirements 7.1
     * Forgot-password with a registered email returns 200.
     */
    public function test_forgot_password_with_registered_email_returns_200(): void
    {
        User::factory()->create(['email' => 'jane@example.com']);

        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => 'jane@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'If an account with that email exists, a password reset link has been sent.',
            ]);
    }

    /**
     * Validates: Requirements 7.2
     * Forgot-password with an unregistered email also returns 200 (no enumeration).
     */
    public function test_forgot_password_with_unregistered_email_returns_200(): void
    {
        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => 'nobody@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'If an account with that email exists, a password reset link has been sent.',
            ]);
    }

    // ---------------------------------------------------------------
    // Logout
    // ---------------------------------------------------------------

    /**
     * Validates: Requirements 8.1
     * Logout with a valid token returns 200 and revokes the token.
     */
    public function test_logout_with_valid_token_returns_200_and_revokes_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully logged out.',
            ]);

        // Verify the token has been revoked.
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => User::class,
        ]);
    }

    /**
     * Validates: Requirements 8.2
     * Logout with an invalid token returns 401.
     */
    public function test_logout_with_invalid_token_returns_401(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid-token-value',
        ])->postJson('/api/auth/logout');

        $response->assertStatus(401);
    }
}
