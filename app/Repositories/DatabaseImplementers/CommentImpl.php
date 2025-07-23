<?php

namespace App\Repositories\DatabaseImplementers;

use App\Enums\CommentStatus;
use App\Enums\LogEvents;
use App\Enums\StatisticUserJobType;
use App\Exceptions\CommentNotFoundException;
use App\Exceptions\FailedToSavedException;
use App\Facades\SetLog;
use App\Jobs\StatisticUserJob;
use App\Models\Comment;
use App\Repositories\Interfaces\CommentRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class CommentImpl implements CommentRepository
{

    public function addNewComment(array $comment, string $articleId, Authenticatable $commenter): ?Comment
    {
        $uuid = Uuid::uuid4()->toString();
        $comment = Comment::create([
            'id' => $uuid,
            'article_id' => $articleId,
            'tenant_id' => "1",
            'user_id' => $commenter->id,
            'content' => $comment['content'],
            'parent_id' => $comment['parent_id'],
            'status' => CommentStatus::PUBLISHED->value
        ]);

        if (!$comment) {
            throw new FailedToSavedException(
                "Gagal menyimpan komentar. Harap coba lagi",
                [
                    'user' => $commenter,
                    'article_id' => $articleId,
                    'comment' => $comment,
                    'model' => Comment::class
                ]
            );
        }

        StatisticUserJob::dispatch($commenter->id, StatisticUserJobType::INCREMENT_COMMENTS_CREATED);

        return $comment;
    }

    public function findCommentByExternalArticleIdAndTenantId(string $externalArticleId, int $tenantId): ?Collection
    {
        $comments = DB::table('comments')
            ->where('article_id', $externalArticleId)
            ->where('tenant_id', $tenantId)
            ->get();

        if ($comments->isEmpty()) {
            SetLog::withEvent(LogEvents::FETCHING)
                ->causedBy($comments)
                ->performedOn($comments)
                ->withMessage("No comments yet")
                ->build();

            return $comments;
        }

        return $comments;
    }

    public function findRepliesByCommentId(string $commentId): ?Collection
    {
        $replies = Comment::where('parent_id', $commentId)->get();
        if ($replies->isEmpty()) {
            throw new CommentNotFoundException(
                "Komentar tidak ditemukan",
                [
                    'comment_id' => $commentId,
                    'model' => Comment::class
                ]
            );
        }

        return $replies;
    }

}
