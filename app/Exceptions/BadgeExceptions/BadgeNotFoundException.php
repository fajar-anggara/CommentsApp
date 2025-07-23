<?php

namespace App\Exceptions\BadgeExceptions;

use App\Exceptions\ApplicationException;

class BadgeNotFoundException extends ApplicationException
{
    public function __construct(
        string $message = "Badge tidak ditemukan",
        array $context = [],
        int $statusCode = 404
    )
    {
        parent::__construct($message, $statusCode, $context);
    }
}
