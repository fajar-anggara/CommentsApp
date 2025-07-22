<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Facades\Article as ArticleDo;
use App\Facades\Comment as CommentDo;
use App\Facades\SetLog;

class ArticleControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_comment_successfully()
    {
        // 1. Arrange
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create();
        $user->givePermissionTo('create comments');

        $this->actingAs($user);

        $articleData = [
            'article_url' => 'http://example.com/article/123',
            'article_id' => 'ext-123',
            'tenant_id' => $tenant->id,
        ];

        $commentData = [
            'user_id' => $user->id,
            'content' => 'This is a test comment.',
            'parent_id' => null,
            'tenant_id' => $tenant->id,
            'article_id' => $articleData['article_id'],
            'article_url' => $articleData['article_url'],
        ];

        $article = new Article($articleData);
        $article->id = 1;

        $comment = new Comment($commentData);
        $comment->id = 1;

        // Mock the facades
        ArticleDo::shouldReceive('findOrCreateByArticleExternalId')
            ->once()
            ->with($articleData['article_url'], $articleData['article_id'], $articleData['tenant_id'])
            ->andReturn($article);

        CommentDo::shouldReceive('addNewComment')
            ->once()
            ->andReturn($comment);

        SetLog::shouldReceive('withEvent')->andReturnSelf();
        SetLog::shouldReceive('causedBy')->andReturnSelf();
        SetLog::shouldReceive('performedOn')->andReturnSelf();
        SetLog::shouldReceive('withProperties')->andReturnSelf();
        SetLog::shouldReceive('withMessage')->andReturnSelf();

        // 2. Act
        $response = $this->postJson(route('gsad.addComment', ['externalId' => $articleData['article_id']]), $commentData);

        // 3. Assert
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Berhasil comment',
            ]);
    }
}
