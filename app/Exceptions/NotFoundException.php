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
    protected string $performedModel;
    protected int $statusCode = 404;

    public function __construct(
        string $jsonMessage = "Data tidak ditemukan",
        array $causer  = [],
        string $performedOnModel = null,
    ) {
        $this->jsonMessage = $jsonMessage;
        $this->causer = $causer;
        $this->performedModel = $performedOnModel;

        Parent::__construct(
            $this->jsonMessage,
            $this->statusCode
        );

        SetLog::withEvent("Fetching")
            ->causedBy($causer)
            ->performedOn($performedOnModel)
            ->withMessage("Not Found when access: " . $performedOnModel)
            ->withProperties([
                'time' => now()
            ])
            ->build();
    }
}
