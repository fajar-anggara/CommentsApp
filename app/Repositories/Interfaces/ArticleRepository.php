<?php

namespace App\Repositories\Interfaces;


use App\Models\Article;

interface ArticleRepository
{
    public function findOrCreateByArticleExternalId(string $url, string $externalArticleId, int $tenantId): ?Article;
}
