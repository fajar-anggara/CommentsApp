<?php

namespace App\Facades;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Facade;

/**
 * @method static register(array $commenter)
 * @method static login(array $commenter)
 * @method static update(Authenticatable $commenter, array $updateData)
 * @method static delete(User $commenter)
 * @method static findById(string $id)
 * @method static findByEmail(string $email)
 *
 * @see \App\Helpers\AuthHelper
 */
class Auth extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'Auth';
    }
}



