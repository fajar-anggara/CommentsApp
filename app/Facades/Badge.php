<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Badge extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'Badge';
    }
}
