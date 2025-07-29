<?php

namespace App\Exceptions\ArticleExceptions;

use App\Exceptions\ApplicationException;

class ArticleNotFoundException extends ApplicationException
{
    public function __construct(
        string $message = "Artikel tidak ditemukan",
        array $context = [],
        int $statusCode = 404
    ) {
        parent::__construct($message, $statusCode, $context);
    }
}
