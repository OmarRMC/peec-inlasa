<?php

namespace App\Console\Commands;

use App\Mail\RecordatorioEnvioResultados as RecordatorioMail;
use App\Models\Ciclo;
use App\Models\FormularioEnsayoResultado;
use App\Models\InscripcionEA;
use App\Models\Laboratorio;
use App\Models\Notificacion;
use App\Models\NotificacionLab;
use App\Models\User;
use App\Services\NotificacionLabService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RecordatorioEnvioResultados extends Command
{
    protected $signature = 'recordatorios:envio-resultados
                            {--dias-antes=1 : Días antes del cierre para el recordatorio final}
                            {--dry-run : Solo reporta, no crea notificaciones ni envía correos}';

    protected $description = 'Recuerda a los laboratorios inscritos a un ensayo que deben enviar resultados cuando abre la ventana del ciclo y antes de su cierre';

    public function handle(): int
    {
        $hoy = Carbon::today();
        $diasAntes = (int) $this->option('dias-antes');
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->warn('[DRY-RUN] No se crearán notificaciones ni se enviarán correos.');
        }

        // created_by solo satisface la FK a users; el remitente mostrado es "Sistema (automático)".
        $createdBy = User::min('id');

        $ciclos = Ciclo::activo()
            ->whereNotNull('id_ensayo')
            ->whereNotNull('fecha_inicio_envio_resultados')
            ->whereNotNull('fecha_fin_envio_resultados')
            ->with('ensayoAptitud')
            ->get();

        $totalNotificados = 0;

        foreach ($ciclos as $ciclo) {
            $inicio = Carbon::parse($ciclo->fecha_inicio_envio_resultados)->startOfDay();
            $fin = Carbon::parse($ciclo->fecha_fin_envio_resultados)->startOfDay();

            // Determinar la fase según la fecha de hoy.
            $fase = null;
            if ($hoy->equalTo($inicio)) {
                $fase = 'apertura';
            } elseif ($hoy->equalTo($fin->copy()->subDays($diasAntes)) && $hoy->lessThanOrEqualTo($fin)) {
                $fase = 'final';
            }

            if ($fase === null) {
                continue;
            }

            // Labs inscritos al ensayo del ciclo (inscripción aprobada/vencida de la gestión del ciclo).
            $idLabs = InscripcionEA::where('id_ea', $ciclo->id_ensayo)
                ->whereHas('inscripcion', function ($q) use ($ciclo) {
                    $q->aprobadoOrVencido()->where('gestion', $ciclo->gestion);
                })
                ->with('inscripcion:id,id_lab')
                ->get()
                ->pluck('inscripcion.id_lab')
                ->unique()
                ->filter()
                ->values();

            // Excluir los que ya enviaron resultados de este ciclo.
            $yaEnviaron = FormularioEnsayoResultado::where('id_ciclo', $ciclo->id)
                ->where('estado', 1)
                ->pluck('id_laboratorio')
                ->unique();

            $pendientes = $idLabs->diff($yaEnviaron);

            // Idempotencia: excluir labs ya notificados para este ciclo + fase.
            $idsNotif = Notificacion::deTipo(Notificacion::TIPO_RECORDATORIO_RESULTADOS)
                ->deEntidad(Notificacion::ENTIDAD_CICLO, $ciclo->id)
                ->where('mensaje->fase', $fase)
                ->pluck('id');

            if ($idsNotif->isNotEmpty()) {
                $yaNotificados = NotificacionLab::whereIn('id_notificacion', $idsNotif)
                    ->pluck('id_lab')
                    ->unique();
                $pendientes = $pendientes->diff($yaNotificados);
            }

            $pendientes = $pendientes->values();

            $this->line(sprintf(
                'Ciclo #%d "%s" (ensayo #%d) fase=%s | inscritos=%d, ya_enviaron=%d, a_notificar=%d',
                $ciclo->id,
                $ciclo->nombre,
                $ciclo->id_ensayo,
                $fase,
                $idLabs->count(),
                $yaEnviaron->count(),
                $pendientes->count()
            ));

            if ($pendientes->isEmpty() || $dryRun) {
                continue;
            }

            $ensayoDesc = optional($ciclo->ensayoAptitud)->descripcion;
            $mensaje = [
                'fase'         => $fase,
                'id_ea'        => $ciclo->id_ensayo,
                'ensayo'       => $ensayoDesc,
                'ciclo'        => $ciclo->nombre,
                'fecha_limite' => $ciclo->fecha_fin_envio_resultados,
            ];

            // 1) Notificación in-app (1 plantilla + N recepciones). Persistente y vigente hasta el cierre.
            NotificacionLabService::crear([
                'id_labs'      => $pendientes->all(),
                'tipo'         => Notificacion::TIPO_RECORDATORIO_RESULTADOS,
                'mensaje'      => $mensaje,
                'entidad'      => [
                    'tipo' => Notificacion::ENTIDAD_CICLO,
                    'id'   => $ciclo->id,
                ],
                'modo'         => Notificacion::MODO_PERSISTENTE,
                'fecha_inicio' => $hoy->toDateString(),
                'fecha_fin'    => $ciclo->fecha_fin_envio_resultados,
                'created_by'   => $createdBy,
            ]);

            // 2) Correo a cada laboratorio pendiente.
            $labs = Laboratorio::whereIn('id', $pendientes->all())->get();
            foreach ($labs as $lab) {
                if (empty($lab->mail_lab)) {
                    continue;
                }
                try {
                    Mail::to($lab->mail_lab)->send(new RecordatorioMail(
                        $lab->nombre_lab,
                        $ensayoDesc,
                        $ciclo->nombre,
                        $ciclo->fecha_fin_envio_resultados,
                        $fase
                    ));
                } catch (\Throwable $e) {
                    Log::error('Recordatorio envío resultados: fallo correo a lab ' . $lab->id . ': ' . $e->getMessage());
                }
            }

            $totalNotificados += $pendientes->count();
        }

        $this->info("Listo. Laboratorios notificados: {$totalNotificados}.");

        return self::SUCCESS;
    }
}
