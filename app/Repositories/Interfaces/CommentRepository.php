<?php

namespace App\Repositories\Interfaces;

use App\Models\Comment;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

interface CommentRepository
{
    public function addNewComment(array $comment, string $articleId, Authenticatable $commenter): ?Comment;
    public function findCommentByExternalArticleIdAndTenantId(string $externalArticleId, int $tenantId): ?Collection;
    public function findRepliesByCommentId(string $commentId): ?Collection;
    public function findCommentById(string $commentId): ?Comment;
    public function addLikeByCommenter(string $commentId, Authenticatable $commenter): bool;
    public function deleteLikeByCommenter(string $commentId,Authenticatable $commenter): bool;
    public function addReportByCommenter(string $commentId,Authenticatable $commenter,array $validated): bool;
    public function deleteReportByCommenter(string $commentId,Authenticatable $commenter): bool;
}
