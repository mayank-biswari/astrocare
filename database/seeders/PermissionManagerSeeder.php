<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionManagerSeeder extends Seeder
{
    /**
     * Seed the granular permissions required by the Permission Manager.
     *
     * Creates "manage permissions", "manage roles", and "manage user-roles"
     * with guard "web". Idempotent — skips if permission already exists.
     */
    public function run(): void
    {
        $permissions = [
            'manage permissions',
            'manage roles',
            'manage user-roles',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web']
            );
        }

        // Clear cached permissions after seeding
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
