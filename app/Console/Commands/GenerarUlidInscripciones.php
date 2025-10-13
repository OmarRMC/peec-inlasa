<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Models\Inscripcion;

class GenerarUlidInscripciones extends Command
{
    /**
     * El nombre y la firma del comando Artisan.
     *
     * @var string
     */
    protected $signature = 'inscripcion:generar-ulid';

    /**
     * La descripción del comando.
     *
     * @var string
     */
    protected $description = 'Genera ULIDs únicos para todas las inscripciones que no lo tengan';

    /**
     * Ejecuta el comando.
     */
    public function handle()
    {
        $inscripciones = Inscripcion::whereNull('ulid')->get();

        if ($inscripciones->isEmpty()) {
            $this->info('✅ Todas las inscripciones ya tienen un ULID.');
            return;
        }

        $this->info('🔄 Generando ULIDs para ' . $inscripciones->count() . ' inscripciones...');

        foreach ($inscripciones as $inscripcion) {
            $inscripcion->ulid = (string) Str::ulid();
            $inscripcion->save();
        }

        $this->info('✅ ULIDs generados correctamente para todas las inscripciones.');
    }
}
