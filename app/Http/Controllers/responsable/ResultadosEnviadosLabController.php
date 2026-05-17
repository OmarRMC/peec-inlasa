<?php

namespace App\Http\Controllers\responsable;

use App\Http\Controllers\Controller;
use App\Models\Ciclo;
use App\Models\FormularioEnsayoResultado;
use App\Models\Laboratorio;
use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ResultadosEnviadosLabController extends Controller
{
    public function listadoRespuestasEnviados(Request $request, $idEA, $idCiclo, $idLab)
    {
        if (!Gate::any([Permiso::ADMIN, Permiso::RESPONSABLE, Permiso::JEFE_PEEC])) {
            return redirect('/')->with('error', 'Acceso restringido.');
        }

        if (Gate::allows(Permiso::JEFE_PEEC)) {
            $ensayo = \App\Models\EnsayoAptitud::find($idEA);
        } else {
            $ensayo = Auth::user()->load(['responsablesEA', 'responsablesEA.paquete'])->responsablesEA()->find($idEA);
        }
        if (!$ensayo) {
            return redirect('/')->with('error', 'Acceso restringido.');
        }

        $ciclo = Ciclo::find($idCiclo);
        if (!$ciclo) {
            return redirect('/')->with('error', 'No se tiene registrado el ciclo.');
        }

        $lab = Laboratorio::find($idLab); // Necesitas traer los datos del laboratorio

        $resultados = FormularioEnsayoResultado::with('respuestasCampo.parametroCampo', 'formulario')
            ->where('id_laboratorio', $idLab)
            ->where('id_ciclo', $idCiclo)
            ->get();
        $resultados->each(fn ($r) => $r->setRelation('respuestas', $r->respuestas_compat));

        // return $resultados; 
        return view('responsable.resultados.lab.index', compact('ensayo', 'ciclo', 'lab', 'resultados'));
    }

    public function previewResultado($id)
    {

        if (!Gate::any([Permiso::ADMIN, Permiso::RESPONSABLE, Permiso::JEFE_PEEC])) {
            return redirect('/')->with('error', 'Acceso restringido.');
        }

        $resultados = FormularioEnsayoResultado::with(['respuestasCampo.parametroCampo', 'formulario', 'ciclo'])->where('id', $id)->get();
        $resultados->each(fn ($r) => $r->setRelation('respuestas', $r->respuestas_compat));
        $resultado  = $resultados->first();
        $idCiclo = $resultado->id_ciclo;
        $idEA = $resultado->ciclo?->id_ensayo;

        if (Gate::allows(Permiso::JEFE_PEEC)) {
            $ensayo = \App\Models\EnsayoAptitud::with('paquete')->find($idEA);
        } else {
            $ensayo = Auth::user()->load(['responsablesEA', 'responsablesEA.paquete'])->responsablesEA()->find($idEA);
        }
        if (!$ensayo) {
            return redirect('/')->with('error', 'Acceso restringido.');
        }

        $ciclo = Ciclo::find($idCiclo);
        if (!$ciclo) {
            return redirect('/')->with('error', 'No se tiene registrado el ciclo.');
        }
        $formulario = $resultado->formulario;
        $respuestas = $resultados;
        $laboratorio = $resultado->laboratorio;
        $cantidad = 1;
        $readonly = true;
        $fechaActualizacion = formatDate($resultado->fecha_envio);
        $fechaRegistro = formatDate($resultado->created_at);
        return view('responsable.resultados.lab.preview', compact('formulario', 'laboratorio', 'cantidad', 'respuestas', 'readonly', 'ensayo', 'fechaRegistro', 'fechaActualizacion'));
    }
}
