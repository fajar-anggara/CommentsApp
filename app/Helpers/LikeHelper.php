<?php

namespace App\Helpers;

use App\Enums\LogEvents;
use App\Enums\StatisticUserJobType;
use App\Enums\UserSyncLikeJobType;
use App\Exceptions\CommentNotFoundException;
use App\Exceptions\FailedToSavedException;
use App\Facades\SetLog;
use App\Jobs\StatisticUserJob;
use App\Jobs\UserSyncLikeJob;
use App\Models\Comment;
use App\Models\CommentLike;
use App\Repositories\Interfaces\CommentRepository;
use App\Repositories\Interfaces\LikeRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;

class LikeHelper
{
    public function __construct(
        protected LikeRepository $likeRepository,
        protected CommentRepository $commentRepository,
    ) {}
    public function add(string $commentId, string $commenterId): void
    {
        $isLiked = $this->likeRepository->findByContext([$commentId, $commenterId]);

        if ($isLiked) {
            // Commenter sudah like
            return;
        }

        $comment =DB::transaction(function () use ($commentId, $commenterId) {
            $comment = $this->commentRepository->findById(
                $commentId,
                "user"
            );

            if (!$comment) {
                throw new CommentNotFoundException(
                    "Komentar tidak ditemukan",
                    [
                        'comment_id' => $commentId,
                        'model' => Comment::class
                    ]
                );
            }

            $comment->increment('likes_count');
            $comment->save();

            $commentIsLiked = $this->likeRepository->store([
                'comment_id' => $commentId,
                'user_id' => $commenterId
            ]);

            if (!$commentIsLiked) {
                throw new FailedToSavedException(
                    "Gagal menyimpan like. Harap coba lagi",
                    [
                        'user' => $comment->user,
                        'comment' => $comment,
                        'model' => CommentLike::class
                    ]
                );
            }

            UserSyncLikeJob::dispatch($comment->user->id, UserSyncLikeJobType::USER_SYNC_LIKES_INCREMENT)->afterCommit();

            return $comment;
        });

        StatisticUserJob::dispatch($commenterId, StatisticUserJobType::INCREMENT_LIKES_GIVEN);

        SetLog::withEvent(LogEvents::STORING_lIKE)
            ->performedOn($comment)
            ->withProperties([
                'performedOn' => [
                    'class' => CommentLike::class,
                    'method' => 'store'
                ]
            ])
            ->withMessage('Berhasil menyimpan like')
            ->build();
    }

    public function remove(string $commentId, string $commenterId): void
    {
        $isLiked = $this->likeRepository->findByContext([$commentId, $commenterId]);

        if (!$isLiked) {
            // Commenter belum like
            return;
        }

        DB::transaction(function () use ($commentId, $commenterId, $isLiked) {
            $comment = $this->commentRepository->findById(
                $commentId,
                "user"
            );

            if (!$comment) {
                throw new CommentNotFoundException(
                    "Komentar tidak ditemukan",
                    [
                        'comment_id' => $commentId,
                        'model' => Comment::class
                    ]
                );
            }

            $comment->decrement('likes_count');
            $comment->save();
            $this->likeRepository->delete($isLiked->id);

            UserSyncLikeJob::dispatch($comment->user->id, UserSyncLikeJobType::USER_SYNC_LIKES_DECREMENT)->afterCommit();
        });

        StatisticUserJob::dispatch($commenterId, StatisticUserJobType::DECREMENT_LIKES_GIVEN);
    }
}
