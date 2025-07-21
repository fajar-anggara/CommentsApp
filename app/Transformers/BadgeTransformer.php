<?php

namespace App\Transformers;

use App\Models\Badge;
use League\Fractal\TransformerAbstract;

class BadgeTransformer extends TransformerAbstract
{
    public function transform(Badge $badge): array
    {
        return [
            'id'          => (string) $badge->id,
            'name'        => (string) $badge->name,
            'avatar'  => (string) $badge->avatar,
            'description' => (string) $badge->description,
            'created_at'  => (string) $badge->created_at->toIso8601String(),
            'updated_at'  => (string) $badge->updated_at->toIso8601String(),
        ];
    }
}
