<?php

namespace App\Transformer;

use League\Fractal\TransformerAbstract;

class CommentsTransformer extends TransformerAbstract
{
    public function transform(Comment $comment): array
    {
        return [
            'id' => (string) $comment->id,
            'content' => (string) $comment->content,
            'created_at' => (string) $comment->created_at->toIso8601String(),
            'updated_at' => (string) $comment->updated_at->toIso8601String(),
            'likes_count' => (int) $comment->likes_count,
            'reports_count' => (int) $comment->reports_count,
            'upvotes_count' => (int) $comment->upvotes_count,
            'downvotes_count' => (int) $comment->downvotes_count,
            'is_liked_by_current_user' => (bool) $comment->isLikedBy(auth()->user()),
            'is_reported_by_current_user' => (bool) $comment->isReportedBy(auth()->user()),
            'is_voted_by_current_user' => (bool) $comment->isVoteBy(auth()->user()),
            'replies_count' => (int) $comment->replies()->count(),
            'parent_id' => (string) $comment->parent_id ? 'comment_' . $comment->parent_id : null,
        ];
    }
}
