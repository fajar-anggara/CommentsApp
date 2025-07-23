<?php

namespace App\Exceptions;

use App\Facades\SetLog;
use App\Exceptions\ApplicationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        ApplicationException::class
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (ApplicationException $e, $request) {
            if ($request->is('api/*')) {
                SetLog::withEvent('EXCEPTION_OCCURRED')
                    ->withProperties(array_merge($e->getContext(), [
                        'exception' => get_class($e),
                        'file'      => $e->getFile(),
                        'line'      => $e->getLine(),
                    ]))
                    ->withMessage($e->getMessage())
                    ->build();

                return $e->render();
            }
        });

        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
