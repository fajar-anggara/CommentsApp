<?php

namespace App\Exceptions;

use App\Enums\LogEvents;
use App\Facades\SetLog;

class WrongCredentialsException extends ReportableException
{
    protected string $email;
    protected string $jsonMessage;
    protected int $statusCode = 401;

    public function __construct(
        string $email = '',
        string $jsonMessage = 'Email atau password salah'
    )
    {
        $this->email = $email;
        $this->jsonMessage = $jsonMessage;

        parent::__construct(
            $this->jsonMessage,
            $this->statusCode
        );

        SetLog::withEvent(LogEvents::LOGIN)
            ->withMessage("Login failed for email: $email")
            ->withProperties([
                'email' => $email,
                'exception' => static::class,
                'performedOn' => [
                    'class' => static::class,
                    'method' => '__construct'
                ]
            ])
            ->build();
    }
}
