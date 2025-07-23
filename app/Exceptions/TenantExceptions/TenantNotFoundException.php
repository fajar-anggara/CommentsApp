<?php

namespace App\Exceptions\TenantExceptions;

use App\Exceptions\ApplicationException;

class TenantNotFoundException extends ApplicationException
{
    public function __construct(
        string $message = "Tenant tidak ditemukan",
        array $context = [],
        int $statusCode = 404
    )
    {
        parent::__construct($message, $statusCode, $context);
    }
}
