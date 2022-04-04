<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
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
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (ValidationException $e, $request) {
            if ($request->is('api/*')) {
                $errors = $e->errors();

                return response()->json([
                    'error' => array_values($errors)[0][0],
                    'data' => null,
                ], 400);
            }
        });

        $this->renderable(function (UnauthorizedException $e, $request) {
            if ($request->is('api/*')) {

                return response()->json([
                    'error' => 'Acceso no autorizado',
                    'data' => null,
                ], 401);
            }
        });

        $this->renderable(function (AuthenticationException $e, $request) {
            if ($request->is('api/*')) {

                return response()->json([
                    'error' => 'No estás autenticado',
                    'unauthenticated' => true,
                    'data' => null,
                ], 401);
            }
        });

        $this->renderable(function (Throwable $e, $request) {
            if ($request->is('api/*')) {

                return response()->json([
                    'error' => $e->getMessage(),
                    'data' => null,
                ], 500);
            }
        });
    }
}
