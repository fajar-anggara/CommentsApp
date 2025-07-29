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
    public function store(array $commenterData): User|bool
    {
        $commenter = User::create([
            'id' => Uuid::uuid4()->toString(),
            'name' => $commenterData['name'],
            'email' => $commenterData['email'],
            'password' => $commenterData['password'],
            'badge_id' => $commenterData['badge_id']
        ]);

        if (!$commenter) {
            return false;
        }

        return $commenter;
    }

    public function update(User $commenter, array $updateData): User|bool
    {
        $updated = $commenter->update($updateData);

        if (!$updated) {
            return false;
        }

        return $commenter;
    }


    public function delete(string $commenterId): bool
    {
        return User::find($commenterId)->delete();
    }

    public function findById(string $commenterId): User|bool
    {
        $commenter = User::find($commenterId);

        if (!$commenter)
        {
            return false;
        }

        return $commenter;
    }

    public function findByEmail(string $email): User|bool
    {
        $commenter = User::where('email', $email)->first();

        if (!$commenter) {
            return false;
        }

        return $commenter;
    }
}
