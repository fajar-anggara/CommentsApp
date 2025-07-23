<?php

namespace App\Facades;

use App\Models\Comment;
use Illuminate\Contracts\Auth\Authenticatable;
use Sentry\Laravel\Facade;

/**
 * @method static addNewComment(array $comment, string $articleId, Authenticatable $commenter)
 * @method static findCommentByExternalArticleIdAndTenantId(string $externalArticleId, int $tenantId)
 * @method static findCommentById(string $commentId)
 * @method static addLikeByCommenter(string $commentId, Authenticatable $commenter)
 * @method static findRepliesByCommentId(string $commentId)
 * @method static updateComment(Comment $comment, array $updateData)
 * @method static deleteComment(Comment $comment)
 */
class CommentDo extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'CommentDo';
    }
}
