<?php

namespace App\Exceptions;

use App\Models\Comment;

class CommentNotFoundException extends ApplicationException
{
    public function __construct(
        string $message = "Komentar tidak ditemukan",
        array $context = [],
        int $statusCode = 404
    )
    {
        parent::__construct($message, $statusCode, $context);
    }
}
