<?php

namespace App\Http\Helpers\Interfaces;

use Illuminate\Database\Eloquent\Model;
use League\Fractal\Serializer\Serializer;

interface FractalHelper
{
    public function useCommenterTransformer(Model $commenter, String $token = null);
    public function withIncludes(array $includes);
    public function buildWithArraySerializer();
}
