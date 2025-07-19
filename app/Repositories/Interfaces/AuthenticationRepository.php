<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface AuthenticationRepository
{
    public function addNewCommenter(array $user): User;

    public function deleteCommenter(User $user): bool;

    public function findCommenterById(int $id): ?User;

    public function existsCommenterById(int $id): bool;
}
