<?php

namespace App\Jobs;

use App\Enums\QueueBucket;
use App\Enums\StatisticArticleJobType;
use App\Models\Article;
use App\Models\StatisticArticle;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class addStatisticArticle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $articleId,
        public StatisticArticleJobType $jobType
    ) {
        $this->onQueue(QueueBucket::STATISTICS->value);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(function () {
            $statistic = StatisticArticle::where('article_id', $this->articleId)->lockForUpdate()->first();

            if (!$statistic) {
                $statistic = StatisticArticle::create(['article_id' => $this->articleId]);
            }

            match ($this->jobType) {
                StatisticArticleJobType::INCREMENT_VIEWS => $statistic->increment('total_views'),
                StatisticArticleJobType::INCREMENT_COMMENTS_COUNT => $statistic->increment('total_comments'),
                StatisticArticleJobType::DECREMENT_COMMENTS_COUNT => $statistic->decrement('total_comments'),
                StatisticArticleJobType::UPDATE_LAST_ACTIVITY => $this->updateLastActivity($statistic),
            };
        });
    }

    /**
     * Update last activity timestamp
     */
    private function updateLastActivity(StatisticArticle $statistic): void
    {
        $statistic->last_activity_at = now();
        $statistic->save();
    }
}
