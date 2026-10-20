<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Recordatorio automático de envío de resultados (apertura + recordatorio final del ciclo).
Schedule::command('recordatorios:envio-resultados')
    ->dailyAt('08:00')
    ->withoutOverlapping();
