<?php

namespace App\Http\Helpers\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface LogHelper
{
    public function performedOn(Model $model): LogHelper;
    public function causedBy(array $causer): LogHelper;
    public function withProperties(array $properties): LogHelper;
    public function withEvent(string $event): LogHelper;
    public function withMessage(string $message): LogHelper;
    public function build(): void;
    public function cleanLogs(): void;
}
