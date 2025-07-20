<?php

namespace App\Repositories\DatabaseImplementers;

use App\Exceptions\NotFoundException;
use App\Exceptions\FailedToSavedException;
use App\Facades\SetLog;
use App\Models\User;
use App\Repositories\Interfaces\AuthenticationRepository;
use Ramsey\Uuid\Uuid;

class AuthenticationImpl implements AuthenticationRepository
{

    /**
     * @throws FailedToSavedException
     */
    public function addNewCommenter(array $user): User
    {
        $user['id'] = Uuid::uuid4()->toString();
        $savedUser = User::create($user);
        if (!$savedUser) {
            SetLog::withEvent('Register Commenter')
                ->withProperties([
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'time' => now()
                ])
                ->withMessage('Failed to create new commenter')
                ->build();

            throw new FailedToSavedException(
                "Kesalahan, silahkan coba lagi",
                $user,
                $savedUser
            );
        }
        $savedUser->assignRole('commenter');

        return $savedUser;
    }

    public function deleteCommenter(User $user): bool
    {
        return false;
    }

    public function findCommenterById(int $id): ?User
    {
        return null;
    }

    /**
     * @throws NotFoundException
     */
    public function findCommenterByEmail(string $email): ?User
    {
        $fetched = User::where('email', $email)->first();
        if (!$fetched) {
            SetLog::withEvent("find_commenter")
                ->withProperties([
                    "email" => $email,
                    "time" => now()
                ])
                ->withMessage('Commenter not found')
                ->build();

            throw new NotFoundException($email);
        }

        return $fetched;
    }

    public function existsCommenterById(int $id): bool
    {
        return false;
    }
}
