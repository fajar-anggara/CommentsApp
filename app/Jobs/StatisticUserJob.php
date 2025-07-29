<?php

namespace App\Jobs;

use App\Enums\QueueBucket;
use App\Enums\StatisticUserJobType;
use App\Models\StatisticUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class StatisticUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $userId,
        public StatisticUserJobType $jobType
    ) {
        $this->onQueue(QueueBucket::STATISTICS->value);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(function () {
            $statistic = StatisticUser::with('user')
                ->where('user_id', $this->userId)
                ->lockForUpdate()
                ->first();

            if (!$statistic) {
                $statistic = StatisticUser::create(['user_id' => $this->userId]);
            }

            match ($this->jobType) {
                StatisticUserJobType::INCREMENT_COMMENTS_CREATED => $this->syncTotalCommentsCreated(
                    'increment',
                    $statistic
                ),
                StatisticUserJobType::INITIALIZE_USER_STATISTICS => $this->initializeUserStatistics($statistic),
                StatisticUserJobType::INCREMENT_UPVOTE_GIVEN => $statistic->increment('total_upvote_comments'),
                StatisticUserJobType::INCREMENT_DOWNVOTE_GIVEN => $statistic->increment('total_downvote_comments'),
                StatisticUserJobType::INCREMENT_LIKES_GIVEN => $statistic->increment('total_liked_comments'),
                StatisticUserJobType::INCREMENT_REPORTS_MADE => $statistic->increment('total_report_times'),

                StatisticUserJobType::DECREMENT_COMMENTS_CREATED => $this->syncTotalCommentsCreated(
                    'decrement',
                    $statistic
                ),
                StatisticUserJobType::DECREMENT_UPVOTE_GIVEN => $statistic->decrement('total_upvote_comments'),
                StatisticUserJobType::DECREMENT_DOWNVOTE_GIVEN => $statistic->decrement('total_downvote_comments'),
                StatisticUserJobType::DECREMENT_LIKES_GIVEN => $statistic->decrement('total_liked_comments'),
                StatisticUserJobType::DECREMENT_REPORTS_MADE => $statistic->decrement('total_report_times'),
            };
        });
    }

    private function syncTotalCommentsCreated(string $operation,StatisticUser $statistic): void
    {
        $user = $statistic->user;

        if ($operation == 'increment')
        {
            $statistic->increment('total_comments_created');
            $user->increment('total_comments_created');
        } elseif ($operation == 'decrement')
        {
            $statistic->decrement('total_comments_created');
            $user->decrement('total_comments_created');
        }
    }

    /**
     * Initialize commenter statistics when commenter is created
     */
    private function initializeUserStatistics(StatisticUser $statistic): void
    {
        $statistic->save();
    }
}
