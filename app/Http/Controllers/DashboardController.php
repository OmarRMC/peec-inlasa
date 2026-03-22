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
        $gestion = configuracion(Configuracion::GESTION_INSCRIPCION);

        // ── LABORATORIO ──────────────────────────────────────────────
        if ($user->isLaboratorio()) {
            $lab = $user->laboratorio;
            $inscripcionActual = $lab->inscripciones()
                ->where('gestion', $gestion)
                ->with('detalleInscripciones')
                ->latest('id')
                ->first();

            return view('dashboard', [
                'tipo'                    => 'laboratorio',
                'lab'                     => $lab,
                'gestion'                 => $gestion,
                'inscripcionActual'       => $inscripcionActual,
                'periodoInscripcion'      => Configuracion::esPeriodoInscripcion(),
                'certificadosHabilitados' => Configuracion::estaHabilitadoCargarCertificado(),
            ]);
        }

        // ── RESPONSABLE DE EA ────────────────────────────────────────
        if ($user->isResponsableEA()) {
            $ensayoAps = $user->responsablesEA()->with('paquete')->get();

            return view('dashboard', [
                'tipo'                    => 'responsable',
                'gestion'                 => $gestion,
                'ensayoAps'               => $ensayoAps,
                'certificadosHabilitados' => Configuracion::estaHabilitadoCargarCertificado(),
            ]);
        }

        // ── ADMIN / JEFE PEEC / PERSONAL INTERNO ────────────────────
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
