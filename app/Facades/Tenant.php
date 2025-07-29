<?php

namespace App\Facades;

use Sentry\Laravel\Facade;

/**
 * @method static findById(int $tenantId)
 *
 * @see \App\Helpers\TenantHelper
 */
class Tenant extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'Tenant';
    }
}
