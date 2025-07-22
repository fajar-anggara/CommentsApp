<?php

namespace App\Exceptions\TenantExceptions;

use App\Enums\LogEvents;
use App\Exceptions\ReportableException;
use App\Facades\SetLog;
use App\Models\Badge;
use App\Models\Tenant;

class TenantNotFoundException extends ReportableException
{
    protected string $jsonMessage;
    protected array $data;
    protected string $model;
    protected int $statusCode = 404;

    public function __construct(
        string $jsonMessage = "Tenant tidak ditemukan",
        array $data = [],
        string $model = Tenant::class,
        int $statusCode = 404
    )
    {
        $this->jsonMessage = $jsonMessage;
        $this->data = $data;
        $this->model = $model;
        $this->statusCode = $statusCode;

        parent::__construct(
            $this->jsonMessage,
            $this->statusCode
        );

        SetLog::withEvent(LogEvents::FETCHING)
            ->withProperties([
                'data' => $data,
                'model' => $model,
                'exception' => static::class,
                'performedOn' => [
                    'class' => static::class,
                    'method' => '__construct'
                ]
            ])
            ->withMessage("Data not found: $jsonMessage")
            ->build();
    }
}
