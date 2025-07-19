<?php

namespace App\Transformers;

use App\Models\StatisticArticle;
use League\Fractal\TransformerAbstract;

class BasicInfoTransformer extends TransformerAbstract
{

    public function transform(StatisticArticle $statistics): array
    {
        return [
            'article_id' => $statistics->article_id,
            'total_active_comments' => $statistics->total_active_comments,
            'total_reported_comments' => $statistics->total_reported_comments,
            'total_upVote_comments' => $statistics->total_upVote_comments,
            'total_downVote_comments' => $statistics->total_downVote_comments,
        ];
    }
}
