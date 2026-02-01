<?php

namespace App\Http\Controllers\lab;

use App\Http\Controllers\Controller;
use App\Models\Ciclo;
use App\Models\InformeTecnico;
use App\Models\InscripcionEA;
use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class InformeTecnicoController extends Controller
{
    public function labInformes(Request $request)
    {
        if (!Gate::any([Permiso::LABORATORIO])) {
            return redirect('/')->with('error', 'No tienes permisos para realizar esta acción.');
        }
        $lab = Auth::user()->laboratorio;
        $gestion = $request->get('gestion', now()->year);
        $gestiones = $lab->inscripciones()
            ->aprobadoOrVencido()
            ->select('gestion')
            ->distinct()
            ->orderBy('gestion', 'desc')
            ->pluck('gestion');

        $ensayos = InscripcionEA::with([
            'ensayoAptitud.paquete',
            'ensayoAptitud.ciclos' => function ($q) use ($gestion, $lab) {
                $q->gestion($gestion)
                    ->with([
                        'informes' => function ($qi) use ($lab) {
                            $qi->where('id_laboratorio', $lab->id);
                        }
                    ]);
            }
        ])
            ->whereHas('inscripcion', function ($q) use ($lab, $gestion) {
                $q->where('id_lab', $lab->id)
                    ->aprobadoOrVencido()
                    ->where('gestion', $gestion);
            })
            ->get();
        $infomes = InformeTecnico::whereIn('id_ciclo', [])->where('id_laboratorio', $lab->id)->get();
        return view('informe_tecnico.lab.index', compact('ensayos', 'gestiones'));
    }
}
