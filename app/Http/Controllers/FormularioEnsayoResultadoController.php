<?php

namespace App\Http\Controllers;

use App\Models\FormularioEnsayoResultado;
use App\Models\Permiso;
use App\Models\RespuestaFormulario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use stdClass;

class FormularioEnsayoResultadoController extends Controller
{
    // public function store(Request $request)
    // {
    //     Log::info('$request->all()');
    //     Log::info($request->all());
    //     return 1;
    //     $data = $request->validate([
    //         'id_formulario' => 'required|exists:formularios,id',
    //         'id_laboratorio' => 'required|exists:laboratorios,id',
    //         'id_ciclo' => 'required|exists:ciclos,id',
    //         'observaciones' => 'nullable|string',
    //         'estado' => 'required|integer',
    //         'gestion' => 'required|string|max:6',
    //         'cod_lab' => 'nullable|string|max:20',
    //         'nombre_lab' => 'required|string|max:100',
    //         'departamento' => 'required|string|max:255',
    //         'fecha_envio' => 'nullable|date',
    //         'respuestas' => 'required|array|min:1',
    //         'respuestas.*.id_parametro' => 'required|exists:parametros,id',
    //         'respuestas.*.nombre' => 'required|string|max:255',
    //         'respuestas.*.visible_nombre' => 'boolean',
    //         'respuestas.*.respuestas' => 'required|array', // aquí irá el JSON de cada campo
    //     ]);

    //     return DB::transaction(function () use ($data) {
    //         $resultado = FormularioEnsayoResultado::create([
    //             'id_formulario' => $data['id_formulario'],
    //             'id_laboratorio' => $data['id_laboratorio'],
    //             'id_ciclo' => $data['id_ciclo'],
    //             'observaciones' => $data['observaciones'] ?? null,
    //             'estado' => $data['estado'],
    //             'gestion' => $data['gestion'],
    //             'cod_lab' => $data['cod_lab'] ?? null,
    //             'nombre_lab' => $data['nombre_lab'],
    //             'departamento' => $data['departamento'],
    //             'fecha_envio' => $data['fecha_envio'] ?? null,
    //             'estructura' => null, // puedes guardar copia de la estructura aquí si lo deseas
    //         ]);

    //         // Insertar respuestas
    //         foreach ($data['respuestas'] as $resp) {
    //             RespuestaFormulario::create([
    //                 'id_formulario_resultado' => $resultado->id,
    //                 'id_parametro' => $resp['id_parametro'],
    //                 'nombre' => $resp['nombre'],
    //                 'visible_nombre' => $resp['visible_nombre'] ?? true,
    //                 'respuestas' => json_encode($resp['respuestas']), // guarda en JSON
    //             ]);
    //         }

    //         return response()->json([
    //             'message' => 'Respuestas almacenadas correctamente ✅',
    //             'resultado_id' => $resultado->id
    //         ], 201);
    //     });
    // }

    // public function store(Request $request)
    // {
    //     if (!Gate::any([Permiso::LABORATORIO])) {
    //         return redirect('/')->with('error', 'No tienes permisos para realizar esta acción.');
    //     }

    //     $user = Auth::user();
    //     $laboratorio = $user->laboratorio;
    //     Log::info('$laboratorio');
    //     Log::info($laboratorio);
    //     $formularioResultado = FormularioEnsayoResultado::create([
    //         'observaciones' => $request->observaciones,
    //         'estado' => 1,
    //         'id_ciclo' => 1,
    //         'id_laboratorio' => $laboratorio->id,
    //         'id_formulario' => $request->id_formulario,
    //         'gestion' => now()->year,
    //         'nombre_lab' => $laboratorio->nombre_lab,
    //         'departamento' => $laboratorio->departamento->nombre_dep
    //     ]);
    //     $secciones = [];
    //     foreach ($request->secciones as $seccionData) {
    //         $sec = new stdClass();
    //         $sec->nombre = $seccionData['nombre'];
    //         $sec->id = $seccionData['id'];
    //         $secciones['secciones'][] = $sec;
    //         foreach ($seccionData['parametros'] as $parametroData) {
    //             RespuestaFormulario::create([
    //                 'id_formulario_resultado' => $formularioResultado->id,
    //                 'id_parametro' => $parametroData['id'],
    //                 'nombre' => $parametroData['nombre'],
    //                 'visible_nombre' => $parametroData['visible_nombre'] ?? false,
    //                 'respuestas' => $parametroData['campos']
    //             ]);
    //         }
    //     }
    //     $formularioResultado->estructura = $secciones;
    //     $formularioResultado->save();
    //     return response()->json([
    //         'message' => 'Formulario guardado correctamente',
    //         'id' => $formularioResultado->id
    //     ]);
    // }


