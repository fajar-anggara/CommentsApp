<?php

namespace App\Exceptions;
use App\Facades\SetLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Sentry\Laravel\Facade as Sentry;

class NotFoundException extends \Exception
{
    protected string $jsonMessage;
    protected array $causer;
    protected Model $performedOnModel;
    protected int $statusCode = 404;

    protected function __construct(
        string $jsonMessage,
        array $causer,
        Model $performedOnModel
    ){
        $this->jsonMessage = $jsonMessage;
        $this->causer = $causer;
        $this->performedOnModel = $performedOnModel;

        Parent::__construct(
            $this->jsonMessage,
            $this->statusCode
        );

        SetLog::withEvent('Fetching')
            ->causedBy($causer)
            ->performedOn($this->performedOnModel)
            ->withMessage("Failed to fetch: " . $this->performedOnModel)
            ->withProperties([
                'time' => now()
            ])
            ->build();
    }
}
