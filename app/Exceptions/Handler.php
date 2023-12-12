<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($request->wantsJson()) {   // Check if the request wants a JSON response
            $response = [
                'message' => $exception->getMessage()
            ];

            if (config('app.debug')) {
                $response['trace'] = $exception->getTrace();
                $response['code'] = $exception->getCode();
            }

            $status = method_exists($exception, 'getStatusCode') 
                    ? $exception->getStatusCode() 
                    : 400;

            return response()->json($response, $status);
        }

        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }



}
