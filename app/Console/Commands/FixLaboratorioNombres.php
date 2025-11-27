<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Laboratorio;

class FixLaboratorioNombres extends Command
{
    protected $signature = 'laboratorio:fix-nombres';
    protected $description = 'Decodifica entidades HTML en nombre_lab (ej. &quot; → ")';

    public function handle()
    {
        $labs = Laboratorio::all();
        $count = 0;

        foreach ($labs as $lab) {
            $original = $lab->nombre_lab_sin_modificacion;
            $decoded = html_entity_decode($original, ENT_QUOTES);
            if ($decoded !== $original) {
                $lab->nombre_lab = $decoded;
                $lab->save();
                $count++;
                $this->info("Proceso LAB: {$lab->cod_lab} =  {$original} -> {$decoded}");
            }
        }

        $this->info("Proceso completado. {$count} registros actualizados.");
    }
}
