<?php

namespace App\Repositories\DatabaseImplementers;

use App\Enums\CommentStatus;
use App\Enums\LogEvents;
use App\Exceptions\FailedToSavedException;
use App\Facades\SetLog;
use App\Models\Comment;
use App\Repositories\Interfaces\CommentRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use Ramsey\Uuid\Uuid;

class CommentImpl implements CommentRepository
{

    public function addNewComment(array $comment, string $articleId, Authenticatable $commenter): ?Comment
    {
        $uuid = Uuid::uuid4()->toString();
        $comment = Comment::create([
            'id' => $uuid,
            'article_id' => $articleId,
            'user_id' => $commenter->id,
            'content' => $comment['content'],
            'parent_id' => $comment['parent_id'],
            'status' => CommentStatus::PUBLISHED->value
        ]);

        if (!$comment) {
            SetLog::withEvent(LogEvents::FETCHING)
                ->causedBy($commenter)
                ->performedOn($comment)
                ->withProperties([
                    'performedOn' => [
                        'class' => CommentImpl::class,
                        'method' => 'addNewComment',
                    ]
                ]);

            throw new FailedToSavedException(
                "Gagal menyimpan komentar. Harap coba lagi",
                [
                    'user' => $commenter,
                    'article_id' => $articleId,
                    'comment' => $comment,
                ],
                Comment::class
            );
        }

        return $comment;
    }
}
