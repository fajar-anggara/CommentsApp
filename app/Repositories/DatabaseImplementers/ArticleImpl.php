<?php

namespace App\Repositories\DatabaseImplementers;

use App\Enums\LogEvents;
use App\Facades\SetLog;
use App\Facades\Tenant;
use App\Models\Article;
use App\Repositories\Interfaces\ArticleRepository;

class ArticleImpl implements ArticleRepository
{

    public function findOrCreateByArticleExternalId(string $url, string $externalArticleId, int $tenantId): ?Article
    {
        // exception was handled in tenant repo
        $tenant = Tenant::findTenantById($tenantId);
        $article = Article::where('external_article_id', $externalArticleId)->first();
        if (!$article) {
            SetLog::withEvent(LogEvents::FETCHING)
                ->causedBy($tenant)
                ->performedOn($tenant)
                ->withProperties([
                    'external_article_id' => $externalArticleId,
                    'tenant_id' => $tenant->id,
                ])
                ->withMessage("No such article exists - Create instead");

            $article = Article::create([
                'tenant_id' => $tenantId,
                'external_article_id' => $externalArticleId,
                'title' => 'Generated Article Title',
                'url' => $url,
            ]);
        }

        return $article;
    }
}
