<?php

namespace App\Repositories\DatabaseImplementers;

use App\Models\CommentLike;
use App\Models\User;
use App\Repositories\Interfaces\LikeRepository;

class LikeImpl implements LikeRepository
{
    public function findByContext(array $context): CommentLike|bool
    {
        $commentLike = CommentLike::whith('user')
            ->where('comment_id', $context['comment_id'])
            ->where('user_id', $context['user_id'])
            ->first();

        if (!$commentLike) {
            return false;
        }

        return $commentLike;
    }

    public function store(array $context): CommentLike|bool
    {
        $commentLike = CommentLike::create([
            'comment_id' => $context['comment_id'],
            'user_id' => $context['user_id']
        ]);

        if (!$commentLike) {
            return false;
        }

        return $commentLike;
    }

    public function delete(string $commentLikeId): bool
    {
        $commentLike = CommentLike::find($commentLikeId);

        if (!$commentLike) {
            return false;
        }

        return $commentLike->delete();
    }
}
