<?php

namespace App\Helpers\Interfaces;

use App\Enums\LogEvents;
use Illuminate\Database\Eloquent\Model;

interface LogHelper
{
    public function performedOn(Model $model): LogHelper;
    public function causedBy(Model $causer): LogHelper;
    public function withProperties(array $properties): LogHelper;
    public function withEvent(LogEvents $event): LogHelper;
    public function withMessage(string $message): LogHelper;
    public function build(): void;
    public function cleanLogs(): void;
}
