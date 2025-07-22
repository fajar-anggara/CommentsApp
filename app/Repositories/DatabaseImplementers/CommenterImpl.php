<?php

namespace App\Repositories\DatabaseImplementers;

use App\Enums\Badges;
use App\Enums\LogEvents;
use App\Exceptions\BadgeExceptions\BadgeNotFoundException;
use App\Exceptions\CommenterExceptions\CommenterNotFoundException;
use App\Exceptions\FailedToSavedException;
use App\Facades\SetLog;
use App\Models\Badge;
use App\Models\StatisticUser;
use App\Models\User;
use App\Repositories\Interfaces\CommenterRepository;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class CommenterImpl implements CommenterRepository
{

    /**
     * @throws FailedToSavedException
     */
    public function addNewCommenter(array $commenter): User
    {
        $badge = Badge::where('name', Badges::SIDER->value)->first();
        if (!$badge) {
            SetLog::withEvent(LogEvents::FETCHING)
                ->withProperties([
                    'causer' => ['badge_id' => Badges::SIDER->value],
                    'performedOn' => [
                        'class' => CommenterImpl::class,
                        'method' => 'addNewCommenter'
                    ]
                ])
                ->withMessage('Failed to fetch badge')
                ->build();

            throw new BadgeNotFoundException();
        }
        SetLog::withEvent(LogEvents::FETCHING)
            ->causedBy($badge)
            ->performedOn($badge)
            ->withProperties([
                'performedOn' => [
                    'class' => CommenterImpl::class,
                    'method' => 'addNewCommenter'
                ]
            ])
            ->withMessage('Fetched badge')
            ->build();

        $savedUser = User::create([
            'id' => Uuid::uuid4()->toString(),
            'name' => $commenter['name'],
            'email' => $commenter['email'],
            'password' => Hash::make($commenter['password']),
            'badge_id' => $badge->id,
        ]);
        if (!$savedUser) {
            SetLog::withEvent(LogEvents::STORING)
                ->withProperties([
                    'causer' => $commenter,
                    'performedOn' => [
                        'class' => CommenterImpl::class,
                        'method' => 'addNewCommenter'
                    ]
                ])
                ->withMessage('Failed to create commenter')
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
                ->withProperties([
                    'causer' => ['user_id' => $savedUser->id],
                    'performedOn' => [
                        'class' => CommenterImpl::class,
                        'method' => 'addNewCommenter'
                    ]
                ])
                ->withMessage('Failed to create statistic for commenter')
                ->build();

            throw new FailedToSavedException(
                "Kesalahan, silahkan coba lagi",
                $commenter,
                StatisticUser::class
            );
        }
        SetLog::withEvent(LogEvents::STORING)
            ->causedBy($savedStatistic)
            ->performedOn($savedStatistic)
            ->withProperties([
                'performedOn' => [
                    'class' => CommenterImpl::class,
                    'method' => 'addNewCommenter'
                ]
            ])
            ->withMessage('Created statistic for commenter')
            ->build();

        return $savedUser;
    }

    public function updateCommenter(User $commenter, array $updateData): User
    {
        if (isset($updateData['name'])) {
            $commenter->name = $updateData['name'];
        }

        if (isset($updateData['email'])) {
            $commenter->email = $updateData['email'];
        }

        if (isset($updateData['email_verified_at'])) {
            $commenter->email_verified_at = $updateData['email_verified_at'];
        }

        if (isset($updateData['avatar_url'])) {
            $commenter->avatar_url = $updateData['avatar_url'];
        }

        if (isset($updateData['bio'])) {
            $commenter->bio = $updateData['bio'];
        }

        $savedUser = $commenter->save();
        if (!$savedUser) {
            SetLog::withEvent(LogEvents::UPDATE)
                ->withProperties([
                    'causer' => [
                        'name' => $commenter->name,
                        'email' => $commenter->email
                    ],
                    'performedOn' => [
                        'class' => CommenterImpl::class,
                        'method' => 'updateCommenter'
                    ]
                ])
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
        $user->statistics()->delete();

        return $user->delete();
    }

    public function findCommenterById(string $id): ?User
    {
        $commenter = User::find($id);
        if (!$commenter) {
            SetLog::withEvent(LogEvents::FETCHING)
                ->withProperties([
                    'performedOn' => [
                        'class' => CommenterImpl::class,
                        'method' => 'findCommenterById'
                    ]
                ])
                ->withMessage('Commenter not found')
                ->build();

            throw new CommenterNotFoundException(
                "User tidak ditemukan, silahkan coba lagi",
                ['id' => $id],
                CommenterImpl::class
            );
        }

        return $commenter;
    }

    /**
     * @throws CommenterNotFoundException
     */
    public function findCommenterByEmail(string $email): ?User
    {
        $fetched = User::where('email', $email)->first();
        if (!$fetched) {
            SetLog::withEvent(LogEvents::FETCHING)
                ->withProperties([
                    "email" => $email,
                    "time" => now(),
                    'performedOn' => [
                        'class' => CommenterImpl::class,
                        'method' => 'findCommenterByEmail'
                    ]
                ])
                ->withMessage('Commenter not found')
                ->build();

            throw new CommenterNotFoundException(
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
