<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a user first
        $user1 = User::create([
            'name' => 'Tenant Owner 1',
            'email' => 'owner1@example.com',
            'password' => Hash::make('password'),
        ]);

        Tenant::create([
            'name' => 'Tenant One',
            'domain' => 'tenantone.localhost',
            'settings' => ['theme' => 'dark'],
            'owner_name' => $user1->name,
            'owner_email' => $user1->email,
            'user_id' => $user1->id,
        ]);

        $user2 = User::create([
            'name' => 'Tenant Owner 2',
            'email' => 'owner2@example.com',
            'password' => Hash::make('password'),
        ]);

        Tenant::create([
            'name' => 'Tenant Two',
            'domain' => 'tenanttwo.localhost',
            'settings' => ['theme' => 'light'],
            'owner_name' => $user2->name,
            'owner_email' => $user2->email,
            'user_id' => $user2->id,
        ]);
    }
}
