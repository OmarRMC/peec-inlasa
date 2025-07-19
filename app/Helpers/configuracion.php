<?php

use App\Models\Configuracion;
use Carbon\Carbon;

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

if (!function_exists('formatDate')) {
    function formatDate(string $date)
    {
        return Carbon::parse($date)
            ->timezone('America/La_Paz')
            ->format('d/m/Y H:i');
    }
}
