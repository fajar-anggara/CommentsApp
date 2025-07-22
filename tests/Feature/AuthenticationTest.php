<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Commenter;
use App\Models\Badge;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Badge::create(['name' => 'SIDER', 'description' => 'Default badge for new users']);
        Role::create(['name' => 'commenter', 'guard_name' => 'sanctum']);
    }

    /**
     * Test if a new commenter can be registered successfully.
     *
     * @return void
     */
    public function test_it_should_register_a_new_commenter_successfully(): void
    {
        $password = $this->faker->password(8);
        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => $password,
        ];

        $response = $this->postJson('/api/auth/register', $data);

        $response->assertStatus(201);

        // Assert the user was created in the database
        $this->assertDatabaseHas('users', [
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        // Assert the response structure
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'avatar_url',
                'token',
                'details' => [
                    'email',
                    'email_verified_at',
                    'bio',
                    'total_comments_created',
                    'total_likes_acquired',
                    'is_muted',
                    'is_banned',
                    'created_at',
                ],
            ],
        ])
        ->assertJson([
            'success' => true,
            'message' => 'Berhasil register Commenter',
            'data' => [
                'name' => $data['name'],
                'details' => [
                    'email' => $data['email'],
                ]
            ]
        ]);
    }

    /**
     * Test if registration fails when name is not provided.
     *
     * @return void
     */
    public function test_it_should_return_validation_error_if_name_is_missing(): void
    {
        $password = $this->faker->password(8);
        $data = [
            'email' => $this->faker->unique()->safeEmail,
            'password' => $password,
        ];

        $response = $this->postJson('/api/auth/register', $data);

        $response->assertStatus(422) // HTTP 422 Unprocessable Entity for validation errors
            ->assertJsonValidationErrors(['name']);

        // Assert the user was not created in the database
        $this->assertDatabaseMissing('users', [
            'email' => $data['email'],
        ]);
    }

    public function test_it_should_login_a_user_successfully(): void
    {
        $password = 'password123';
        $user = User::factory()->create(['password' => bcrypt($password)]);
        $user->assignRole('commenter');

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['token'],
            ])
            ->assertJson(['success' => true]);
    }

    public function test_it_should_logout_an_authenticated_user(): void
    {
        $user = User::factory()->create();
        $user->assignRole('commenter');

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'Berhasil logout']);
    }

    public function test_it_should_refresh_a_token_successfully(): void
    {
        $user = User::factory()->create();
        $user->assignRole('commenter');

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/auth/refresh');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['token'],
            ])
            ->assertJson(['success' => true]);
    }

    // public function test_it_should_fail_login_with_wrong_credentials() { /* ... */ }

    // public function test_it_should_fail_login_if_email_is_missing() { /* ... */ }

    // public function test_it_should_fail_login_if_password_is_missing() { /* ... */ }

    // public function test_it_should_not_logout_an_unauthenticated_user() { /* ... */ }

    // public function test_it_should_not_refresh_token_for_unauthenticated_user() { /* ... */ }
}
