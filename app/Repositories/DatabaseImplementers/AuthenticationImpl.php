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
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;
use Ramsey\Uuid\Uuid;

class AuthenticationImpl implements AuthenticationRepository
{

    /**
     * @throws FailedToSavedException
     */
    public function addNewCommenter(array $commenter): User
    {
        $badge = Badge::where('name', Badges::SIDER->value)->first();
        if (!$badge) {
            SetLog::withEvent(LogEvents::FETCHING)
                ->causedBy(['badge_id' => Badges::SIDER->value])
                ->performedOn(AuthenticationImpl::class)
                ->withMessage('Failed to fetch badge')
                ->build();

            throw new NotFoundException(
                "Kesalahan, silahkan coba lagi",
                ['badge_id' => $badge->id],
                Badge::class
            );
        }

        $commenter['id'] = Uuid::uuid4()->toString();
        $commenter['badge_id'] = $badge->id;
        $savedUser = User::create($commenter);
        if (!$savedUser) {
            SetLog::withEvent(LogEvents::STORING)
                ->causedBy(Arr::only($commenter, ['name', 'email']))
                ->performedOn(AuthenticationImpl::class)
                ->withMessage('Failed to create new commenter')
                ->build();

            throw new FailedToSavedException(
                "Kesalahan, silahkan coba lagi",
                $commenter,
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
                ->performedOn(AuthenticationImpl::class)
                ->withMessage('Failed to create statistic for commenter')
                ->build();

            throw new FailedToSavedException(
                "Kesalahan, silahkan coba lagi",
                $commenter,
                User::class
            );
        }

        return $savedUser;
    }

    public function updateCommenter(User $commenter, array $updateData): User
    {
        if ($updateData['name'] != null)
            $commenter->name = $updateData['name'];

        if ($updateData['email'] != null)
            $commenter->email = $updateData['email'];

        if ($updateData['email_verified_at'] != null)
            $commenter->email_verified_at = $updateData['email_verified_at'];

        if ($updateData['avatar_url'] != null)
            $commenter->avatar_url = $updateData['avatar_url'];

        if ($updateData['bio'] != null)
            $commenter->bio = $updateData['bio'];

        $savedUser = $commenter->save();
        if (!$savedUser) {
            SetLog::withEvent(LogEvents::UPDATE)
                ->causedBy([
                    'name' => $commenter->name,
                    'email' => $commenter->email
                ])
                ->performedOn(AuthenticationImpl::class)
                ->withMessage('Failed to update commenter')
                ->build();

            throw new FailedToSavedException(
                "Kesalahan, silahkan coba lagi",
                [
                    'name' => $commenter->name,
                    'email' => $commenter->email
                ],
                User::class
            );
        }

        return $commenter;
    }


    public function deleteCommenter(User $user): bool
    {
        return false;
    }

    public function findCommenterById(string $id): ?User
    {
        $commenter = User::find($id);
        if (!$commenter) {
            SetLog::withEvent(LogEvents::FETCHING_COMMENTER)
                ->performedOn(AuthenticationImpl::class)
                ->withMessage('Commenter not found')
                ->build();

            throw new NotFoundException(
                "Kesalahan, silahkan coba lagi",
                ['id' => $id],
                AuthenticationImpl::class
            );
        }

        return $commenter;
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
