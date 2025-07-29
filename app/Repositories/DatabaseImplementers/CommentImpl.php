<?php

namespace App\Repositories\DatabaseImplementers;

use App\Models\Comment;
use App\Repositories\Interfaces\CommentRepository;
use Illuminate\Support\Collection;
use Ramsey\Uuid\Uuid;

class CommentImpl implements CommentRepository
{

    public function storeCommentByCommenter(
        array $comment,
        string $commenterId,
    ): Comment|bool
    {
        $uuid = Uuid::uuid4()->toString();
        $comment = Comment::create([
            'id' => $uuid,
            'article_id' => $comment['article_id'],
            'tenant_id' => $comment['tenant_id'],
            'user_id' => $commenterId,
            'content' => $comment['content'],
            'parent_id' => $comment['parent_id'],
            'status' => $comment['status']
        ]);

        if (!$comment) {
            return false;
        }

        return $comment;
    }

    public function findCommentByExternalArticleIdAndTenantId(
        string $externalArticleId,
        int $tenantId
    ): Collection|bool
    {
        $defaultStatus = config('comments.comment_default_status_fetching');
        $comments = Comment::where('article_id', $externalArticleId)
            ->where('tenant_id', $tenantId)
            ->where('status', $defaultStatus)
            ->get();

        if (!$comments) {
            return false;
        }

        return $comments;
    }

    public function findCommentById(
        string $commentId,
        string $with = null
    ): Comment|bool
    {
        $defaultStatus = config('comments.comment_default_status_fetching');
        if ($with == null) {
            $comment = Comment::where('id', $commentId)
                ->where('status', $defaultStatus)
                ->first();
        } else {
            $comment = Comment::with($with)
                ->where('id', $commentId)
                ->where('status', $defaultStatus)
                ->first();
        }

        if (!$comment) {
            return false;
        }

        return $comment;
    }

