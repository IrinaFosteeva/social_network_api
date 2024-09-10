<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => 'view_users']);
        Permission::create(['name' => 'edit_users']);
        Permission::create(['name' => 'delete_users']);

        Permission::create(['name' => 'view_profiles']);
        Permission::create(['name' => 'edit_profiles']);
        Permission::create(['name' => 'delete_profiles']);

        Permission::create(['name' => 'view_messages']);
        Permission::create(['name' => 'send_messages']);
        Permission::create(['name' => 'delete_messages']);

        Permission::create(['name' => 'view_followings']);
        Permission::create(['name' => 'view_followers']);
        Permission::create(['name' => 'manage_followings']);

        Permission::create(['name' => 'assign_role']);




        $roleAdmin = Role::create(['name' => 'admin']);
        $roleAdmin->givePermissionTo([
            'view_users', 'edit_users', 'delete_users',
            'view_profiles', 'edit_profiles', 'delete_profiles',
            'view_messages', 'send_messages', 'delete_messages',
            'view_followings', 'view_followers', 'manage_followings',
            'assign_role'
        ]);

        $roleModerator = Role::create(['name' => 'moderator']);
        $roleModerator->givePermissionTo([
            'view_users',
            'view_profiles', 'edit_profiles', 'delete_profiles',
            'view_messages', 'send_messages', 'delete_messages',
            'view_followings', 'view_followers', 'manage_followings',
            ]);

        $roleUser = Role::create(['name' => 'user']);
        $roleUser->givePermissionTo([
            'view_users',
            'view_profiles', 'edit_profiles', 'delete_profiles',
            'view_messages', 'send_messages', 'delete_messages',
            'view_followings', 'view_followers', 'manage_followings',
            ]);
    }
}
