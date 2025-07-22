<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasUuids, HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'article_id',
        'user_id',
        'tenant_id',
        'content',
        'parent_id',
        'status',
        'likes_count',
        'reports_count',
        'upvotes_count',
        'downvotes_count',
    ];

    protected function casts(): array
    {
        return [
            'likes_count' => 'integer',
            'reports_count' => 'integer',
            'upvotes_count' => 'integer',
            'downvotes_count' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo {
        return $this->belongsTo(self::class);
    }

    public function replies(): HasMany {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function likedBy(): HasMany {
        return $this->hasMany(CommentLike::class, 'comment_id');
    }

    public function upVotes(): HasMany {
        return $this->hasMany(CommentUpvote::class, 'comment_id');
    }

    public function downVotes(): HasMany {
        return $this->hasMany(CommentDownvote::class, 'comment_id');
    }

    public function reports(): HasMany {
        return $this->hasMany(CommentReport::class, 'comment_id');
    }
}
