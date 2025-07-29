<?php

namespace App\Helpers;

use App\Enums\Badges;
use App\Enums\LogEvents;
use App\Enums\StatisticUserJobType;
use App\Exceptions\CommenterExceptions\CommenterNotFoundException;
use App\Exceptions\CommenterExceptions\WrongCredentialsException;
use App\Exceptions\CommentNotFoundException;
use App\Exceptions\FailedToSavedException;
use App\Facades\SetLog;
use App\Jobs\StatisticUserJob;
use App\Models\Badge;
use App\Models\User;
use App\Repositories\Interfaces\CommenterRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class AuthHelper
{
    public function __construct(
        protected CommenterRepository $commenterRepository
    ){}

    private function ensureDefaultBadge(): Badge
    {
        return Badge::firstOrCreate([
            'name' => Badges::SIDER->value
        ], [
            'id' => Uuid::uuid4()->toString(),
            'description' => 'Si cuman baca',
        ]);
    }

    private function assignRole(User $commenter, string $role = 'commenter'): void
    {
        $commenter->assignRole($role);
    }

    private function generateToken(User $commenter, string $tokenName): string
    {
        return $commenter->createToken($tokenName)->plainTextToken;
    }

    public function register(array $commenterData): array
    {
        $defaultBadge = $this->ensureDefaultBadge();

        $commenter = DB::transaction(function () use ($commenterData, $defaultBadge) {
            $commenterData['password'] = Hash::make($commenterData['password']);
            $commenterData['badge_id'] = $defaultBadge->id;
            $commenter = $this->commenterRepository->store(
                Arr::only($commenterData, [
                    'name',
                    'email',
                    'password',
                    'badge_id'
                ])
            );

            if (!$commenter) {
                throw new FailedToSavedException(
                    "Kesalahan, silahkan coba lagi",
                    [
                        'commenter' => Arr::except($commenterData, ['password']),
                        'model' => User::class
                    ]
                );
            }

            StatisticUserJob::dispatch($commenter->id, StatisticUserJobType::INITIALIZE_USER_STATISTICS)->afterCommit();

            return $commenter;
        });

        $this->assignRole(
            $commenter,
            'commenter'
        );

        SetLog::withEvent(LogEvents::REGISTER)
            ->causedBy($commenter)
            ->withProperties([
                'badges' => Badges::SIDER->value,
                'role' => 'commenter',
                'performedOn' => [
                    'class' => AuthHelper::class,
                    'method' => 'store'
                ]
            ])
            ->withMessage('Register a new commenter successfully')
            ->build();

        return [
            'data' => $commenter,
            'token' => $this->generateToken(
                $commenter,
                'register'
            )
        ];
    }

    public function login(array $commenterData): array
    {
        $commenter = $this->commenterRepository->findByEmail($commenterData['email']);
        if (!$commenter) {
            throw new CommentNotFoundException(
                "User tidak ditemukan, silahkan coba lagi",
                [
                    'email' => $commenterData['email'],
                    'model' => User::class
                ]
            );
        }

        if (!Hash::check($commenterData['password'], $commenter->password)) {
            throw new WrongCredentialsException(
                "Password atau email salah, silahkan coba lagi",
                [
                    'email' => $commenterData['email'],
                    'model' => User::class
                ]
            );
        }

        $commenter->tokens()->delete();

        SetLog::withEvent(LogEvents::LOGIN)
            ->causedBy($commenter)
            ->withProperties([
                'performedOn' => [
                    'class' => AuthHelper::class,
                    'method' => 'login'
                ]
            ])
            ->withMessage('Login a commenter successfully')
            ->build();

        return [
            'data' => $commenter,
            'token' => $this->generateToken(
                $commenter,
                'login'
            )
        ];
    }

    public function update(string $commenterId, array $updateData): array
    {
        $updated = DB::transaction(function () use ($commenterId, $updateData) {
            $commenter = $this->commenterRepository->findById($commenterId);
            if (!$commenter) {
                throw new CommenterNotFoundException(
                    "User tidak ditemukan, silahkan coba lagi",
                    [
                        'id' => $commenterId,
                        'model' => User::class
                    ]
                );
            }

            $data['name'] = $updateData['name'] ?? $commenter->name;
            $data['email'] = $updateData['email'] ?? $commenter->email;
            $data['avatar_url'] = $updateData['avatar_url'] ?? $commenter->avatar_url;
            $data['bio'] = $updateData['bio'] ?? $commenter->bio;

            $update = $this->commenterRepository->update(
                $commenter,
                $data
            );

            if (!$update) {
                throw new FailedToSavedException(
                    "Kesalahan, silahkan coba lagi",
                    [
                        'name' => $commenter->name,
                        'email' => $commenter->email,
                        'model' => User::class
                    ]
                );
            }

            return $update;
        });

        $updated->tokens()->delete();
        $token = $this->generateToken(
            $updated,
            'update'
        );

        SetLog::withEvent(LogEvents::UPDATE)
            ->causedBy($updated)
            ->withProperties([
                'performedOn' => [
                    'class' => AuthHelper::class,
                    'method' => 'update'
                ]
            ])
            ->withMessage('Update commenter successfully')
            ->build();

        return [
            'data' => $updated,
            'token' => $token
        ];
    }

    public function delete(string $commenterId): void
    {
        $commenter = $this->commenterRepository->delete($commenterId);

        SetLog::withEvent(LogEvents::DELETE)
            ->causedBy($commenter)
            ->withProperties([
                'performedOn' => [
                    'class' => AuthHelper::class,
                    'method' => 'delete'
                ]
            ])
            ->withMessage('Delete commenter successfully')
            ->build();
    }
}
