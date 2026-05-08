<?php

namespace Tests\Unit;

use App\Services\PermissionGroupingService;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for PermissionGroupingService.
 *
 * Validates: Requirements 12.1, 12.2, 12.3
 */
class PermissionGroupingServiceTest extends TestCase
{
    /**
     * Test with mixed permissions: some with spaces, some without.
     */
    #[Test]
    public function it_groups_mixed_permissions_correctly(): void
    {
        $permissions = collect([
            'view users',
            'edit users',
            'admin',
            'delete posts',
            'moderator',
            'view posts',
        ]);

        $grouped = PermissionGroupingService::group($permissions);

        // Permissions with spaces are grouped by ucfirst prefix
        $this->assertTrue($grouped->has('View'));
        $this->assertTrue($grouped->has('Edit'));
        $this->assertTrue($grouped->has('Delete'));
        $this->assertTrue($grouped->has('General'));

        // Verify correct permissions in each group
        $this->assertTrue($grouped->get('View')->contains('view users'));
        $this->assertTrue($grouped->get('View')->contains('view posts'));
        $this->assertTrue($grouped->get('Edit')->contains('edit users'));
        $this->assertTrue($grouped->get('Delete')->contains('delete posts'));

        // Permissions without spaces go to General
        $this->assertTrue($grouped->get('General')->contains('admin'));
        $this->assertTrue($grouped->get('General')->contains('moderator'));
    }

    /**
     * Test with empty collection returns empty collection.
     */
    #[Test]
    public function it_returns_empty_collection_for_empty_input(): void
    {
        $permissions = collect([]);

        $grouped = PermissionGroupingService::group($permissions);

        $this->assertInstanceOf(Collection::class, $grouped);
        $this->assertTrue($grouped->isEmpty());
    }

    /**
     * Test alphabetical ordering of groups with "General" last.
     */
    #[Test]
    public function it_sorts_groups_alphabetically_with_general_last(): void
    {
        $permissions = collect([
            'view users',
            'admin',
            'edit posts',
            'access lms',
            'delete comments',
        ]);

        $grouped = PermissionGroupingService::group($permissions);

        $keys = $grouped->keys()->toArray();

        // Expected order: Access, Delete, Edit, View, General (alphabetical + General last)
        $this->assertEquals(['Access', 'Delete', 'Edit', 'View', 'General'], $keys);
    }

    /**
     * Test with all permissions having spaces (no "General" group).
     */
    #[Test]
    public function it_handles_all_permissions_with_spaces(): void
    {
        $permissions = collect([
            'view users',
            'edit users',
            'delete posts',
            'create roles',
        ]);

        $grouped = PermissionGroupingService::group($permissions);

        $this->assertFalse($grouped->has('General'));
        $this->assertTrue($grouped->has('View'));
        $this->assertTrue($grouped->has('Edit'));
        $this->assertTrue($grouped->has('Delete'));
        $this->assertTrue($grouped->has('Create'));

        // Verify alphabetical order
        $keys = $grouped->keys()->toArray();
        $this->assertEquals(['Create', 'Delete', 'Edit', 'View'], $keys);
    }

    /**
     * Test with all permissions without spaces (only "General" group).
     */
    #[Test]
    public function it_handles_all_permissions_without_spaces(): void
    {
        $permissions = collect([
            'admin',
            'superuser',
            'moderator',
        ]);

        $grouped = PermissionGroupingService::group($permissions);

        $this->assertEquals(1, $grouped->count());
        $this->assertTrue($grouped->has('General'));
        $this->assertEquals(3, $grouped->get('General')->count());
        $this->assertTrue($grouped->get('General')->contains('admin'));
        $this->assertTrue($grouped->get('General')->contains('superuser'));
        $this->assertTrue($grouped->get('General')->contains('moderator'));
    }
}
