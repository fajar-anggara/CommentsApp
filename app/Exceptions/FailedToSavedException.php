<?php

namespace App\Exceptions;

use App\Enums\LogEvents;
use App\Facades\SetLog;
use Illuminate\Database\Eloquent\Model;

class FailedToSavedException extends \Exception
{
    protected string $jsonMessage;
    protected array $data;
    protected string $model;
    protected int $statusCode = 500;

    public function __construct(
        string $jsonMessage = "Kesalahan, silahkan coba lagi",
        array $data = [],
        string $model = '',
        int $statusCode = 500
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

        SetLog::withEvent(LogEvents::STORING)
            ->withProperties([
                'data' => $data,
                'model' => $model,
                'exception' => static::class,
                'performedOn' => [
                    'class' => static::class,
                    'method' => '__construct'
                ]
            ])
            ->withMessage("Failed to save data: $jsonMessage")
            ->build();
    }
}
