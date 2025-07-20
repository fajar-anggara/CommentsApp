<?php
namespace App\Exceptions;

use App\Facades\SetLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Sentry\Laravel\Facade as Sentry;

abstract class ReportableException extends \Exception
{
    protected int $statusCode;
    protected string $jsonMessage;

    public function __construct(
        string $jsonMessage = "Terjadi Kesalahan",
        int $statusCode = 500
    )
    {
        Parent::__construct($jsonMessage);
        $this->jsonMessage = $jsonMessage;
        $this->statusCode = $statusCode;
    }

    public function report(): void
    {
        Log::warning($this->getMessage(), ['exception' => static::class]);

        if (app()->bound('sentry')) {
            Sentry::configureScope(function ($scope) {
                $scope->setLevel(\Sentry\Severity::warning());
                $scope->setTag('exception_type', static::class);
                $scope->setContext('request', [
                    'ip' => request()->ip(),
                    'agent' => request()->userAgent(),
                    'route' => request()->route()?->getName(),
                ]);
            });

            Sentry::captureException($this);
        }
    }

    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $this->jsonMessage
        ], $this->statusCode);
    }
}
