<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tenant>
 */
class TenantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'domain' => $this->faker->domainName,
            'settings' => ['theme' => 'default'],
            'owner_email' => $this->faker->unique()->safeEmail,
            'owner_name' => $this->faker->name,
            'user_id' => User::factory(),
        ];
    }
}
