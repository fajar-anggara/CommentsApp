<?php

namespace App\Exceptions\CommenterExceptions;

use App\Exceptions\ApplicationException;

class WrongCredentialsException extends ApplicationException
{
    public function __construct(
        string $message = 'Email atau password salah',
        array $context = [],
        int $statusCode = 401
    )
    {
        parent::__construct($message, $statusCode, $context);
    }
}
