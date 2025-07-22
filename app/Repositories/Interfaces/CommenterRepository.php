<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

interface CommenterRepository
{
    public function addNewCommenter(array $commenter): User;
    public function updateCommenter(User $commenter, array $updateData): User;
    public function deleteCommenter(User $commenter): bool;
    public function findCommenterById(string $id): ?User;
    public function findCommenterByEmail(string $email): ?User;
    public function existsCommenterById(int $id): bool;
}
