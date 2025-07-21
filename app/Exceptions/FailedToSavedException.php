<?php

namespace App\Exceptions;

use App\Enums\LogEvents;
use App\Facades\SetLog;
use Illuminate\Database\Eloquent\Model;

class FailedToSavedException extends \Exception
{
    protected string $jsonMessage;
    protected array $causer;
    protected string $performedModel;
    protected int $statusCode = 500;

    public function __construct(
        string $jsonMessage = "Terjadi kesalahan, harap coba lagi",
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

        SetLog::withEvent(LogEvents::STORING)
            ->causedBy($causer)
            ->performedOn($performedOnModel)
            ->withMessage("Failed to save to: " . $performedOnModel)
            ->withProperties([
                'exception' => static::class
            ])
            ->build();
    }
}

