<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $root = Role::create(['name' => 'root']);

        $permissions = [
            'user index',
            'user add',
            'user edit',
            'user delete',
            'role index',
            'role add',
            'role edit',
            'role delete',
            'permission index',
            'permission add',
            'permission edit',
            'permission delete',
        ];

        foreach ($permissions as $perm) {
            Permission::create(['name' => $perm]);
        }

        $root->givePermissionTo(Permission::all());
    }
}
