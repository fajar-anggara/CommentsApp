<?php

namespace App\Repositories\DatabaseImplementers;

use App\Enums\Badges;
use App\Enums\LogEvents;
use App\Enums\StatisticUserJobType;
use App\Exceptions\BadgeExceptions\BadgeNotFoundException;
use App\Exceptions\CommenterExceptions\CommenterNotFoundException;
use App\Exceptions\FailedToSavedException;
use App\Facades\SetLog;
use App\Jobs\StatisticUserJob;
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
            throw new BadgeNotFoundException(
                'Badge tidak ditemukan',
                [
                    'badge_name' => Badges::SIDER->value,
                    'model' => Badge::class
                ]
            );
        }

        $savedUser = User::create([
            'id' => Uuid::uuid4()->toString(),
            'name' => $commenter['name'],
            'email' => $commenter['email'],
            'password' => Hash::make($commenter['password']),
            'badge_id' => $badge->id,
        ]);
        if (!$savedUser) {
            throw new FailedToSavedException(
                "Kesalahan, silahkan coba lagi",
                [
                    'commenter' => $commenter,
                    'model' => User::class
                ]
            );
        }

        $savedUser->assignRole('commenter');
        StatisticUserJob::dispatch($savedUser->id, StatisticUserJobType::INITIALIZE_USER_STATISTICS);
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
            throw new FailedToSavedException(
                "Kesalahan, silahkan coba lagi",
                [
                    'name' => $commenter->name,
                    'email' => $commenter->email,
                    'model' => User::class
                ]
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
            throw new CommenterNotFoundException(
                "User tidak ditemukan, silahkan coba lagi",
                [
                    'id' => $id,
                    'model' => User::class
                ]
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
            throw new CommenterNotFoundException(
                "Email atau Nama tidak ditemukan",
                [
                    'email' => $email,
                    'model' => User::class
                ]
            );
        }

        return $fetched;
    }

    public function existsCommenterById(int $id): bool
    {
        return (bool) User::find($id);
    }
}
