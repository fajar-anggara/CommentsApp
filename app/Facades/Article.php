<?php

namespace App\Facades;

use Sentry\Laravel\Facade;

/**
 * @method static findOrCreateByArticleExternalId(string $url, string $externalArticleId, int $tenantId);
 */
class Article extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'Article';
    }
}
