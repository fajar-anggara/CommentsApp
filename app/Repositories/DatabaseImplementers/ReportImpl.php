<?php

namespace App\Repositories\DatabaseImplementers;

use App\Models\CommentReport;
use App\Repositories\Interfaces\ReportRepository;

class ReportImpl implements ReportRepository
{
    public function findOrCreate(array $context): CommentReport
    {
        return CommentReport::firstOrCreate([
            'comment_id' => $context['comment_id'],
            'user_id' => $context['user_id'],
        ]);
    }
}
