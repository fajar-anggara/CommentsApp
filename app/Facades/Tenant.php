<?php

namespace App\Facades;

use Sentry\Laravel\Facade;

/**
 * @method static findTenantById(int $tenantId)
 */
class Tenant extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'Tenant';
    }
}
