<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class DetailTransformer extends TransformerAbstract
{
    public function transform(User $user): array
    {
        return [
            'email'                  => (string) $user->email,
            'email_verified_at'      => $user->email_verified_at ? $user->email_verified_at->toIso8601String() : null,
            'bio'                    => (string) $user->bio,
            'total_comments_created' => (int) $user->total_comments_created,
            'total_likes_acquired'   => (int) $user->total_likes_acquired,
            'is_muted'               => (bool) $user->is_muted,
            'is_banned'              => (bool) $user->is_banned,
            'created_at'             => (string) $user->created_at->toIso8601String(),
        ];
    }
}
