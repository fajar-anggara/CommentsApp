<?php

namespace App\Facades;

use App\Models\Comment;
use Illuminate\Contracts\Auth\Authenticatable;
use Sentry\Laravel\Facade;

/**
 * @method static addNewComment(array $comment, string $articleId, Authenticatable $commenter)
 * @method static findCommentByExternalArticleIdAndTenantId(string $externalArticleId, int $tenantId, string $parentId = null)
 */
class CommentDo extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'CommentDo';
    }
}
