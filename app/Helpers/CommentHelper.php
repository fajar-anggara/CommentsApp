<?php

namespace App\Helpers;

use App\Enums\LogEvents;
use App\Enums\StatisticUserJobType;
use App\Exceptions\CommentNotFoundException;
use App\Exceptions\FailedToSavedException;
use App\Facades\Article;
use App\Facades\SetLog;
use App\Facades\Tenant;
use App\Jobs\StatisticUserJob;
use App\Models\Comment;
use App\Models\CommentReport;
use App\Repositories\Interfaces\CommentRepository;
use App\Repositories\Interfaces\ReportRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CommentHelper
{
    public function __construct(
        protected CommentRepository $commentRepository,
        protected ReportRepository $reportRepository,
    ) {}

    public function ensureReport(array $context): CommentReport
    {
        return $this->reportRepository->findOrCreate([
            'comment_id' => $context['comment_id'],
            'user_id' => $context['user_id'],
        ]);
    }

    public function store(array $validated): Comment
    {
        Tenant::findById($validated['tenant_id']);

        $commenter = auth()->guard()->user();
        $comment = DB::transaction(function () use ($validated, $commenter) {
            $article = Article::findByContext(
                Arr::only($validated, [
                    'article_url',
                    'external_article_id',
                    'tenant_id'
                ]));

            $commentData['article_id'] = $article->id;
            $commentData['tenant_id'] = $validated['tenant_id'];
            $commentData['user_id'] = $commenter->id;
            $commentData['content'] = $validated['content'];
            $commentData['parent_id'] = $validated['parent_id'];
            $commentData['status'] = config('comments.comment_default_status_storing');

            $comment = $this->commentRepository->storeByCommenter($commentData, $commenter->id);
            if (!$comment) {
                throw new FailedToSavedException(
                    "Gagal menyimpan komentar. Harap coba lagi",
                    [
                        'comment' => $commentData,
                        'commenter' => $commenter,
                        'model' => Comment::class
                    ]
                );
            }

            return $comment;
        });

        SetLog::withEvent(LogEvents::STORING)
            ->causedBy($commenter)
            ->performedOn($comment)
            ->withProperties([
                'performedOn' => [
                    'class' => Article::class,
                    'method' => 'addComment',
                ]
            ])
            ->withMessage("Comment added successfully");

        return $comment;
    }

    public function getByArticleIdAndTenantId(
        string $articleId,
        int $tenantId
    ): Collection
    {
        $comments = $this->commentRepository->findByExternalArticleIdAndTenantId($articleId, $tenantId);

        if ($comments->isEmpty()) {
            throw new CommentNotFoundException(
                "Komentar tidak ditemukan",
                [
                    'article_id' => $articleId,
                    'tenant_id' => $tenantId,
                    'model' => Comment::class
                ],
                404
            );
        }

        SetLog::withEvent(LogEvents::FETCHING)
            ->withProperties([
                'causer' => $comments,
                'performedOn' => [
                    'class' => Article::class,
                    'method' => 'getComments',
                ]
            ])
            ->withMessage("Fetch comments successfully");

        return $comments;
    }

    public function getReplies(string $commentId): Collection
    {
        $comments = $this->commentRepository->findRepliesByParentId($commentId);
        if (!$comments) {
            throw new CommentNotFoundException(
                "Komentar tidak ditemukan",
                [
                    'comment_id' => $commentId,
                    'model' => Comment::class
                ],
                404
            );
        }

        SetLog::withEvent(LogEvents::FETCHING_COMMENTS)
            ->withProperties([
                'performedOn' => [
                    'class' => Comment::class,
                    'method' => 'getReplies',
                ]
            ])
            ->withMessage("Fetch replies successfully");

        return $comments;
    }

    public function addReport(string $commentId, string $commenterId, string $reason): void
    {
        $report = DB::transaction(function () use ($commentId, $commenterId, $reason) {
            $report = $this->ensureReport([
                'comment_id' => $commentId,
                'user_id' => $commenterId,
            ]);

            $report->reason = $reason;
            $report->save();

            if (!$report) {
                throw new FailedToSavedException(
                    "Gagal menyimpan report komentar. Harap coba lagi",
                    [
                        'report' => $report,
                        'model' => CommentReport::class
                    ]
                );
            }

            StatisticUserJob::dispatch($commenterId, StatisticUserJobType::DECREMENT_REPORTS_MADE)->afterCommit();

            return $report;
        });

        SetLog::withEvent(LogEvents::CREATE_REPORT)
            ->performedOn($report)
            ->withProperties([
                'performedOn' => [
                    'class' => Comment::class,
                    'method' => 'addReport',
                ]
            ])
            ->withMessage("Comment reported successfully");
    }

    public function deleteReport(string $commentId, string $commenterId): void
    {
        $report = DB::transaction(function () use ($commentId, $commenterId) {
            $report = $this->ensureReport([
                'comment_id' => $commentId,
                'user_id' => $commenterId,
            ]);

            $report->delete();

            if (!$report) {
                throw new FailedToSavedException(
                    "Gagal menghapus report komentar. Harap coba lagi",
                    [
                        'report' => $report,
                        'model' => CommentReport::class
                    ]
                );
            }

            StatisticUserJob::dispatch($commenterId, StatisticUserJobType::DECREMENT_REPORTS_MADE)->afterCommit();

            return $report;
        });

        SetLog::withEvent(LogEvents::DELETE_REPORT)
            ->performedOn($report)
            ->withProperties([
                'performedOn' => [
                    'class' => Comment::class,
                    'method' => 'deleteReport',
                ]
            ])
            ->withMessage("Comment report deleted successfully");
    }
}
