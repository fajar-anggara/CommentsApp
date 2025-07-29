<?php

namespace App\Repositories\Interfaces;

use App\Models\CommentDownvote;
use App\Models\CommentUpvote;

interface VoteRepository
{
    public function findOrNewUpvote(
        string $commentId,
        string $commenterId
    ): CommentUpvote|bool;

    public function findOrNewDownvote(
        string $commentId,
        string $commenterId
    ): CommentDownvote|bool;

    public function delete(
        string $commentId,
        string $commenterId,
        string $upOrDownVote
    ): bool;
}
