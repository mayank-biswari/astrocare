<?php

namespace Tests\Feature;

use Database\Seeders\PermissionManagerSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * Feature: admin-permission-management
 * Property 15: Granular permission seeding idempotency
 *
 * For any number of times the permission seeder is executed, the system SHALL
 * contain exactly one instance each of "manage permissions", "manage roles",
 * and "manage user-roles" with guard "web", and no duplicate entries SHALL be created.
 *
 * Validates: Requirements 14.1, 14.2, 14.3, 14.4
 */
class PermissionManagerSeederPropertyTest extends TestCase
{
    use RefreshDatabase;

    private const ITERATIONS = 100;

    private const EXPECTED_PERMISSIONS = [
        'manage permissions',
        'manage roles',
        'manage user-roles',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        // Reset cached permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Property 15: Granular permission seeding idempotency
     *
     * For any number of times the seeder is executed (1-10), the system contains
     * exactly one instance each of the three granular permissions with no duplicates.
     *
     * **Validates: Requirements 14.1, 14.2, 14.3, 14.4**
     */
    #[Test]
    public function property_seeder_idempotency(): void
    {
        $seeder = new PermissionManagerSeeder();

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            // Clear all permissions to start fresh each iteration
            Permission::query()->delete();
            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            // Run the seeder a random number of times (1-10)
            $runCount = mt_rand(1, 10);

            for ($r = 0; $r < $runCount; $r++) {
                $seeder->run();
            }

            // Property: Exactly 3 permissions exist with guard "web" matching the expected names
            $allPermissions = Permission::where('guard_name', 'web')
                ->whereIn('name', self::EXPECTED_PERMISSIONS)
                ->get();

            $this->assertCount(
                3,
                $allPermissions,
                "Iteration {$i} (ran {$runCount} times): Expected exactly 3 granular permissions, found {$allPermissions->count()}"
            );

            // Property: Each expected permission exists exactly once (no duplicates)
            foreach (self::EXPECTED_PERMISSIONS as $expectedName) {
                $count = Permission::where('name', $expectedName)
                    ->where('guard_name', 'web')
                    ->count();

                $this->assertEquals(
                    1,
                    $count,
                    "Iteration {$i} (ran {$runCount} times): Permission '{$expectedName}' should exist exactly once, found {$count} instances"
                );
            }

            // Property: No other permissions with these names exist under different guards
            $totalWithNames = Permission::whereIn('name', self::EXPECTED_PERMISSIONS)->count();
            $this->assertEquals(
                3,
                $totalWithNames,
                "Iteration {$i} (ran {$runCount} times): Found permissions with expected names under unexpected guards"
            );
        }
    }
}
