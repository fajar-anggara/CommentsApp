<?php

namespace App\Exceptions;

use App\Exceptions\ApplicationException;

class FailedToSavedException extends ApplicationException
{
    public function __construct(
        string $message = "Kesalahan, silahkan coba lagi",
        array $context = [],
        int $statusCode = 500
    )
    {
        parent::__construct($message, $statusCode, $context);
    }
}
