<?php

namespace App\Http\Controllers;

use App\Models\Ciclo;
use App\Models\Configuracion;
use App\Models\EnsayoAptitud;
use App\Models\FormularioEnsayoResultado;
use App\Models\InformeTecnico;
use App\Models\Inscripcion;
use App\Models\InscripcionEA;
use App\Models\Laboratorio;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->loadMissing(['permisos', 'laboratorio', 'cargo']);

        // ── LABORATORIO ──────────────────────────────────────────────
        if ($user->isLaboratorio()) {
            $lab = $user->laboratorio;
            $inscripcionActual = $lab->inscripciones()
                ->with('detalleInscripciones')
                ->latest('id')
                ->first();

            $gestion = $inscripcionActual?->gestion ?? configuracion(Configuracion::GESTION_INSCRIPCION);

            $ultimaGestionAprobada = $lab->inscripciones()
                ->where('status_inscripcion', Inscripcion::STATUS_APROBADO)
                ->orderByDesc('gestion')
                ->value('gestion');

            $inscripcionesAprobadas = $ultimaGestionAprobada
                ? $lab->inscripciones()
                    ->with(['detalleInscripciones', 'pagos'])
                    ->where('status_inscripcion', Inscripcion::STATUS_APROBADO)
                    ->where('gestion', $ultimaGestionAprobada)
                    ->get()
                : collect();

            $paquetesAprobados  = $inscripcionesAprobadas->flatMap->detalleInscripciones;
            $totalAprobado      = $inscripcionesAprobadas->sum('costo_total');
            $saldoTotal         = $inscripcionesAprobadas->sum('saldo');
            $pagadoAprobado     = $inscripcionesAprobadas->every(fn($i) => $i->status_cuenta === Inscripcion::STATUS_PAGADO);

            $ensayosConCiclo = collect();
            $pendientes      = collect();
            if ($inscripcionesAprobadas->isNotEmpty()) {
                $paqueteIds = $paquetesAprobados->pluck('id_paquete')->unique();
                $ensayos    = EnsayoAptitud::whereIn('id_paquete', $paqueteIds)
                    ->where('status', 1)
                    ->get();

                $hoy = Carbon::today();
                foreach ($ensayos as $ea) {
                    $estado = $ea->getEstadoCiclosYResultados();
                    $ciclo  = $estado['cicloActivo'] ?? $estado['siguienteCiclo'] ?? null;

                    if (!$ciclo) continue;

                    $ensayosConCiclo->push([
                        'ensayo'      => $ea,
                        'ciclo'       => $ciclo,
                        'estadoCiclo' => $estado['estado'],
                    ]);

                    // Pendientes solo para ciclos activos hoy
                    if ($estado['estado'] !== 'activo') continue;

                    if ($ciclo->enPeriodoEnvioResultados()) {
                        $enviado = FormularioEnsayoResultado::where('id_laboratorio', $lab->id)
                            ->where('id_ciclo', $ciclo->id)
                            ->exists();
                        $pendientes->push([
                            'ciclo'      => $ciclo,
                            'ensayo'     => $ea,
                            'tipo'       => 'resultados',
                            'completado' => $enviado,
                            'vence'      => $ciclo->fecha_fin_envio_resultados_show,
                            'ruta'       => route('lab.inscritos-ensayos.formularios', $ea->id),
                        ]);
                    }
                }
            }

            return view('dashboard', [
                'tipo'                    => 'laboratorio',
                'lab'                     => $lab,
                'gestion'                 => $gestion,
                'inscripcionActual'        => $inscripcionActual,
                'inscripcionesAprobadas'  => $inscripcionesAprobadas,
                'paquetesAprobados'      => $paquetesAprobados,
                'totalAprobado'          => $totalAprobado,
                'saldoTotal'             => $saldoTotal,
                'pagadoAprobado'         => $pagadoAprobado,
                'ultimaGestionAprobada'  => $ultimaGestionAprobada,
                'ensayosConCiclo'         => $ensayosConCiclo,
                'pendientes'              => $pendientes,
                'periodoInscripcion'      => Configuracion::esPeriodoInscripcion(),
                'certificadosHabilitados' => Configuracion::estaHabilitadoCargarCertificado(),
            ]);
        }

        // ── RESPONSABLE DE EA ────────────────────────────────────────
        if ($user->isResponsableEA()) {
            $gestion   = Inscripcion::latest('id')->value('gestion') ?? configuracion(Configuracion::GESTION_INSCRIPCION);
            $ensayoAps = $user->responsablesEA()->with('paquete')->get();

            foreach ($ensayoAps as $ea) {
                $ea->total_inscritos = InscripcionEA::whereHas('inscripcion', fn($q) => $q->where('gestion', $gestion))
                    ->where('id_ea', $ea->id)
                    ->count();

                $estadoCiclo          = $ea->getEstadoCiclosYResultados();
                $ciclo                = $estadoCiclo['cicloActivo'] ?? $estadoCiclo['siguienteCiclo'] ?? null;
                $ea->ciclo_dashboard  = $ciclo;
                $ea->estado_ciclo     = $estadoCiclo['estado'];

                if ($ciclo) {
                    $ciclo->resultados_recibidos = FormularioEnsayoResultado::where('id_ensayo', $ea->id)
                        ->where('id_ciclo', $ciclo->id)
                        ->count();
                }
            }

            return view('dashboard', [
                'tipo'                    => 'responsable',
                'gestion'                 => $gestion,
                'ensayoAps'               => $ensayoAps,
                'certificadosHabilitados' => Configuracion::estaHabilitadoCargarCertificado(),
            ]);
        }

        // ── ADMIN / JEFE PEEC / PERSONAL INTERNO ────────────────────
        $gestion = Inscripcion::latest('id')->value('gestion') ?? configuracion(Configuracion::GESTION_INSCRIPCION);
        $inscripcionesGestion = Inscripcion::where('gestion', $gestion)->get();

        $ciclosGestion = Ciclo::where('gestion', $gestion)->get();
        $hoy = Carbon::today();
        $ciclosActivosHoy = Ciclo::where('gestion', $gestion)
            ->activo()
            ->with('ensayoAptitud')
            ->get()
            ->filter(function ($c) use ($hoy) {
                return ($c->fecha_inicio_envio_muestras && $c->fecha_fin_envio_muestras
                        && $hoy->between($c->fecha_inicio_envio_muestras, $c->fecha_fin_envio_muestras))
                    || $c->enPeriodoEnvioResultados()
                    || ($c->fecha_inicio_envio_reporte && $c->fecha_fin_envio_reporte
                        && $hoy->between($c->fecha_inicio_envio_reporte, $c->fecha_fin_envio_reporte));
            })
            ->values();

        return view('dashboard', [
            'tipo'                    => 'admin',
            'gestion'                 => $gestion,
            'totalLabs'               => Laboratorio::active()->count(),
            'totalInscripciones'      => $inscripcionesGestion->count(),
            'enRevision'              => $inscripcionesGestion->where('status_inscripcion', Inscripcion::STATUS_EN_REVISION)->count(),
            'aprobadas'               => $inscripcionesGestion->where('status_inscripcion', Inscripcion::STATUS_APROBADO)->count(),
            'enObservacion'           => $inscripcionesGestion->where('status_inscripcion', Inscripcion::STATUS_EN_OBSERVACION)->count(),
            'anuladas'                => $inscripcionesGestion->where('status_inscripcion', Inscripcion::STATUS_ANULADO)->count(),
            'deudores'                => $inscripcionesGestion->where('status_cuenta', Inscripcion::STATUS_DEUDOR)->count(),
            'totalCiclos'             => $ciclosGestion->count(),
            'ciclosHabilitados'       => $ciclosGestion->where('estado', true)->count(),
            'ciclosEnCurso'           => $ciclosActivosHoy->count(),
            'totalResultados'         => FormularioEnsayoResultado::where('gestion', $gestion)->count(),
            'totalInformes'           => InformeTecnico::where('gestion', $gestion)->count(),
            'ciclosActivosHoy'        => $ciclosActivosHoy,
            'periodoInscripcion'      => Configuracion::esPeriodoInscripcion(),
            'certificadosHabilitados' => Configuracion::estaHabilitadoCargarCertificado(),
        ]);
    }
}
