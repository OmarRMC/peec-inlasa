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
    function formatDate(string $date, $time = true)
    {
        $format = $time ? 'd/m/Y H:i' : 'd/m/Y';
        return Carbon::parse($date)
            ->timezone('America/La_Paz')
            ->format($format);
    }
}
