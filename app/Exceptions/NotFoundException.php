<?php

namespace App\Exceptions;
use App\Enums\LogEvents;
use App\Facades\SetLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Sentry\Laravel\Facade as Sentry;

class NotFoundException extends \Exception
{
    protected string $jsonMessage;
    protected array $data;
    protected string $model;
    protected int $statusCode = 404;

    public function __construct(
        string $jsonMessage = "Data tidak ditemukan",
        array $data = [],
        string $model = '',
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
