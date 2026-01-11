<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Certificado;

class FixCertificadoNombreLaboratorio extends Command
{
    protected $signature = 'certificado:fix-nombre-laboratorio';

    protected $description = 'Decodifica entidades HTML en nombre_laboratorio del certificado (ej. &quot; → ")';

    public function handle()
    {
        $certificados = Certificado::all();
        $count = 0;

        foreach ($certificados as $cert) {
            $original = $cert->nombre_laboratorio;

            if (!$original) {
                continue;
            }

            $decoded = html_entity_decode($original, ENT_QUOTES);

            if ($decoded !== $original) {
                $cert->nombre_laboratorio = $decoded;
                $cert->save();

                $count++;

                $this->info(
                    "Certificado ID {$cert->id}: {$original} -> {$decoded}"
                );
            }
        }

        $this->info("Proceso completado. {$count} certificados actualizados.");
    }
}
