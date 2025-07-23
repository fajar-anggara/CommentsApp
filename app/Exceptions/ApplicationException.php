<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Throwable;

abstract class ApplicationException extends Exception
{
    /**
     * The HTTP status code for the response.
     *
     * @var int
     */
    protected $statusCode;

    /**
     * The context data for logging.
     *
     * @var array
     */
    protected $context;

    public function __construct(string $message = "", int $statusCode = 500, array $context = [], Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->statusCode = $statusCode;
        $this->context = $context;
    }

    /**
     * Get the HTTP status code.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Get the context data for logging.
     *
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @return JsonResponse
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
        ], $this->getStatusCode());
    }
}
