<?php

namespace App\Repositories\Interfaces;

use App\Models\CommentLike;

interface LikeRepository
{
    public function findByContext(
        array $context
    ): CommentLike|bool;

    public function store(
        array $context
    ): CommentLike|bool;

    public function delete(
        string $commentLikeId
    ): bool;
}
