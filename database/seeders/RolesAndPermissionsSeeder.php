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

        Permission::create(['name' => 'view users']);
        Permission::create(['name' => 'edit users']);
        Permission::create(['name' => 'delete users']);

        Permission::create(['name' => 'view profile']);
        Permission::create(['name' => 'edit profile']);

        Permission::create(['name' => 'view messages']);
        Permission::create(['name' => 'send messages']);
        Permission::create(['name' => 'delete messages']);

        Permission::create(['name' => 'view followings']);
        Permission::create(['name' => 'manage followings']);



        $roleAdmin = Role::create(['name' => 'admin']);
        $roleAdmin->givePermissionTo([
            'view users', 'edit users', 'delete users',
            'view profile', 'edit profile',
            'view messages', 'send messages', 'delete messages',
            'view followings', 'manage followings',
        ]);

        $roleModerator = Role::create(['name' => 'moderator']);
        $roleModerator->givePermissionTo([
            'view users',
            'view profile', 'edit profile',
            'view messages', 'send messages', 'delete messages',
            'view followings', 'manage followings',
            ]);

        $roleUser = Role::create(['name' => 'user']);
        $roleUser->givePermissionTo([
            'view profile', 'edit profile',
            'view messages', 'send messages',
            'view followings', 'manage followings',
            ]);
    }
}
