<?php

namespace App\Helpers;

use App\Exceptions\ArticleExceptions\ArticleNotFoundException;
use App\Models\Article;
use App\Repositories\Interfaces\ArticleRepository;

class ArticleHelper
{
    public function __construct(
        protected ArticleRepository $articleRepository
    ) {}

    public function findByContext(array $context): Article
    {
        $article = $this->articleRepository->findByContext($context);

        if (!$article) {
            throw new ArticleNotFoundException(
                "Artikel tidak ditemukan",
                [
                    'article_url' => $context['article_url'],
                    'external_article_id' => $context['external_article_id'],
                    'tenant_id' => $context['tenant_id'],
                    'model' => Article::class
                ]
            );
        }

        return $article;
    }
}
