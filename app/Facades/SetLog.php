<?php

namespace App\Facades;

use App\Enums\LogEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;

/**
 * @method static withEvent(LogEvents $event): LogHelper
 * @method static withProperties(array $properties): LogHelper
 * @method static withMessage(string $message): LogHelper
 * @method static causedBy(Model $model): LogHelper
 * @method static performedOn(Model $model): LogHelper
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
