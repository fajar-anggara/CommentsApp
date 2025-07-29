<?php

namespace App\Facades;

use Sentry\Laravel\Facade;

/**
 * @method static store(array $validated);
 * @method static getByArticleAndTenantId(string $articleId, string $tenantId);
 * @method static getReplies(string $commentId);
 * @method static addReport(string $commentId, string $commenterId, string $reason);
 * @method static deleteReport(string $commentId, string $commenterId);
 *
 * @see \App\Helpers\CommentHelper
 */
class Comment extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'Comment';
    }
}
