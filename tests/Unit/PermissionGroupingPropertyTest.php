<?php

namespace Tests\Unit;

use App\Services\PermissionGroupingService;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Feature: admin-permission-management
 * Property 12: Permission grouping by prefix
 *
 * For any collection of permission names, verify each permission is placed in the
 * correct group based on prefix before first space, and permissions with no space
 * are in "General".
 *
 * Validates: Requirements 12.1, 12.2
 */
class PermissionGroupingPropertyTest extends TestCase
{
    private const ITERATIONS = 100;

    /**
     * Generate a random permission name.
     * May or may not contain a space (prefix + suffix pattern).
     */
    private function generateRandomPermissionName(): string
    {
        $prefixes = ['access', 'view', 'edit', 'delete', 'create', 'manage', 'export', 'import', 'approve', 'publish'];
        $suffixes = ['users', 'roles', 'permissions', 'posts', 'comments', 'settings', 'reports', 'lms', 'dashboard', 'orders'];
        $singleWords = ['admin', 'superuser', 'moderator', 'editor', 'viewer', 'manager', 'analyst', 'support'];

        // ~70% chance of having a prefix (space-separated), ~30% single word
        if (mt_rand(1, 10) <= 7) {
            $prefix = $prefixes[array_rand($prefixes)];
            $suffix = $suffixes[array_rand($suffixes)];
            return $prefix . ' ' . $suffix;
        }

        return $singleWords[array_rand($singleWords)];
    }

    /**
     * Generate a random collection of permission name strings.
     */
    private function generateRandomPermissionCollection(): Collection
    {
        $count = mt_rand(0, 30);
        $permissions = [];

        for ($i = 0; $i < $count; $i++) {
            $permissions[] = $this->generateRandomPermissionName();
        }

        return collect($permissions);
    }

    /**
     * Property 12: Each permission is placed in the correct group based on prefix before first space.
     *
     * **Validates: Requirements 12.1, 12.2**
     */
    #[Test]
    public function property_permissions_are_grouped_by_prefix_before_first_space(): void
    {
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $permissions = $this->generateRandomPermissionCollection();
            $grouped = PermissionGroupingService::group($permissions);

            foreach ($permissions as $permissionName) {
                $spacePos = strpos($permissionName, ' ');

                if ($spacePos === false) {
                    // Permissions without a space should be in "General"
                    $this->assertTrue(
                        $grouped->has('General'),
                        "Iteration {$i}: Permission '{$permissionName}' has no space but 'General' group is missing"
                    );
                    $this->assertTrue(
                        $grouped->get('General')->contains($permissionName),
                        "Iteration {$i}: Permission '{$permissionName}' (no space) should be in 'General' group"
                    );
                } else {
                    // Permissions with a space should be in the group named by their prefix (ucfirst)
                    $expectedGroup = ucfirst(substr($permissionName, 0, $spacePos));
                    $this->assertTrue(
                        $grouped->has($expectedGroup),
                        "Iteration {$i}: Expected group '{$expectedGroup}' for permission '{$permissionName}' but group is missing"
                    );
                    $this->assertTrue(
                        $grouped->get($expectedGroup)->contains($permissionName),
                        "Iteration {$i}: Permission '{$permissionName}' should be in group '{$expectedGroup}'"
                    );
                }
            }
        }
    }

    /**
     * Property 12: Permissions with no space in their name are placed in the "General" group.
     *
     * **Validates: Requirements 12.1, 12.2**
     */
    #[Test]
    public function property_permissions_without_space_are_in_general_group(): void
    {
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $permissions = $this->generateRandomPermissionCollection();
            $grouped = PermissionGroupingService::group($permissions);

            // Check that every permission in "General" group has no space
            if ($grouped->has('General')) {
                foreach ($grouped->get('General') as $perm) {
                    $name = is_string($perm) ? $perm : $perm->name;
                    $this->assertFalse(
                        str_contains($name, ' '),
                        "Iteration {$i}: Permission '{$name}' is in 'General' group but contains a space"
                    );
                }
            }

            // Check that no permission without a space exists in a non-General group
            foreach ($grouped as $groupName => $groupPermissions) {
                if ($groupName === 'General') {
                    continue;
                }
                foreach ($groupPermissions as $perm) {
                    $name = is_string($perm) ? $perm : $perm->name;
                    $this->assertTrue(
                        str_contains($name, ' '),
                        "Iteration {$i}: Permission '{$name}' in group '{$groupName}' has no space but should be in 'General'"
                    );
                }
            }
        }
    }

    /**
     * Property 12: Groups are sorted alphabetically with "General" last.
     *
     * **Validates: Requirements 12.1, 12.2**
     */
    #[Test]
    public function property_groups_are_sorted_alphabetically_with_general_last(): void
    {
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $permissions = $this->generateRandomPermissionCollection();
            $grouped = PermissionGroupingService::group($permissions);

            $keys = $grouped->keys()->toArray();

            if (empty($keys)) {
                continue; // Empty collection produces no groups
            }

            // If "General" exists, it must be last
            if (in_array('General', $keys)) {
                $this->assertEquals(
                    'General',
                    end($keys),
                    "Iteration {$i}: 'General' group should be last but found at position " . array_search('General', $keys)
                );

                // Non-General keys should be sorted alphabetically
                $nonGeneralKeys = array_slice($keys, 0, -1);
                $sortedNonGeneral = $nonGeneralKeys;
                sort($sortedNonGeneral);
                $this->assertEquals(
                    $sortedNonGeneral,
                    $nonGeneralKeys,
                    "Iteration {$i}: Non-General groups should be sorted alphabetically"
                );
            } else {
                // All keys should be sorted alphabetically
                $sortedKeys = $keys;
                sort($sortedKeys);
                $this->assertEquals(
                    $sortedKeys,
                    $keys,
                    "Iteration {$i}: Groups should be sorted alphabetically"
                );
            }
        }
    }

    /**
     * Property 12: All input permissions are preserved in the output (no permission is lost).
     *
     * **Validates: Requirements 12.1, 12.2**
     */
    #[Test]
    public function property_all_permissions_are_preserved_in_grouped_output(): void
    {
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $permissions = $this->generateRandomPermissionCollection();
            $grouped = PermissionGroupingService::group($permissions);

            // Total count of permissions across all groups should equal input count
            $totalGrouped = $grouped->flatten()->count();
            $this->assertEquals(
                $permissions->count(),
                $totalGrouped,
                "Iteration {$i}: Expected {$permissions->count()} permissions in output but got {$totalGrouped}"
            );
        }
    }
}
