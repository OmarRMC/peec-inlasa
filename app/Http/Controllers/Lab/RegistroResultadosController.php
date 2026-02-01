<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use App\Models\EnsayoAptitud;
use App\Models\Formulario;
use App\Models\FormularioEnsayo;
use App\Models\InscripcionEA;
use App\Models\InscripcionEAFormulario;
use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class RegistroResultadosController extends Controller
{
    function listaEnsayosInscritos()
    {
        if (!Gate::any([Permiso::LABORATORIO])) {
            return redirect('/')->with('error', 'No tienes permisos para realizar esta acción.');
        }
        $lab = Auth::user()->laboratorio;
        $ensayos = InscripcionEA::with([
            'ensayoAptitud.paquete'
        ])
            ->whereHas('inscripcion', function ($q) use ($lab) {
                $q->where('id_lab', $lab->id)
                    ->aprobadoOrVencido()
                    ->where('gestion', now()->year);
            })
            ->get();
        return view('laboratorio.inscripcion.ensayos', compact('ensayos'));
    }

    function getFormulariosByEa($id)
    {
        if (!Gate::any([Permiso::LABORATORIO])) {
            return redirect('/')->with('error', 'No tienes permisos para realizar esta acción.');
        }
        $ensayo = EnsayoAptitud::find($id);
        if (!$ensayo) {
            return redirect('/')->with('error', 'Ensayo no encontrado.');
        }
        $formularios = $ensayo->formularios()->activo()->get();
        $ciclos = $ensayo->getEstadoCiclosYResultados();
        $cicloActivo = $ciclos['cicloActivo'];
        $cicloSiguiente = $ciclos['siguienteCiclo'];
        $estado = $ciclos['estado'];
        return view('laboratorio.resultados.formularios', compact('formularios', 'ensayo', 'cicloActivo', 'cicloSiguiente', 'estado'));
    }

    function formularioLlenar($id, $idEA)
    {
        if (!Gate::any([Permiso::LABORATORIO])) {
            return redirect('/')->with('error', 'No tienes permisos para realizar esta acción.');
        }
        $laboratorio = Auth::user()->laboratorio;
        $inscripciones = $laboratorio->inscripciones()
            ->where('gestion', now()->year)
            ->aprobadoOrVencido()
            ->get();
        $ensayos = $inscripciones->flatMap(function ($inscripcion) use ($idEA) {
            return $inscripcion->ensayos()->where('id_ea', $idEA)->get();
        });
        $ensayo = $ensayos->first();
        if (!$ensayo) {
            return redirect('/')->with('error', 'No se tiene el registro del ensayo');
        }
        $ensayoAptitud = EnsayoAptitud::find($idEA);
        if (!$ensayoAptitud) {
            return redirect('/')->with('error', 'No se tiene el registro del ensayo');
        }        
        $formulario = $ensayoAptitud->formularios()->with(['secciones.parametros.campos.grupoSelector.opciones'])->find($id);
        if (!$formulario) {
            return redirect('/')->with('error', 'No se tiene el registro del formulario activo');
        }
        $registro = InscripcionEAFormulario::where('formulario_id', $id)
            ->where('inscripcion_ea_id', $ensayo->id)
            ->first();
        $cantidad = $registro->cantidad ?? 1;
        $cicloId = $ensayoAptitud->getCicloActivo()?->id;
        if (!$cicloId) {
            return redirect('/')->with('error', 'No se tiene el ciclo activo');
        }
        $respuestas = $laboratorio->respuestas()->where('id_ciclo', $cicloId)->where('gestion', now()->year)->where('id_formulario', $formulario->id)->get();
        $respuestas->load(['respuestas']);
        // return $respuestas;
        return view('laboratorio.resultados.llenar', compact('formulario', 'laboratorio', 'cantidad', 'idEA', 'respuestas'));
    }

    function guardarResultados(Request $request, $id)
    {
        if (!Gate::any([Permiso::LABORATORIO])) {
            return redirect('/')->with('error', 'No tienes permisos para realizar esta acción.');
        }
        $laboratorio = Auth::user()->laboratorio;
        $formulario  = FormularioEnsayo::with(['secciones.parametros.grupoSelector.opciones'])->find($id);
        if (!$formulario) {
            return redirect('/')->with('error', 'Formulario no encontrado.');
        }

        $data = $request->all();


        return redirect()->route('lab.inscritos-ensayos.formularios.llenar', ['id' => $id])->with('success', 'Resultados guardados correctamente.');
    }
}
