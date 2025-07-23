<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\User;
use App\Models\Tenant;
use App\Models\CommentLike;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        // Create necessary permissions for the tests
        Permission::create(['name' => 'create comments']);

        $this->tenant = Tenant::factory()->create();
        $this->user = $this->tenant->user;
        $this->user->givePermissionTo('create comments');
    }

    public function test_guest_cannot_like_a_comment()
    {
        $comment = Comment::factory()->create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->postJson("/api/comments/{$comment->id}/like");

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_like_a_comment()
    {
        $comment = Comment::factory()->create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'likes_count' => 0,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')->postJson("/api/comments/{$comment->id}/like");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Berhasil menambah like',
            ]);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'likes_count' => 1,
        ]);

        $this->assertDatabaseHas('comment_likes', [
            'comment_id' => $comment->id,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_liking_a_non_existent_comment_returns_not_found()
    {
        $nonExistentCommentId = '99999';

        $response = $this->actingAs($this->user, 'sanctum')->postJson("/api/comments/{$nonExistentCommentId}/like");

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Komentar tidak ditemukan',
            ]);
    }

    public function test_liking_a_comment_multiple_times_increments_count_and_creates_one_like_record()
    {
        $comment = Comment::factory()->create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'likes_count' => 0,
        ]);

        // First like
        $this->actingAs($this->user, 'sanctum')->postJson("/api/comments/{$comment->id}/like");
        // Second like
        $response = $this->actingAs($this->user, 'sanctum')->postJson("/api/comments/{$comment->id}/like");

        $response->assertStatus(200);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'likes_count' => 2,
        ]);

        // Even with multiple likes, there should only be one record in comment_likes
        $this->assertDatabaseCount('comment_likes', 1);
    }
}
