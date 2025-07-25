<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommentReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'comment_id',
        'user_id',
        'reason'
    ];

    public function comment(): BelongsTo {
        return $this->belongsTo(Comment::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
