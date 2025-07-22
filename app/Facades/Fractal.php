<?php

namespace App\Facades;

//use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * @method static useCommenterTransformer(Authenticatable $commenter, string $token = null): FractalHelper
 * @method static useCommentTransformer(Collection $comments);
 * @method static withIncludes(array $includes): FractalHelper
 * @method static buildWithArraySerializer(): void
 */
class Fractal extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'Fractal';
    }
}
