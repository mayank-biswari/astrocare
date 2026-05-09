<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class LeadPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'assign leads',
            'view any lead',
            'view own lead',
            'edit any lead',
            'edit own lead',
            'delete any lead',
            'delete own lead',
            'create lead',
            'export leads',
            'view lead pii',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web']
            );
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
