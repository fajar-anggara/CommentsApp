<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool add(string $commentId, string $commenterId)
 * @method static bool remove(string $commentId, string $commenterId)
 *
 * @see \App\Helpers\LikeHelper
 */
class Like extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'Like';
    }
}
