<?php

namespace App\Facades;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;

/**
 * @method static useCommenterTransformer(Model $commenter, string $token): FractalHelper
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
