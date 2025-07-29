<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

interface CommenterRepository
{
    public function store(
        array $commenterData
    ): User|bool;
    public function update(
        User $commenter,
        array $updateData
    ): User|bool;
    public function delete(
        string $commenterId
    ): bool;
    public function findById(
        string $commenterId
    ): User|bool;
    public function findByEmail(
        string $email
    ): User|bool;
}
