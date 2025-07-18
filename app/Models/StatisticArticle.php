<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatisticArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'total_active_comments',
        'total_reported_comments',
        'total_upVote_comments',
        'total_downVote_comments'
    ];

    protected function casts(): array
    {
       return [
        'total_active_comments' => 'int',
        'total_reported_comments' => 'int',
        'total_upVote_comments' => 'int',
        'total_downVote_comments' => 'int',
       ];
    }
}
