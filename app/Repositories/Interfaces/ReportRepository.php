<?php

namespace App\Repositories\Interfaces;

use App\Models\CommentReport;

interface ReportRepository
{
    public function findOrCreate(
        array $context,
    ): CommentReport|bool;
}
