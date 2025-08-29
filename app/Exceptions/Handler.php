<?php

namespace App\Exceptions;

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use PDOException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;


class Handler extends ExceptionHandler
{
    /**
     * Una lista de excepciones que no se reportan.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * Una lista de los inputs que nunca deben almacenarse en la sesión.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Registra las callbacks para manejar excepciones.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
