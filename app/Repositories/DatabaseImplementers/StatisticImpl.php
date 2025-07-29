<?php

namespace App\Repositories\DatabaseImplementers;

use App\Models\StatisticUser;
use App\Repositories\Interfaces\StatisticRepository;

class StatisticImpl implements StatisticRepository
{

    public function initiate(string $commenterId): StatisticUser|bool
    {
        $statistic = StatisticUser::create([
            'user_id' => $commenterId,
            'total_comments_created' => 0,
            'total_upvote_comments' => 0,
            'total_downvote_comments' => 0,
            'total_liked_comments' => 0,
            'total_report_times' => 0,
            'total_comments' => 0,
            'total_muted_times' => 0,
        ]);

        if (!$statistic) {
            return false;
        }

        return $statistic;
    }
}
