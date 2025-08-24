<?php

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'usuario.activo' => \App\Http\Middleware\VerificarUsuarioActivo::class,
            'canany' => \App\Http\Middleware\CanAnyPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (QueryException $e, $request) {
            if ($e->getCode() === "23000") {
                Log::error("Error de clave foránea: " . $e->getMessage());
                return redirect()->back()->with(
                    'error',
                    '⚠️ No se puede eliminar el registro porque está siendo utilizado en otra tabla.'
                );
            }
        });
    })->create();
