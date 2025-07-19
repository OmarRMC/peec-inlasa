<?php

use App\Models\Configuracion;

if (!function_exists('configuracion')) {
    function configuracion(string $key, ?string $valor = null)
    {
        if ($valor) {
            Configuracion::updateOrCreate(
                ['key' => $key],
                ['valor' => $valor]
            );
        } else {
            return Configuracion::find($key)?->valor;
        }
    }
}