    public function findRepliesByParentId(
        string $parentId
    ): Collection|bool
    {
        $defaultStatus = config('comments.comment_default_status_fetching');
        $comments = Comment::where('parent_id', $parentId)
            ->where('status', $defaultStatus)
            ->get();

        if (!$comments) {
            return false;
        }

        return $comments;
    }


//    public function addNewComment(array $comment, string $articleId, Authenticatable $commenter): ?Comment
//    {
//        $uuid = Uuid::uuid4()->toString();
//        $comment = Comment::create([
//            'id' => $uuid,
//            'article_id' => $articleId,
//            'tenant_id' => "1",
//            'user_id' => $commenter->id,
//            'content' => $comment['content'],
//            'parent_id' => $comment['parent_id'],
//            'status' => CommentStatus::PUBLISHED->value
//        ]);
//
//        if (!$comment) {
//            throw new FailedToSavedException(
//                "Gagal menyimpan komentar. Harap coba lagi",
//                [
//                    'user' => $commenter,
//                    'article_id' => $articleId,
//                    'comment' => $comment,
//                    'model' => Comment::class
//                ]
//            );
//        }
//
//        StatisticUserJob::dispatch($commenter->id, StatisticUserJobType::INCREMENT_COMMENTS_CREATED);
//
//        return $comment;
//    }
//
//    public function findCommentByExternalArticleIdAndTenantId(string $externalArticleId, int $tenantId): ?Collection
//    {
//        $comments = DB::table('comments')
//            ->where('article_id', $externalArticleId)
//            ->where('tenant_id', $tenantId)
//            ->get();
//
//        if ($comments->isEmpty()) {
//            SetLog::withEvent(LogEvents::FETCHING)
//                ->withProperties([
//                    'causer' => $comments
//                ])
//                ->withMessage("No comments yet")
//                ->build();
//
//            return $comments;
//        }
//
//        return $comments;
//    }
//
//    public function findRepliesByCommentId(string $commentId): ?Collection
//    {
//        $replies = Comment::where('parent_id', $commentId)->get();
//        if ($replies->isEmpty()) {
//            throw new CommentNotFoundException(
//                "Komentar tidak ditemukan",
//                [
//                    'comment_id' => $commentId,
//                    'model' => Comment::class
//                ]
//            );
//        }
//
//        return $replies;
//    }
//
//    public function findCommentById(string $commentId): ?Comment
//    {
//        $comment = Comment::find($commentId);
//
//        if (!$comment) {
//            throw new CommentNotFoundException(
//                "Komentar tidak ditemukan",
//                [
//                    'comment_id' => $commentId,
//                    'model' => Comment::class
//                ]
//            );
//        }
//
//        return $comment;
//    }
//
//    public function addLikeByCommenter(string $commentId, Authenticatable $commenter): bool
//    {
//        return LikeHelper::addLike($commentId, $commenter);
//    }
//
//    public function deleteLikeByCommenter(string $commentId, Authenticatable $commenter): bool
//    {
//        return LikeHelper::removeLike($commentId, $commenter);
//    }
//
//
//    public function addReportByCommenter(string $commentId,Authenticatable $commenter,array $validated): bool
//    {
//        DB::transaction(function () use ($commentId, $commenter, $validated) {
//            $comment = Comment::find($commentId);
//            if (!$comment) {
//                throw new CommentNotFoundException(
//                    "Komentar tidak ditemukan",
//                    [
//                        'comment_id' => $commentId,
//                        'model' => Comment::class
//                    ]
//                );
//            }
//            $comment->reports_count = $comment->reports_count + 1;
//            $comment->save();
//
//            $isReported = CommentReport::where('comment_id', $commentId)->where('user_id', $commenter->id)->first   ();
//
//            if ($isReported) {
//                $isReported->reason = $validated['reason'];
//                $isReported->save();
//            } else {
//                $useReported = CommentReport::create([
//                    'comment_id' => $commentId,
//                    'user_id' => $commenter->id,
//                    'reason' => $validated['reason']
//                ]);
//
//                if (!$useReported) {
//                    throw new FailedToSavedException(
//                        "Gagal menyimpan laporan. Harap coba lagi",
//                        [
//                            'user' => $commenter,
//                            'comment' => $comment,
//                            'model' => CommentReport::class
//                        ]
//                    );
//                }
//            }
//        });
//
//        StatisticUserJob::dispatch($commenter->id, StatisticUserJobType::INCREMENT_REPORTS_MADE);
//
//        return true;
//    }
//
//    public function deleteReportByCommenter(string $commentId,Authenticatable $commenter): bool
//    {
//        $isReported = CommentReport::where('comment_id', $commentId)
//            ->where('user_id', $commenter->id)
//            ->first();
//
//        if (!$isReported) {
//            return true;
//        }
//
//        DB::transaction(function () use ($commentId, $commenter) {
//            $comment = Comment::find($commentId);
//            if (!$comment) {
//                throw new CommentNotFoundException(
//                    "Komentar tidak ditemukan",
//                    [
//                        'comment_id' => $commentId,
//                        'model' => Comment::class
//                    ]
//                );
//            }
//
//            $comment->reports_count = $comment->reports_count - 1;
//            $comment->save();
//
//            $commentReport = DB::table('comment_reports')
//                ->where('comment_id', $commentId)
//                ->where('user_id', $commenter->id)
//                ->delete();
//            if (!$commentReport) {
//                throw new FailedToSavedException(
//                    "Gagal menghapus laporan. Harap coba lagi",
//                    [
//                        'user' => $commenter,
//                        'comment' => $commentReport,
//                        'model' => CommentReport::class
//                    ]
//                );
//            }
//        });
//
//        StatisticUserJob::dispatch($commenter->id, StatisticUserJobType::DECREMENT_REPORTS_MADE);
//
//        return true;
//    }
//
//    public function addVoteByCommenter(string $commentId, Authenticatable $commenter): bool
//    {
//        return VotingHelper::addUpvote($commentId, $commenter);
//    }
//
//    public function addDownvoteByCommenter(string $commentId, Authenticatable $commenter): bool
//    {
//        return VotingHelper::addDownvote($commentId, $commenter);
//    }
//
//    public function deleteUpvoteByCommenter(string $commentId, Authenticatable $commenter): bool
//    {
//        return VotingHelper::removeUpvote($commentId, $commenter);
//    }
//
//    public function deleteDownvoteByCommenter(string $commentId, Authenticatable $commenter): bool
//    {
//        return VotingHelper::removeDownvote($commentId, $commenter);
//    }
}
