<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatisticUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_comments_created',
        'total_upvote_comments',
        'total_downvote_comments',
        'total_liked_comments',
        'total_report_times',
        'total_comments',
        'total_muted_times',
    ];

    protected function casts() : array
    {
        return [
            'total_comments_created' => 'int',
            'total_upvote_comments' => 'int',
            'total_downvote_comments' => 'int',
            'total_liked_comments' => 'int',
            'total_report_times' => 'int',
            'total_comments' => 'int',
            'total_muted_times' => 'int'
        ];
    }

    public function user(): belongsTo
    {
        return $this->belongsTo(User::class);
    }
}
