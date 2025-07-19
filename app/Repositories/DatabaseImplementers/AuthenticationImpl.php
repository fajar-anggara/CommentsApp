<?php

namespace App\Repositories\DatabaseImplementers;

use App\Models\User;
use App\Repositories\Interfaces\AuthenticationRepository;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class AuthenticationImpl implements AuthenticationRepository
{

    public function addNewCommenter(array $user): User
    {
        $user['id'] = Uuid::uuid4()->toString();
        $id = DB::table("users")->insertGetId($user);
        return User::query()->find($user['id'])->first();
    }

    public function deleteCommenter(User $user): bool
    {
        return false;
    }

    public function findCommenterById(int $id): ?User
    {
        return null;
    }

    public function existsCommenterById(int $id): bool
    {
        return false;
    }
}
