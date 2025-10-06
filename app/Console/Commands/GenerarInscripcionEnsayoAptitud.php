<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Inscripcion;
use App\Models\InscripcionEA;
use App\Models\EnsayoAptitud;

class GenerarInscripcionEnsayoAptitud extends Command
{
    # php artisan app:generar-inscripcion-ensayo-aptitud 2025
    protected $signature = 'app:generar-inscripcion-ensayo-aptitud {gestion?}';
    protected $description = 'Genera los registros en InscripcionEA para las inscripciones de una gestión específica';

    public function handle()
    {
        $gestion = $this->argument('gestion') ?? now()->year;
        $this->info("🔄 Generando InscripcionEA para la gestión: $gestion");
        $inscripciones = Inscripcion::where('gestion', $gestion)->get();
        $this->info("Se encontraron {$inscripciones->count()} inscripciones.");
        foreach ($inscripciones as $inscripcion) {
            $this->generarEnsayosParaInscripcion($inscripcion);
        }
        $this->info("✅ Registros de InscripcionEA generados correctamente.");
    }

    private function generarEnsayosParaInscripcion(Inscripcion $inscripcion)
    {
        if ($inscripcion->ensayos()->exists()) {
            $this->warn("La inscripción ID {$inscripcion->id} ya tiene ensayos generados, se omite.");
            return;
        }
        foreach ($inscripcion->detalleInscripciones as $detalle) {
            $ensayos = EnsayoAptitud::where('id_paquete', $detalle->id_paquete)
                ->active()
                ->get();

            foreach ($ensayos as $ea) {
                InscripcionEA::create([
                    'id_inscripcion' => $inscripcion->id,
                    'id_ea' => $ea->id,
                    'descripcion_ea' => $ea->descripcion,
                ]);
            }
        }
        $this->info("InscripcionEA generados para la inscripción ID {$inscripcion->id}");
    }
}
