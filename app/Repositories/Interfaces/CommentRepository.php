<?php

namespace App\Repositories\Interfaces;

use App\Models\Comment;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;

interface CommentRepository
{
    public function addNewComment(array $comment, string $articleId, Authenticatable $commenter): ?Comment;
    public function findCommentByExternalArticleIdAndTenantId(string $externalArticleId, int $tenantId): ?Collection;
    public function findRepliesByCommentId(string $commentId): ?Collection;
}
