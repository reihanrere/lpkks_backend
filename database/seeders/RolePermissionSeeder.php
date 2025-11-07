<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // create base role
        $admin = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'api']
        );
        $user = Role::firstOrCreate(
            ['name' => 'user', 'guard_name' => 'api']
        );

        // create base permission
        $permissions = [
            'view products',
            'create products',
            'update products',
            'delete products',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'api']
            );
        }

        $admin->syncPermissions(Permission::all());

        $user->syncPermissions([
            'view products',
        ]);
    }
}
