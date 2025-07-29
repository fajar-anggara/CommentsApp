<?php

namespace App\Repositories\DatabaseImplementers;

use App\Enums\LogEvents;
use App\Facades\SetLog;
use App\Facades\Tenant;
use App\Models\Article;
use App\Repositories\Interfaces\ArticleRepository;

class ArticleImpl implements ArticleRepository
{

    public function findByContext(array $context): Article|bool
    {
        $article = Article::where('url', $context['article_url'])
            ->where('tenant_id', $context['tenant_id'])
            ->where('external_article_id', $context['external_article_id'])
            ->first();

        if (!$article) {
            return false;
        }

        return $article;
    }
}
