<?php

namespace App\Console\Commands;

use App\Models\RespuestaCampo;
use App\Models\RespuestaFormulario;
use App\Services\MapeadorValorRespuesta;
use Illuminate\Console\Command;

class BackfillRespuestasCampo extends Command
{
    protected $signature = 'respuestas:backfill {--chunk=200} {--dry-run}';

    protected $description = 'Migra respuestas_formulario (JSON) a respuestas_campo (filas tipadas). Idempotente.';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $procesadasJson    = 0;
        $upserts           = 0;
        $omitidasHuerfanas = 0;
        $omitidasSinId     = 0;

        RespuestaFormulario::chunkById((int) $this->option('chunk'), function ($filas) use (
            &$procesadasJson, &$upserts, &$omitidasHuerfanas, &$omitidasSinId, $dry
        ) {
            foreach ($filas as $fila) {
                $procesadasJson++;
                if (!$fila->id_formulario_resultado) {
                    $omitidasHuerfanas++;
                    continue;
                }

                foreach (($fila->respuestas ?? []) as $campoData) {
                    if (empty($campoData['id'])) {
                        $omitidasSinId++;
                        continue;
                    }
                    $mapped = MapeadorValorRespuesta::mapear($campoData, $fila->nombre);

                    if (!$dry) {
                        RespuestaCampo::updateOrCreate(
                            [
                                'id_formulario_resultado' => $fila->id_formulario_resultado,
                                'id_parametro_campo'      => (int) $campoData['id'],
                            ],
                            $mapped
                        );
                    }
                    $upserts++;
                }
            }
        });

        $this->info("Filas JSON procesadas:           {$procesadasJson}");
        $this->info("Filas tipadas creadas/upsert:    {$upserts}");
        $this->info("Omitidas (resultado huerfano):   {$omitidasHuerfanas}");
        $this->info("Omitidas (campo sin id):         {$omitidasSinId}");

        if ($dry) {
            $this->warn('Modo --dry-run: no se escribio nada.');
        }

        return self::SUCCESS;
    }
}
