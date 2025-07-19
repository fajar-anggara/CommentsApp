<?php

namespace App\Transformers;

use App\Models\StatisticUser;
use League\Fractal\TransformerAbstract;

class StatisticTransformer extends TransformerAbstract
{
    public function transform(StatisticUser $statistic): array
    {
        return [
            'id'                     => (int) $statistic->id,
            'user_id'                => (string) $statistic->user_id,
            'total_comments_created' => (int) $statistic->total_comments_created,
            'total_upvote_comments'  => (int) $statistic->total_upVote_comments,
            'total_downvote_comments' => (int) $statistic->total_downVote_comments,
            'total_liked_comments'   => (int) $statistic->total_liked_comments,
            'total_report_times'     => (int) $statistic->total_report_times,
            'total_comments'         => (int) $statistic->total_comments,
            'total_muted_times'      => (int) $statistic->total_muted_times,
        ];
    }
}
