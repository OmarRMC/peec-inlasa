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
        if (!Gate::any([Permiso::ADMIN, Permiso::RESPONSABLE])) {
            return redirect('/')->with('error', 'Acceso restringido.');
        }

        $responsable = Auth::user()->load(['responsablesEA', 'responsablesEA.paquete']);
        $ensayo = $responsable->responsablesEA()->find($idEA);
        if (!$ensayo) {
            return redirect('/')->with('error', 'Acceso restringido.');
        }

        $ciclo = Ciclo::find($idCiclo);
        if (!$ciclo) {
            return redirect('/')->with('error', 'No se tiene registrado el ciclo.');
        }

        $lab = Laboratorio::find($idLab); // Necesitas traer los datos del laboratorio

        $resultados = FormularioEnsayoResultado::with('respuestas', 'formulario')
            ->where('id_laboratorio', $idLab)
            ->where('id_ciclo', $idCiclo)
            ->where('id_ensayo', $ensayo->id)
            ->get();

        // return $resultados; 
        return view('responsable.resultados.lab.index', compact('ensayo', 'ciclo', 'lab', 'resultados'));
    }

    public function previewResultado($id)
    {

        if (!Gate::any([Permiso::ADMIN, Permiso::RESPONSABLE])) {
            return redirect('/')->with('error', 'Acceso restringido.');
        }

        $resultados = FormularioEnsayoResultado::with(['respuestas', 'formulario'])->where('id', $id)->get();
        $resultado  = $resultados->first();
        $idEA = $resultado->id_ensayo;
        $idCiclo = $resultado->id_ciclo;

        $responsable = Auth::user()->load(['responsablesEA', 'responsablesEA.paquete']);
        $ensayo = $responsable->responsablesEA()->find($idEA);
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
