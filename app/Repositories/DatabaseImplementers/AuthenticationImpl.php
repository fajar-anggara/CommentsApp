<?php

namespace App\Repositories\DatabaseImplementers;

use App\Enums\Badges;
use App\Enums\LogEvents;
use App\Exceptions\NotFoundException;
use App\Exceptions\FailedToSavedException;
use App\Facades\SetLog;
use App\Models\Badge;
use App\Models\StatisticUser;
use App\Models\User;
use App\Repositories\Interfaces\AuthenticationRepository;
use Illuminate\Support\Arr;
use Ramsey\Uuid\Uuid;

class AuthenticationImpl implements AuthenticationRepository
{

    /**
     * @throws FailedToSavedException
     */
    public function addNewCommenter(array $user): User
    {
        $badge = Badge::where('name', Badges::SIDER->value)->first();
        if (!$badge) {
            SetLog::withEvent(LogEvents::FETCHING)
                ->causedBy(['badge_id' => Badges::SIDER->value])
                ->withMessage('Failed to fetch badge')
                ->build();

            throw new NotFoundException(
                "Kesalahan, silahkan coba lagi",
                ['badge_id' => $badge->id],
                Badge::class
            );
        }

        $user['id'] = Uuid::uuid4()->toString();
        $user['badge_id'] = $badge->id;
        $savedUser = User::create($user);
        if (!$savedUser) {
            SetLog::withEvent(LogEvents::STORING)
                ->causedBy(Arr::only($user, ['name', 'email']))
                ->withMessage('Failed to create new commenter')
                ->build();

            throw new FailedToSavedException(
                "Kesalahan, silahkan coba lagi",
                $user,
                User::class
            );
        }

        $savedUser->assignRole('commenter');
        $savedStatistic = StatisticUser::create([
            'user_id' => $savedUser->id,
        ]);
        if (!$savedStatistic) {
            SetLog::withEvent(LogEvents::STORING)
                ->causedBy(['user_id' => $savedUser->id])
                ->withMessage('Failed to create statistic for commenter')
                ->build();

            throw new FailedToSavedException(
                "Kesalahan, silahkan coba lagi",
                $user,
                User::class
            );
        }

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
            SetLog::withEvent(LogEvents::FETCHING_COMMENTER)
                ->withProperties([
                    "email" => $email,
                    "time" => now()
                ])
                ->withMessage('Commenter not found')
                ->build();

            throw new NotFoundException(
                "Email atau Nama tidak ditemukan",
                ['email' => $email],
                User::class
            );
        }

        return $fetched;
    }

    public function existsCommenterById(int $id): bool
    {
        return false;
    }
}
