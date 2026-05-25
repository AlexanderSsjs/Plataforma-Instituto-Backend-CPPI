<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        
        // 🔒 1. REGISTRO DEL MIDDLEWARE DE ROLES
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);

        // 🌐 2. OPTIMIZACIÓN STATEFUL PARA REVERSIÓN DE COOKIES/CORS (Sanctum)
        $middleware->statefulApi();

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Su sesión ha expirado o el token es inválido. Por favor, inicie sesión de nuevo.'
                ], 401);
            }
        });

    })->create();