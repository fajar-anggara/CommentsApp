<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::create(['name' => 'admin']);
        $moderator = Role::create(['name' => 'moderator', 'guard_name' => 'sanctum']);
        $commenter = Role::create(['name' => 'commenter', 'guard_name' => 'sanctum']);
        $reader = Role::create(['name' => 'reader', 'guard_name' => 'sanctum']);

        $reporting = Permission::create(['name' => 'reporting comments', 'guard_name' => 'sanctum']);
        $readComments = Permission::create(['name' => 'read comments', 'guard_name' => 'sanctum']);
        $createComments = Permission::create(['name' => 'create comments', 'guard_name' => 'sanctum']);
        $updateComments = Permission::create(['name' => 'update comments', 'guard_name' => 'sanctum']);
        $deleteComments = Permission::create(['name' => 'delete comments', 'guard_name' => 'sanctum']);
        $banUser = Permission::create(['name' => 'ban user', 'guard_name' => 'sanctum']);
        $mutUser = Permission::create(['name' => 'mute user', 'guard_name' => 'sanctum']);
        $hideComments = Permission::create(['name' => 'hide user', 'guard_name' => 'sanctum']);
        $viewReports = Permission::create(['name' => 'view reports', 'guard_name' => 'sanctum']);
        $badging = Permission::create(['name' => 'badging', 'guard_name' => 'sanctum']);
        $recruiting = Permission::create(['name' => 'recruiting', 'guard_name' => 'sanctum']);


        $admin->syncPermissions([
            $badging,
            $recruiting,
            $viewReports,
            $hideComments,
            $mutUser,
            $banUser,
            $deleteComments,
            $readComments
        ]);

        $moderator->syncPermissions([
            $viewReports,
            $hideComments,
            $mutUser,
            $banUser,
            $deleteComments,
            $readComments
        ]);

        $commenter->syncPermissions([
            $readComments,
            $createComments,
            $updateComments,
            $deleteComments,
            $reporting
        ]);

        $reader->syncPermissions([
            $readComments,
            $reporting
        ]);

    }
}
