<?php

namespace App\Facades;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Facade;

/**
 * @method static addNewCommenter(array $commenter)
 * @method static updateCommenter(Authenticatable $commenter, array $updateData)
 * @method static deleteCommenter(User $commenter)
 * @method static findCommenterById(string $id)
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



