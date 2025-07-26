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
use App\Models\CommentLike;
use App\Models\CommentReport;
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
                ->withProperties([
                    'causer' => $comments
                ])
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

    public function findCommentById(string $commentId): ?Comment
    {
        $comment = Comment::find($commentId);

        if (!$comment) {
            throw new CommentNotFoundException(
                "Komentar tidak ditemukan",
                [
                    'comment_id' => $commentId,
                    'model' => Comment::class
                ]
            );
        }

        return $comment;
    }

    public function addLikeByCommenter(string $commentId, Authenticatable $commenter): bool
    {
        $isLiked = CommentLike::where('comment_id', $commentId)
            ->where('user_id', $commenter->id)
            ->exists();

        if ($isLiked) {
            return true;
        }

        DB::transaction(function () use ($commentId, $commenter) {
            $comment = Comment::find($commentId);
            if (!$comment) {
                throw new CommentNotFoundException(
                    "Komentar tidak ditemukan",
                    [
                        'comment_id' => $commentId,
                        'model' => Comment::class
                    ]
                );
            }
            $comment->likes_count = $comment->likes_count + 1;
            $comment->save();

            $useLiked = CommentLike::updateOrCreate([
                'comment_id' => $commentId,
                'user_id' => $commenter->id
            ]);
            if (!$useLiked) {
                throw new FailedToSavedException(
                    "Gagal menyimpan like. Harap coba lagi",
                    [
                        'user' => $commenter,
                        'comment' => $comment,
                        'model' => CommentLike::class
                    ]
                );
            }
        });

        StatisticUserJob::dispatch($commenter->id, StatisticUserJobType::INCREMENT_LIKES_GIVEN);

        return true;
    }

    public function deleteLikeByCommenter(string $commentId, Authenticatable $commenter): bool
    {
        $isLiked = CommentLike::where('comment_id', $commentId)
            ->where('user_id', $commenter->id)
            ->exists();

        if (!$isLiked)
        {
            return true;
        }

        DB::transaction(function () use ($commentId, $commenter) {
            $commentLike = DB::table('comment_likes')
                ->where('comment_id', $commentId)
                ->where('user_id', $commenter->id)
                ->delete();

            if (!$commentLike) {
                throw new FailedToSavedException(
                    "Gagal menghapus like. Harap coba lagi",
                    [
                        'user' => $commenter,
                        'comment' => $commentLike,
                        'model' => CommentLike::class
                    ]
                );
            }

            $comment = Comment::find($commentId);
            if (!$comment) {
                throw new CommentNotFoundException(
                    "Komentar tidak ditemukan",
                    [
                        'comment_id' => $commentId,
                        'model' => Comment::class
                    ]
                );
            }

            $comment->likes_count = $comment->likes_count - 1;
            $comment->save();
        });

        StatisticUserJob::dispatch($commenter->id, StatisticUserJobType::DECREMENT_LIKES_GIVEN);

        return true;
    }


    public function addReportByCommenter(string $commentId,Authenticatable $commenter,array $validated): bool
    {
        DB::transaction(function () use ($commentId, $commenter, $validated) {
            $comment = Comment::find($commentId);
            if (!$comment) {
                throw new CommentNotFoundException(
                    "Komentar tidak ditemukan",
                    [
                        'comment_id' => $commentId,
                        'model' => Comment::class
                    ]
                );
            }
            $comment->reports_count = $comment->reports_count + 1;
            $comment->save();

            $isReported = CommentReport::where('comment_id', $commentId)->where('user_id', $commenter->id)->first   ();

            if ($isReported) {
                $isReported->reason = $validated['reason'];
                $isReported->save();
            } else {
                $useReported = CommentReport::create([
                    'comment_id' => $commentId,
                    'user_id' => $commenter->id,
                    'reason' => $validated['reason']
                ]);

                if (!$useReported) {
                    throw new FailedToSavedException(
                        "Gagal menyimpan laporan. Harap coba lagi",
                        [
                            'user' => $commenter,
                            'comment' => $comment,
                            'model' => CommentReport::class
                        ]
                    );
                }
            }
        });

        StatisticUserJob::dispatch($commenter->id, StatisticUserJobType::INCREMENT_REPORTS_MADE);

        return true;
    }

    public function deleteReportByCommenter(string $commentId,Authenticatable $commenter): bool
    {
        $isReported = CommentReport::where('comment_id', $commentId)->where('user_id', $commenter->id)->first();
        if (!$isReported) {
            return true;
        }

        DB::transaction(function () use ($commentId, $commenter) {
            $comment = Comment::find($commentId);
            if (!$comment) {
                throw new CommentNotFoundException(
                    "Komentar tidak ditemukan",
                    [
                        'comment_id' => $commentId,
                        'model' => Comment::class
                    ]
                );
            }

            $comment->reports_count = $comment->reports_count - 1;
            $comment->save();

            $commentReport = DB::table('comment_reports')
                ->where('comment_id', $commentId)
                ->where('user_id', $commenter->id)
                ->delete();
            if (!$commentReport) {
                throw new FailedToSavedException(
                    "Gagal menghapus laporan. Harap coba lagi",
                    [
                        'user' => $commenter,
                        'comment' => $commentReport,
                        'model' => CommentReport::class
                    ]
                );
            }
        });

        StatisticUserJob::dispatch($commenter->id, StatisticUserJobType::DECREMENT_REPORTS_MADE);

        return true;
    }
}
