<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\StatisticUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserAccessTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'commenter', 'guard_name' => 'sanctum']);
    }

    public function test_it_should_get_authenticated_user_profile_successfully(): void
    {
        $user = User::factory()->has(StatisticUser::factory(), 'statistics')->create();
        $user->assignRole('commenter');

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'avatar_url',
                    'details',
                ],
            ])
            ->assertJson([
                'success' => true,
                'data' => ['id' => $user->id],
            ]);
    }

    public function test_it_should_update_authenticated_user_profile_successfully(): void
    {
        $user = User::factory()->has(StatisticUser::factory(), 'statistics')->create();
        $user->assignRole('commenter');

        Sanctum::actingAs($user);

        $newName = $this->faker->name;
        $response = $this->putJson('/api/auth/profile', [
            'name' => $newName,
            'email' => $user->email, // Add email to satisfy repository
            'bio' => 'This is an updated bio.',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'Berhasil update data']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $newName,
        ]);
    }

    public function test_it_should_delete_authenticated_user_account_successfully(): void
    {
        $user = User::factory()->has(StatisticUser::factory(), 'statistics')->create();
        $user->assignRole('commenter');

        Sanctum::actingAs($user);

        $response = $this->deleteJson('/api/auth/account');

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'Berhasil menghapus akun']);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_it_should_return_unauthorized_if_no_token_is_provided_for_me_route(): void
    {
        $response = $this->getJson(route('user.me'));

        $response->assertUnauthorized();
    }

    public function test_it_should_return_unauthorized_if_no_token_is_provided_for_profile_route(): void
    {
        $response = $this->putJson(route('user.profile'));

        $response->assertUnauthorized();
    }

    public function test_it_should_return_unauthorized_if_no_token_is_provided_for_delete_route(): void
    {
        $response = $this->deleteJson(route('user.delete'));

        $response->assertUnauthorized();
    }
}
