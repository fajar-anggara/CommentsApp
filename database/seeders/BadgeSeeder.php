<?php

namespace Database\Seeders;

use App\Enums\Badges;
use App\Models\Badge;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Badge::create([
            'id' => Uuid::uuid4()->toString(),
            'name' => Badges::SIDER->value,
            'avatar' => 'sider_avatar.png',
            'description' => "Si cuman baca",
        ]);

        Badge::create([
            'id' => Uuid::uuid4()->toString(),
            'name' => Badges::CONTRIBUTOR->value,
            'avatar' => 'sider_avatar.png',
            'description' => "Si Paling gercep",
        ]);

        Badge::create([
            'id' => Uuid::uuid4()->toString(),
            'name' => Badges::SEPUH->value,
            'avatar' => 'sider_avatar.png',
            'description' => "Penghuni asli",
        ]);
    }
}
