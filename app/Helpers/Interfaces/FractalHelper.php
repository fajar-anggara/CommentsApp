<?php

namespace App\Helpers\Interfaces;

use Illuminate\Contracts\Auth\Authenticatable;

interface FractalHelper
{
    public function useCommenterTransformer(Authenticatable $commenter, String $token = null);
    public function withIncludes(array $includes);
    public function buildWithArraySerializer();
}
