<?php

namespace App\Repositories\DatabaseImplementers;

use App\Models\CommentDownvote;
use App\Models\CommentUpvote;
use App\Repositories\Interfaces\VoteRepository;

class VoteImpl implements VoteRepository
{
    public function findOrNewUpvote(string $commentId, string $commenterId): CommentUpvote|bool
    {
        return CommentUpvote::firstOrNew([
            'comment_id' => $commentId,
            'user_id' => $commenterId
        ]);
    }

    public function findOrNewDownvote(string $commentId, string $commenterId): CommentDownvote|bool
    {
        return CommentDownvote::firstOrNew([
            'comment_id' => $commentId,
            'user_id' => $commenterId
        ]);
    }

    public function delete(string $commentId, string $commenterId, string $upOrDownVote): bool
    {
        if ($upOrDownVote == 'upvote') {
            return CommentUpvote::where([
                'comment_id' => $commentId,
                'user_id' => $commenterId
            ])->delete();
        } elseif ($upOrDownVote == 'downvote') {
            return CommentDownvote::where([
                'comment_id' => $commentId,
                'user_id' => $commenterId
            ])->delete();
        } else {
            return false;
        }
    }
}
