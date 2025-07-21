<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\NullResource;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'comments',
        'badge',
        'statistics',
        'details'
    ];

    private $token;

    public function setToken(String $token = null): static
    {
        $this->token = $token;
        return $this;
    }

    public function transform(User $user): array
    {
        return [
            'id'                     => (string) $user->id,
            'name'                   => (string) $user->name,
            'avatar_url'             => (string) $user->avatar_url,
            'token'                  => $this->token,
        ];
    }

    public function includeDetails(User $user): Item
    {
        return $this->item($user, new DetailTransformer());
    }

    public function includeBadge(User $user): Item|NullResource
    {
        if ($user->badge) {
            return $this->item($user->badge, new BadgeTransformer());
        }

        return $this->null();
    }

    public function includeComments(User $user): Collection
    {
        return $this->collection($user->comments, new CommentTransformer());
    }

    public function includeStatistics(User $user): Item
    {
        return $this->item($user->statistics()->first(), new StatisticTransformer());
    }
}
