<?php

namespace App\Facades;

use App\Models\User;
use Illuminate\Support\Facades\Facade;

/**
 * @method static addNewCommenter(array $user)
 * @method static deleteCommenter(User $user)
 * @method static findCommenterById(int $id)
 * @method static findCommenterByEmail(string $email)
 * @method static existsCommenterById(int $id)
 */
class AuthDo extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'AuthDo';
    }
}



