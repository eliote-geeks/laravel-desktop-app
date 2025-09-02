<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['admin', 'user', 'moderator'];
        
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        $permissions = [
            'view dashboard',
            'manage users',
            'manage settings',
            'view reports',
            'delete users'
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        $adminRole = Role::findByName('admin');
        $adminRole->givePermissionTo(Permission::all());

        $userRole = Role::findByName('user');
        $userRole->givePermissionTo(['view dashboard']);

        $moderatorRole = Role::findByName('moderator');
        $moderatorRole->givePermissionTo(['view dashboard', 'view reports']);
    }
}