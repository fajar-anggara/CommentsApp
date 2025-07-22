<?php

namespace App\Helpers\Interfaces;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;

interface FractalHelper
{
    public function useCommenterTransformer(Authenticatable $commenter, String $token = null);
    public function useCommentTransformer(Collection $comments);
    public function withIncludes(array $includes);
    public function buildWithArraySerializer();
}
