<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool upvote(string $commentId, string $commenterId)
 * @method static bool downvote(string $commentId, string $commenterId)
 * @method static bool remove(string $commentId, string $commenterId, string $upOrDownVote)
 *
 * @see \App\Helpers\VotingHelper
 */
class Voting extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'Voting';
    }
}
