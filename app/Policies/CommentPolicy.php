<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can like a comment.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function addLike(User $user)
    {
        return true;
    }
}
