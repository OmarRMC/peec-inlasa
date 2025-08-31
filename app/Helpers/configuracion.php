<?php

use App\Models\Configuracion;
use Carbon\Carbon;

if (!function_exists('configuracion')) {
    function configuracion(string $key, $valor = null)
    {
        if (isset($valor)) {
            Configuracion::updateOrCreate(
                ['key' => $key],
                ['valor' => is_array($valor) ? json_encode($valor, JSON_UNESCAPED_UNICODE) : $valor]
            );
        } else {
            $config = Configuracion::find($key)?->valor;
            $decoded = json_decode($config);
            return json_last_error() === JSON_ERROR_NONE ? $decoded : $config;
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
