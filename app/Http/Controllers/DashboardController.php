<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use App\Models\Inscripcion;
use App\Models\Laboratorio;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->loadMissing(['permisos', 'laboratorio', 'cargo']);

        // ── LABORATORIO ──────────────────────────────────────────────
        if ($user->isLaboratorio()) {
            $lab = $user->laboratorio;
            $gestion = configuracion(Configuracion::GESTION_INSCRIPCION);

            // Inscripciones de la gestión configurada, sin anuladas (un lab puede tener varias)
            $inscripciones = $lab->inscripciones()
                ->with('detalleInscripciones')
                ->where('gestion', $gestion)
                ->where('status_inscripcion', '!=', Inscripcion::STATUS_ANULADO)
                ->orderBy('id')
                ->get();

            // Válidas = aprobadas o vencidas; si no hay, se muestran las que están en revisión/observación
            $estadosValidos = [Inscripcion::STATUS_APROBADO, Inscripcion::STATUS_VENCIDO];
            $validas = $inscripciones->whereIn('status_inscripcion', $estadosValidos);
            $enTramite = $inscripciones->whereNotIn('status_inscripcion', $estadosValidos);
            $mostradas = $validas->isNotEmpty() ? $validas : $enTramite;

            return view('dashboard', [
                'tipo'                    => 'laboratorio',
                'lab'                     => $lab,
                'gestion'                 => $gestion,
                'resumenInscripcion'      => $mostradas->isEmpty() ? null : (object) [
                    'valida'        => $validas->isNotEmpty(),
                    'estados'       => $mostradas->pluck('status_inscripcion')->unique()->values(),
                    'paquetes'      => $mostradas->flatMap->detalleInscripciones
                        ->unique('id_paquete')
                        ->pluck('descripcion_paquete')
                        ->values(),
                    'costo_total'   => $mostradas->sum('costo_total'),
                    'pagado'        => $mostradas->every(fn($i) => $i->status_cuenta === Inscripcion::STATUS_PAGADO),
                    'fecha'         => $mostradas->last()->fecha_inscripcion,
                    // Inscripciones adicionales aún en trámite cuando ya hay una aprobada
                    'en_tramite'    => $validas->isNotEmpty() ? $enTramite->count() : 0,
                ],
                'periodoInscripcion'      => Configuracion::esPeriodoInscripcion(),
                'certificadosHabilitados' => Configuracion::estaHabilitadoCargarCertificado(),
            ]);
        }

        // ── RESPONSABLE DE EA ────────────────────────────────────────
        if ($user->isResponsableEA()) {
            $ensayoAps = $user->responsablesEA()->with('paquete')->get();
            $gestion = Inscripcion::latest('id')->value('gestion') ?? configuracion(Configuracion::GESTION_INSCRIPCION);

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
            'periodoInscripcion'      => Configuracion::esPeriodoInscripcion(),
            'certificadosHabilitados' => Configuracion::estaHabilitadoCargarCertificado(),
        ]);
    }
}
