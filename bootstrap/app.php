<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->expectsJson()) {
                $status = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;

                return response()->json([
                    'message' => match (true) {
                        $status === 404 => 'Resource not found.',
                        $status === 403 => 'Access denied.',
                        $status === 419 => 'Session expired. Please refresh and try again.',
                        $status === 429 => 'Too many requests. Please wait and try again.',
                        default => app()->isProduction()
                            ? 'Something went wrong. Please try again later.'
                            : $e->getMessage(),
                    },
                ], $status);
            }

            return null;
        });

        $exceptions->report(function (Throwable $e) {
            if ($e instanceof HttpExceptionInterface && $e->getStatusCode() < 500) {
                return false;
            }

            return null;
        });
    })->create();
