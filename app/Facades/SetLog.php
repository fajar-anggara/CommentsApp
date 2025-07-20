<?php

namespace App\Facades;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;

/**
 * @method static withEvent(string $event): LogHelper
 * @method static withProperties(array $properties): LogHelper
 * @method static withMessage(string $message): LogHelper
 * @method static withCausedBy(Model $model): LogHelper
 * @method static performedOn(string $model): LogHelper
 * @method static build(): void
 * @method static cleanLogs(): void
 */
class SetLog extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'SetLog';
    }
}
