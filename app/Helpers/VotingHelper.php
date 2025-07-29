<?php

namespace App\Helpers;

use App\Enums\LogEvents;
use App\Enums\StatisticUserJobType;
use App\Exceptions\CommentNotFoundException;
use App\Exceptions\FailedToSavedException;
use App\Facades\SetLog;
use App\Jobs\StatisticUserJob;
use App\Models\Comment;
use App\Models\CommentDownvote;
use App\Models\CommentUpvote;
use App\Repositories\Interfaces\CommentRepository;
use App\Repositories\Interfaces\VoteRepository;
use Illuminate\Support\Facades\DB;

class VotingHelper
{
    public function __construct(
        protected VoteRepository $voteRepository,
        protected CommentRepository $commentRepository
    ) {}

    public function upvote(string $commentId, string $commenterId): void
    {
        $voted = DB::transaction(function () use ($commentId, $commenterId) {
            $upvote = $this->voteRepository->findOrNewUpvote($commentId, $commenterId);
            $upvote->save();

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

            $comment->increment('upvotes_count');
            $comment->save();

            $comment->user->increment('total_upvote_acquired');
            $comment->user->save();

            StatisticUserJob::dispatch(
                $commenterId,
                StatisticUserJobType::INCREMENT_UPVOTE_GIVEN
            )->afterCommit();

            return $upvote;
        });

        if (!$voted) {
            throw new FailedToSavedException(
                "Gagal menyimpan upvote komentar. Harap coba lagi",
                [
                    'upvote' => $voted,
                    'model' => CommentUpvote::class
                ]
            );
        }

        SetLog::withEvent(LogEvents::CREATE_UPVOTE)
            ->performedOn($voted)
            ->withProperties([
                'performedOn' => [
                    'class' => CommentUpvote::class,
                    'method' => 'upvote'
                ]
            ])
            ->withMessage('Berhasil upvote')
            ->build();
    }

    public function downvote(string $commentId, string $commenterId): void
    {
        $voted = DB::transaction(function () use ($commentId, $commenterId) {
            $downvote = $this->voteRepository->findOrNewDownvote($commentId, $commenterId);
            $downvote->save();

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

            $comment->increment('downvotes_count');
            $comment->save();

            $comment->user->increment('total_downvote_acquired');
            $comment->user->save();

            StatisticUserJob::dispatch(
                $commenterId,
                StatisticUserJobType::INCREMENT_DOWNVOTE_GIVEN
            )->afterCommit();

            return $downvote;
        });

        if (!$voted) {
            throw new FailedToSavedException(
                "Gagal menyimpan downvote komentar. Harap coba lagi",
                [
                    'downvote' => $voted,
                    'model' => CommentDownvote::class
                ]
            );
        }

        SetLog::withEvent(LogEvents::CREATE_DOWNVOTE)
            ->performedOn($voted)
            ->withProperties([
                'performedOn' => [
                    'class' => CommentDownvote::class,
                    'method' => 'downvote'
                ]
            ])
            ->withMessage('Berhasil downvote')
            ->build();
    }

    public function remove(string $commentId, string $commenterId, string $upOrDownVote): void
    {
        $removed = DB::transaction(function () use ($commentId, $commenterId, $upOrDownVote) {
            $removed = $this->voteRepository->delete(
                $commentId,
                $commenterId,
                $upOrDownVote
            );

            if (!$removed) {
                throw new FailedToSavedException(
                    "Gagal menghapus vote komentar. Harap coba lagi",
                    [
                        'vote' => $removed,
                        'model' => $upOrDownVote == 'upvote' ? CommentUpvote::class : CommentDownvote::class
                    ]
                );
            }

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

            $comment->decrement($upOrDownVote == 'upvote' ? 'upvotes_count' : 'downvotes_count');
            $comment->save();

            $comment->user->decrement($upOrDownVote == 'upvote' ? 'total_upvote_acquired' : 'total_downvote_acquired');
            $comment->user->save();

            StatisticUserJob::dispatch(
                $commenterId,
                $upOrDownVote == 'upvote' ? StatisticUserJobType::DECREMENT_UPVOTE_GIVEN : StatisticUserJobType::DECREMENT_DOWNVOTE_GIVEN
            )->afterCommit();

        });

        SetLog::withEvent(LogEvents::REMOVE_VOTE)
            ->performedOn($removed)
            ->withProperties([
                'performedOn' => [
                    'class' => $upOrDownVote == 'upvote' ? CommentUpvote::class : CommentDownvote::class,
                    'method' => 'remove'
                ]
            ])
            ->withMessage('Berhasil menghapus vote')
            ->build();
    }
}
