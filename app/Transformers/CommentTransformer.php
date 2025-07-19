<?php

namespace App\Transformers;

use App\Models\Comment;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class CommentTransformer extends TransformerAbstract
{
    public function transform(Comment $comment): array
    {
        return [
            'id'            => (string) $comment->id,
            'article_id'    => (int) $comment->article_id,
            'user_id'       => (string) $comment->user_id,
            'content'       => (string) $comment->content,
            'parent_id'     => (string) $comment->parent_id,
            'status'        => (string) $comment->status,
            'likes_count'   => (int) $comment->likes_count,
            'reports_count' => (int) $comment->reports_count,
            'upvotes_count' => (int) $comment->upvotes_count,
            'downvotes_count' => (int) $comment->downvotes_count,
            'created_at'    => (string) $comment->created_at->toIso8601String(),
            'updated_at'    => (string) $comment->updated_at->toIso8601String(),
        ];
    }
}
