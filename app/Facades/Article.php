<?php

namespace App\Facades;

use Sentry\Laravel\Facade;

/**
 * @method static findByContext(array $context);
 *
 * @see \App\Helpers\ArticleHelper
 */
class Article extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'Article';
    }
}
