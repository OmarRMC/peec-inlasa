<?php

namespace App\Http\Controllers\responsable;

use App\Http\Controllers\Controller;
use App\Models\Laboratorio;
use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class ReporteResultadosController extends Controller
{
    public function reporteEnsayos()
    {
        if (!Gate::any(Permiso::RESPONSABLE)) {
            return redirect('/')->with('error', 'Acceso restringido. No dispones de los permisos requeridos para realizar esta operación.');
        }
        $resposable  = Auth::user()->load(['responsablesEA', 'responsablesEA.paquete']);
        $ensayos = $resposable->responsablesEA;
        return view('reportes.ensayos.respuestas.index', compact('ensayos'));
    }

    public function exportEnsayoResultados() {}

    public function resultadosRegistradosPorLab(Request $request, $idEA)
    {
        if (!Gate::any(Permiso::RESPONSABLE)) {
            return redirect('/')->with('error', 'Acceso restringido. No dispones de los permisos requeridos para realizar esta operación.');
        }

        $responsable = Auth::user()->load(['responsablesEA', 'responsablesEA.paquete']);
        $ensayo = $responsable->responsablesEA()->find($idEA);

        if (!$ensayo) {
            return redirect('/')->with('error', 'Acceso restringido. No dispones de los permisos requeridos para realizar esta operación.');
        }



        $gestion = $request->input('gestion', date('Y'));

        // Obtener ciclos y ciclo activo
        $ciclos = $ensayo->ciclos()->gestion($gestion)->activo()->get();
        if ($ciclos->isEmpty()) {
            return redirect('/')->with('error', 'No se tiene registrados los ciclos');
        }
        if ($gestion == date('Y')) {
            $cicloActivo = $ensayo->getCicloActivo();
        } else {
            $cicloActivo = $ensayo->getCicloActivo($gestion);
        }
        $cicloId = $request->input('idCiclo', $cicloActivo?->id);

        // Búsqueda por nombre o código de laboratorio
        $search = trim($request->input('search', ''));

        $labsQuery = Laboratorio::query()
            ->whereHas('respuestas', function ($query) use ($ensayo, $cicloId, $gestion) {
                $query->where('id_ensayo', $ensayo->id)
                    ->where('id_ciclo', $cicloId)
                    ->where('gestion', $gestion);
            });

        if ($search !== '') {
            $labsQuery->where(function ($q) use ($search) {
                $q->where('cod_lab', 'LIKE', "%{$search}%")
                    ->orWhere('nombre_lab', 'LIKE', "%{$search}%")
                    ->orWhere('wapp_lab', 'LIKE', "%{$search}%");
            });
        }

        $labs = $labsQuery->orderBy('cod_lab', 'desc')->paginate(10);

        return view('resultados.labs.index', compact('labs', 'ciclos', 'cicloId', 'gestion', 'search', 'idEA', 'ensayo'));
    }
}
