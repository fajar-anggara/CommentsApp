<?php

namespace App\Repositories\Interfaces;


use App\Models\Article;

interface ArticleRepository
{
    public function findByContext(
        array $context
    ): Article|bool;
}
