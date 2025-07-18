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
        $moderator = Role::create(['name' => 'moderator', 'guard_name' => 'api']);
        $commenter = Role::create(['name' => 'commenter', 'guard_name' => 'api']);
        $reader = Role::create(['name' => 'reader', 'guard_name' => 'api']);

        $reporting = Permission::create(['name' => 'reporting comments']);
        $readComments = Permission::create(['name' => 'read comments']);
        $createComments = Permission::create(['name' => 'create comments']);
        $updateComments = Permission::create(['name' => 'update comments']);
        $deleteComments = Permission::create(['name' => 'delete comments']);
        $banUser = Permission::create(['name' => 'ban user']);
        $mutUser = Permission::create(['name' => 'mute user']);
        $hideComments = Permission::create(['name' => 'hide user']);
        $viewReports = Permission::create(['name' => 'view reports']);
        $badging = Permission::create(['name' => 'badging']);
        $recruiting = Permission::create(['name' => 'recruiting']);


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
