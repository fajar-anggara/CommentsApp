<?php

namespace App\Jobs;

use App\Enums\LogEvents;
use App\Enums\QueueBucket;
use App\Enums\UserSyncLikeJobType;
use App\Facades\SetLog;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class UserSyncLikeJob implements ShouldQueue
{
    use Queueable, Dispatchable;

    /**
     * Create a new job instance.
     */
    private string $userId;
    private UserSyncLikeJobType $jobType;
    public function __construct(string $userId, UserSyncLikeJobType $jobType)
    {
        $this->userId = $userId;
        $this->jobType = $jobType;
        $this->onQueue("statistics");
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = User::find($this->userId);

        match ($this->jobType) {
            UserSyncLikeJobType::USER_SYNC_LIKES_INCREMENT => $user->increment('total_likes_acquired'),
            UserSyncLikeJobType::USER_SYNC_LIKES_DECREMENT => $user->decrement('total_likes_acquired'),
        };

        SetLog::withEvent(LogEvents::FETCHING)
            ->causedBy($user)
            ->withProperties([
                'total_likes_acquired' => $user->total_likes_acquired,
            ])
            ->withMessage("User likes synced")
            ->build();

        $user->save();
    }
}
