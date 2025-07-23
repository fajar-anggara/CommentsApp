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
        'total_views',
        'total_comments',
        'total_active_comments',
        'total_reported_comments',
        'total_upVote_comments',
        'total_downVote_comments',
        'last_activity_at'
    ];

    protected function casts(): array
    {
       return [
        'total_views' => 'int',
        'total_comments' => 'int',
        'total_active_comments' => 'int',
        'total_reported_comments' => 'int',
        'total_upVote_comments' => 'int',
        'total_downVote_comments' => 'int',
        'last_activity_at' => 'datetime',
       ];
    }
}
