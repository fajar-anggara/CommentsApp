<?php

namespace App\Repositories\Interfaces;

use App\Enums\CommentStatus;
use App\Models\Comment;
use Illuminate\Support\Collection;

interface CommentRepository
{
    public function storeByCommenter(
        array $comment,
        string $commenterId
    ): Comment|bool;

    public function findByExternalArticleIdAndTenantId(
        string $externalArticleId,
        int $tenantId
    ): Collection|bool;

    public function findById(
        string $commentId,
        string $with = null
    ): Comment|bool;

    public function findRepliesByParentId(
        string $parentId
    ): Collection|bool;
}
