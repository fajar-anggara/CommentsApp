<?php

namespace App\Helpers\Interfaces;

use App\Enums\LogEvents;

interface LogHelper
{
    public function performedOn(string $model): LogHelper;
    public function causedBy(array $causer): LogHelper;
    public function withProperties(array $properties): LogHelper;
    public function withEvent(LogEvents $event): LogHelper;
    public function withMessage(string $message): LogHelper;
    public function build(): void;
    public function cleanLogs(): void;
}