    public function testAdmin(Request $request)
    {
        $user = Auth::user();
        // Simulación de "FormularioEnsayoResultado"
        $formularioResultado = [
            'id' => 0, // como es test, no se guarda en DB
            'observaciones' => $request->observaciones ?? '',
            'estado' => 1,
            'id_ciclo' => 1,
            'id_laboratorio' => 1,
            'id_formulario' => $request->id_formulario,
            'gestion' => now()->year,
            'nombre_lab' => '$laboratorio->nombre_lab',
            'departamento' => '$laboratorio->departamento->nombre_dep',
            'estructura' => []
        ];

        $secciones = [];
        foreach ($request->secciones as $seccionData) {
            $sec = [
                'id' => $seccionData['id'],
                'nombre' => $seccionData['nombre'],
                'parametros' => []
            ];

            foreach ($seccionData['parametros'] as $parametroData) {
                $param = [
                    'id' => $parametroData['id'],
                    'nombre' => $parametroData['nombre'],
                    'visible_nombre' => $parametroData['visible_nombre'] ?? false,
                    'respuestas' => $parametroData['campos'] ?? []
                ];
                $sec['parametros'][] = $param;
            }

            $secciones[] = $sec;
        }

        $formularioResultado['estructura'] = $secciones;

        return response()->json([
            'success' => true,
            'mensaje' => 'Test Admin: datos simulados generados correctamente.',
            'data' => $formularioResultado
        ]);
    }


    public function store(Request $request)
    {
        if (!Gate::any([Permiso::LABORATORIO])) {
            return redirect('/')->with('error', 'No tienes permisos para realizar esta acción.');
        }
        $user = Auth::user();
        $laboratorio = $user->laboratorio;
        $formularioResultado = FormularioEnsayoResultado::find($request->id_formulario_ensayos_resultados);
        $esActualizacion = false;

        if (!$formularioResultado) {
            $formularioResultado = FormularioEnsayoResultado::create([
                'observaciones' => $request->observaciones,
                'estado' => 1,
                'id_ciclo' => 1,
                'id_laboratorio' => $laboratorio->id,
                'id_formulario' => $request->id_formulario,
                'gestion' => now()->year,
                'nombre_lab' => $laboratorio->nombre_lab,
                'departamento' => $laboratorio->departamento->nombre_dep
            ]);
        } else {
            $formularioResultado->observaciones = $request->observaciones;
            $formularioResultado->save();
            $esActualizacion = true;
        }

        $secciones = [];
        foreach ($request->secciones as $seccionData) {
            $sec = new \stdClass();
            $sec->nombre = $seccionData['nombre'];
            $sec->id = $seccionData['id'];
            $secciones['secciones'][] = $sec;
            foreach ($seccionData['parametros'] as $parametroData) {
                RespuestaFormulario::updateOrCreate(
                    [
                        'id_formulario_resultado' => $formularioResultado->id,
                        'id_parametro' => $parametroData['id'],
                    ],
                    [
                        'nombre' => $parametroData['nombre'],
                        'visible_nombre' => $parametroData['visible_nombre'] ?? false,
                        'respuestas' => $parametroData['campos']
                    ]
                );
            }
        }

        $formularioResultado->estructura = $secciones;
        $formularioResultado->save();
        $mensaje = $esActualizacion
            ? 'Formulario actualizado correctamente.'
            : 'Formulario registrado correctamente.';

        return redirect()->back()->with('success', $mensaje);
    }
}
