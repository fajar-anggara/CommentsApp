<?php

namespace App\Exceptions\CommenterExceptions;

use App\Exceptions\ApplicationException;

class CommenterNotFoundException extends ApplicationException
{
    public function __construct(
        string $message = "User tidak ditemukan",
        array $context = [],
        int $statusCode = 404
    )
    {
        parent::__construct($message, $statusCode, $context);
    }
}
