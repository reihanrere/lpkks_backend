<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/v1/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);

        $middleware->redirectGuestsTo(fn () => throw new AuthenticationException('Unauthenticated. Please login first.'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // Handler untuk UnauthorizedHttpException (misalnya dari abort(401))
        $exceptions->render(function (UnauthorizedHttpException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or missing authentication token.',
            ], 401);
        });

        // Handler untuk AuthenticationException (autentikasi gagal)
        $exceptions->render(function (AuthenticationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated. Please login first.',
            ], 401);
        });

        // Handler untuk UnauthorizedException (dari Spatie role/permission middleware)
        $exceptions->render(function (UnauthorizedException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Forbidden. You do not have the required role or permission.',
            ], 403);
        });

        // Handler untuk ValidationException
        $exceptions->render(function (Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        });

        // Handler catch-all untuk Throwable (error lainnya)
        $exceptions->render(function (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTrace() : null,
            ], 500);
        });

    })->create();
