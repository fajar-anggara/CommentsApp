<?php

namespace App\Repositories\Interfaces;

use App\Models\Comment;
use Illuminate\Contracts\Auth\Authenticatable;

interface CommentRepository
{
    public function addNewComment(array $comment, string $articleId, Authenticatable $commenter): ?Comment;
}
