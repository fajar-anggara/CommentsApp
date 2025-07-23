<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\CommentStatus;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'article_id' => Article::factory(),
            'user_id' => User::factory(),
            'tenant_id' => function (array $attributes) {
                return Tenant::find($attributes['article_id'])->tenant_id;
            },
            'content' => $this->faker->paragraph,
            'status' => CommentStatus::PUBLISHED->value,
            'likes_count' => 0,
            'reports_count' => 0,
            'upvotes_count' => 0,
            'downvotes_count' => 0,
        ];
    }
}
