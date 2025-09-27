<?php

namespace App\Http\Controllers;

use App\Models\EnsayoAptitud;
use App\Models\Formulario;
use App\Models\FormularioEnsayo;
use App\Models\Parametro;
use App\Models\ParametroCampo;
use App\Models\Programa;
use App\Models\Seccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FormularioEnsayoController extends Controller
{
    public function formulariosIndex(Request $request)
    {
        $search = $request->input('search');

        $query = Programa::Active()
            ->with(['areas.paquetes.ensayosAptitud' => function ($q) use ($search) {
                if ($search) {
                    $q->where('descripcion', 'LIKE', "%{$search}%");
                }
                $q->orderBy('descripcion', 'asc');
            }]);

        $programas = $query->paginate(10);

        return view('admin.formularios.index', compact('programas', 'search'));
    }

    public function preview($id)
    {
        $formulario = FormularioEnsayo::find($id);
        if (!$formulario) {
            return redirect()->route('admin.formularios.ea')->with('error', 'Formulario no encontrado.');
        }
        $primaryColor = $formulario->color_primario;
        $secondaryColor = $formulario->color_secundario;
        return view('admin.formularios.preview', compact('formulario', 'primaryColor', 'secondaryColor'));
    }

    public function formulariosByEa($idEA)
    {
        $ensayo = EnsayoAptitud::with(['formularios'])->find($idEA);
        if (!$ensayo) {
            return redirect()->route('admin.formularios.ea')->with('error', 'Ensayo de Aptitud no encontrado.');
        }
        Log::info('$ensayo');
        Log::info($ensayo);
        $formularios = $ensayo->formularios;
        Log::info('$formularios');
        Log::info($formularios);

        $formulariosDisponibles = FormularioEnsayo::where('id_ensayo', '!=', $ensayo->id)->activo()->get();

        return view('admin.formularios.show', compact('ensayo', 'formularios', 'formulariosDisponibles'));
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'id_ensayo' => ['required', 'exists:ensayo_aptitud,id'],
                'nombre'            => ['required', 'string', 'max:255'],
                'codigo'            => ['nullable', 'string', 'max:80', 'unique:formularios,codigo'],
                'nota'              => ['nullable', 'string'],
                'color_primario'    => ['nullable', 'string', 'max:10'],
                'color_secundario'  => ['nullable', 'string', 'max:10'],
                'estado'            => ['nullable', 'boolean'],
                'editable_por_encargado' => ['nullable', 'boolean']
            ],
            [
                'id_ensayo.required' => 'El campo ensayo de aptitud es obligatorio.',
                'id_ensayo.exists'   => 'El ensayo de aptitud seleccionado no existe.',
                'nombre.required'            => 'El nombre del formulario es obligatorio.',
                'nombre.max'                 => 'El nombre no puede superar los 255 caracteres.',
                'codigo.max'                 => 'El código no puede superar los 80 caracteres.',
                'codigo.unique'              => 'El código ya está en uso. Debe ser único.',
                'nota.string'                => 'La nota debe ser una cadena de texto.',
                'color_primario.max'         => 'El color primario no puede superar los 10 caracteres.',
                'color_secundario.max'       => 'El color secundario no puede superar los 10 caracteres.',
                'estado.boolean'             => 'El estado debe ser verdadero o falso.',
                'editable_por_encargado.boolean' => 'El campo editable por encargado debe ser verdadero o falso.'
            ]
        );
        $ensayo = EnsayoAptitud::find($request->id_ensayo);
        if (!$ensayo) {
            return redirect()->back()->with('error', 'Ensayo de Aptitud no encontrado.');
        }


        $formulario = new FormularioEnsayo();
        $formulario->id_ensayo = $ensayo->id;
        $formulario->nombre = $request->nombre;
        $formulario->codigo = $request->codigo;
        $formulario->nota = $request->nota;
        $formulario->color_primario = $request->color_primario;
        $formulario->color_secundario = $request->color_secundario;
        $formulario->estado = $request->estado ?? false;
        $formulario->editable_por_encargado = $request->editable_por_encargado ?? false;
        $formulario->save();
        $formulario->ensayos()->attach($request->id_ensayo);
        return redirect()
            ->back()
            ->with('success', 'Formulario creado exitosamente.');
    }

    public function update(Request $request, $id)
    {
        Log::info($id);
        $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo' => 'nullable|string|max:80',
            'nota' => 'nullable|string',
            'color_primario' => 'required|string|max:10',
            'color_secundario' => 'required|string|max:10',
        ]);
        $formulario = FormularioEnsayo::find($id);
        if (!$formulario) {
            return redirect()->back()->with('error', 'Formulario no encontrado.');
        }

        $formulario->id_ensayo = $request->id_ensayo;
        $formulario->nombre = $request->nombre;
        $formulario->codigo = $request->codigo;
        $formulario->nota = $request->nota;
        $formulario->color_primario = $request->color_primario;
        $formulario->color_secundario = $request->color_secundario;
        $formulario->estado = $request->estado;
        $formulario->editable_por_encargado = $request->editable_por_encargado ?? false;
        $formulario->update();

        return redirect()->back()->with('success', 'Formulario actualizado correctamente.');
    }

    public function destroy(FormularioEnsayo $formulario)
    {
        $formulario->delete();

        return redirect()->back()->with('success', 'Formulario eliminado correctamente.');
    }

    public function edit($id, $idEa)
    {
        $formulario = FormularioEnsayo::with([
            'secciones.parametros.campos.grupoSelector.opciones'
        ])->find($id);
        if (!$formulario) {
            return redirect()->route('admin.formularios.ea')->with('error', 'Formulario no encontrado.');
        }
        $ensayo = $formulario->ensayos->firstWhere('id', $idEa);
        if (!$ensayo) {
            return redirect()->route('admin.formularios.ea')->with('error', 'Ensayo de Aptitud no asociado al formulario.');
        }
        $grupos = $ensayo->gruposSelectores()->get();
        $camposBD = $formulario->secciones->pluck('parametros')->flatten()->pluck('campos')->flatten();
        Log::info('$campos->');
        Log::info($camposBD);

        // return 1; 
        return view('admin.formularios.edit', compact('formulario', 'grupos', 'ensayo', 'camposBD'));
    }



    public function updateEstructura(Request $request, $id)
    {
        $formulario = FormularioEnsayo::findOrFail($id);
        $data = $request->validate([
            'secciones' => 'nullable|array|min:1',
            'secciones.*.id' => 'nullable|exists:secciones,id',
            'secciones.*.nombre' => 'required|string|max:255',
            'secciones.*.descripcion' => 'nullable|string|max:1000',
            'secciones.*.cantidad_parametros' => 'nullable|integer|min:0',
            'secciones.*.headers' => 'nullable|array',
            'secciones.*.headers.*' => 'string|max:255',

            'secciones.*.parametros' => 'required|array',
            'secciones.*.parametros.*.id' => 'nullable|exists:parametros,id',
            'secciones.*.parametros.*.requerido_si_completa' => 'nullable',
            'secciones.*.parametros.*.visible_nombre' => 'nullable',
            'secciones.*.parametros.*.nombre' => 'required|string|max:255',
            'secciones.*.parametros.*.campos' => 'nullable|array',
            'secciones.*.parametros.*.campos.*.id' => 'nullable|exists:parametro_campos,id',
            'secciones.*.parametros.*.campos.*.label' => 'required|string|max:255',
            'secciones.*.parametros.*.campos.*.tipo' => 'required|string|in:text,number,date,select,checkbox,textarea,datalist',
            'secciones.*.parametros.*.campos.*.placeholder' => 'nullable|string|max:255',
            'secciones.*.parametros.*.campos.*.unidad' => 'nullable|string|max:50',
            'secciones.*.parametros.*.campos.*.requerido' => 'nullable',
            'secciones.*.parametros.*.campos.*.posicion' => 'nullable|integer|min:0',
            'secciones.*.parametros.*.campos.*.mensaje' => 'nullable|string|max:500',
            'secciones.*.parametros.*.campos.*.step' => 'nullable|string|max:50',
            'secciones.*.parametros.*.campos.*.pattern' => 'nullable|string|max:255',
            'secciones.*.parametros.*.campos.*.range' => 'nullable|string|max:255',
            'secciones.*.parametros.*.campos.*.id_grupo_selector' => 'nullable|exists:grupos_selectores,id',
            'secciones.*.parametros.*.campos.*.id_campo_padre' => 'nullable|exists:parametro_campos,id',
        ]);

        DB::transaction(function () use ($formulario, $data) {

            $existingSeccionesIds = $formulario->secciones()->pluck('id')->toArray();
            $requestSeccionesIds = collect($data['secciones'] ?? [])->pluck('id')->filter()->toArray();

            $seccionesToDelete = array_diff($existingSeccionesIds, $requestSeccionesIds);
            Seccion::destroy($seccionesToDelete);

            foreach ($data['secciones'] ?? [] as $secIdx => $sec) {

                $seccion = $formulario->secciones()->updateOrCreate(
                    ['id' => $sec['id'] ?? null],
                    [
                        'nombre' => $sec['nombre'],
                        'descripcion' => $sec['descripcion'] ?? null,
                        'cantidad_parametros' => max((int)($sec['cantidad_parametros'] ?? 0), count($sec['parametros'])),
                        'posicion' => $secIdx,
                        'headers' => $sec['headers'] ?? [],
                    ]
                );

                $existingParametrosIds = $seccion->parametros()->pluck('id')->toArray();
                $requestParametrosIds = collect($sec['parametros'] ?? [])->pluck('id')->filter()->toArray();
                $parametrosToDelete = array_diff($existingParametrosIds, $requestParametrosIds);
                Parametro::destroy($parametrosToDelete);

                foreach ($sec['parametros'] ?? [] as $paramIdx => $param) {
                    $parametro = $seccion->parametros()->updateOrCreate(
                        ['id' => $param['id'] ?? null],
                        [
                            'nombre' => $param['nombre'],
                            'visible_nombre' => $param['visible_nombre']??false,
                            'requerido_si_completa' => $param['requerido_si_completa']??false
                        ]
                    );

                    $existingCamposIds = $parametro->campos()->pluck('id')->toArray();
                    $requestCamposIds = collect($param['campos'] ?? [])->pluck('id')->filter()->toArray();
                    $camposToDelete = array_diff($existingCamposIds, $requestCamposIds);
                    Log::info($camposToDelete);
                    ParametroCampo::destroy($camposToDelete);

                    foreach ($param['campos'] ?? [] as $campoIdx => $campo) {
                        $id_campo_padre = $campo['id_campo_padre'] ?? null;
                        $parentExists = ParametroCampo::find($id_campo_padre);
                        if (!$parentExists) {
                            $id_campo_padre = null;
                        }
                        $campoModel = $parametro->campos()->updateOrCreate(
                            ['id' => $campo['id'] ?? null],
                            [
                                'nombre' => $campo['nombre'] ?? '',
                                'label' => $campo['label'],
                                'tipo' => $campo['tipo'],
                                'placeholder' => $campo['placeholder'] ?? null,
                                'unidad' => $campo['unidad'] ?? null,
                                'requerido' => isset($campo['requerido']),
                                'posicion' => $campo['posicion'] ?? $campoIdx,
                                'mensaje' => $campo['mensaje'] ?? null,
                                'step' => $campo['step'] ?? null,
                                'pattern' => $campo['pattern'] ?? null,
                                'range' => $campo['range'] ?? null,
                                'id_grupo_selector' => $campo['id_grupo_selector'] ?? null,
                                'id_parametro' => $parametro->id,
                                'id_campo_padre' => $id_campo_padre,

                            ]
                        );

                        // Rango min/max
                        if (($campo['tipo'] == 'text' || $campo['tipo'] == 'number') && isset($campo['range'])) {
                            $clean = trim($campo['range'], '[]');
                            [$min, $max] = explode('-', $clean);
                            if ($campo['tipo'] === 'text') {
                                $campoModel->minlength = $min ?? null;
                                $campoModel->maxlength = $max ?? null;
                            } else {
                                $campoModel->min = $min ?? null;
                                $campoModel->max = $max ?? null;
                            }
                            $campoModel->save();
                        }
                    }
                }
            }
        });

        return redirect()->back()->with('success', 'Formulario actualizado correctamente ✅');
    }

    // public function updateEstructura(Request $request, $id)
    // {
    //     $formulario = FormularioEnsayo::findOrFail($id);
    //     $data = $request->validate([
    //         'secciones' => 'nullable|array|min:1',
    //         'secciones.*.nombre' => 'required|string|max:255',
    //         'secciones.*.descripcion' => 'nullable|string|max:1000',
    //         'secciones.*.cantidad_parametros' => 'nullable|integer|min:0',
    //         'secciones.*.headers' => 'nullable|array',
    //         'secciones.*.headers.*' => 'string|max:255',

    //         'secciones.*.parametros' => 'required|array',
    //         'secciones.*.parametros.*.nombre' => 'required|string|max:255',
    //         'secciones.*.parametros.*.campos' => 'nullable|array',
    //         'secciones.*.parametros.*.campos.*.label' => 'required|string|max:255',
    //         'secciones.*.parametros.*.campos.*.tipo' => 'required|string|in:text,number,date,select,checkbox,textarea,datalist',
    //         'secciones.*.parametros.*.campos.*.placeholder' => 'nullable|string|max:255',
    //         'secciones.*.parametros.*.campos.*.unidad' => 'nullable|string|max:50',
    //         'secciones.*.parametros.*.campos.*.requerido' => 'nullable',
    //         'secciones.*.parametros.*.campos.*.posicion' => 'nullable|integer|min:0',
    //         'secciones.*.parametros.*.campos.*.mensaje' => 'nullable|string|max:500',
    //         'secciones.*.parametros.*.campos.*.step' => 'nullable|string|max:50',
    //         'secciones.*.parametros.*.campos.*.pattern' => 'nullable|string|max:255',
    //         'secciones.*.parametros.*.campos.*.range' => 'nullable|string|max:255',
    //         'secciones.*.parametros.*.campos.*.id_grupo_selector' => 'nullable|exists:grupos_selectores,id',
    //         'secciones.*.parametros.*.campos.*.id_campo_padre' => 'nullable|exists:parametro_campos,id',
    //     ]);

    //     DB::transaction(function () use ($formulario, $data) {
    //         $formulario->secciones()->each(function ($seccion) {
    //             $seccion->parametros()->each(function ($param) {
    //                 $param->campos()->delete();
    //             });
    //             $seccion->parametros()->delete();
    //             $seccion->delete();
    //         });

    //         $data['secciones'] = $data['secciones'] ?? [];
    //         foreach ($data['secciones'] as $secIdx => $sec) {
    //             $seccion = $formulario->secciones()->create([
    //                 'nombre' => $sec['nombre'],
    //                 'descripcion' => $sec['descripcion'] ?? null,
    //                 'cantidad_parametros' => max((int)($sec['cantidad_parametros'] ?? 0), count($sec['parametros'])),
    //                 'posicion' => $secIdx,
    //                 'headers' => isset($sec['headers']) ? $sec['headers'] : [],
    //             ]);

    //             foreach ($sec['parametros'] as $paramIdx => $param) {
    //                 $parametro = $seccion->parametros()->create([
    //                     'nombre' => $param['nombre'],
    //                 ]);
    //                 foreach ($param['campos'] ?? [] as $campoIdx => $campo) {
    //                     $campoModel = new ParametroCampo();
    //                     $campoModel->nombre = $campo['nombre'] ?? '';
    //                     $campoModel->label = $campo['label'];
    //                     $campoModel->tipo = $campo['tipo'];
    //                     $campoModel->placeholder = $campo['placeholder'] ?? null;
    //                     $campoModel->unidad = $campo['unidad'] ?? null;
    //                     $campoModel->requerido = isset($campo['requerido']);
    //                     $campoModel->posicion  = $campo['posicion'] ?? $campoIdx;
    //                     $campoModel->mensaje = $campo['mensaje'] ?? null;
    //                     $campoModel->step  = $campo['step'] ?? null;
    //                     $campoModel->pattern = $campo['pattern'] ?? null;
    //                     $campoModel->range = $campo['range'] ?? null;
    //                     $campoModel->id_grupo_selector = $campo['id_grupo_selector'] ?? null;
    //                     $campoModel->id_parametro = $parametro->id;

    //                     if (($campo['tipo'] == 'text' || $campo['tipo'] == 'number') && isset($campo['range'])) {
    //                         $clean = trim($campo['range'], '[]');
    //                         [$min, $max] = explode('-', $clean);
    //                         if ($campo['tipo'] === 'text') {
    //                             $campoModel->minlength = $min ?? null;
    //                             $campoModel->maxlength = $max ?? null;
    //                         } else {
    //                             $campoModel->min = $min ?? null;
    //                             $campoModel->max = $max ?? null;
    //                         }
    //                     }
    //                     $campoModel->id_campo_padre = $campo['id_campo_padre'] ?? null;
    //                     $campoModel->save();    
    //                 }
    //             }
    //         }
    //     });
    //     return redirect()->back()
    //         ->with('success', 'Formulario actualizado correctamente ✅');
    // }

    public function usar(Request $request, $id)
    {
        $request->validate([
            'formulario_id' => 'required|exists:formularios,id',
        ]);
        $formularioBase = FormularioEnsayo::find($request->formulario_id);
        $ensayo = EnsayoAptitud::find($id);

        if (!$ensayo) {
            return redirect()->back()->with('error', 'Ensayo de Aptitud no encontrado.');
        }

        if (!$formularioBase) {
            return redirect()->back()->with('error', 'Formulario no encontrado.');
        }
        $nuevoFormulario = $formularioBase->replicate();
        $nuevoFormulario->id_ensayo = $ensayo->id;
        $nuevoFormulario->save();

        $formularioBase->load('secciones.parametros');
        foreach ($formularioBase->secciones as $seccionBase) {
            $nuevaSeccion = new Seccion();
            $nuevaSeccion->id_formulario = $nuevoFormulario->id;
            $nuevaSeccion->nombre = $seccionBase->nombre;
            $nuevaSeccion->descripcion = $seccionBase->descripcion;
            $nuevaSeccion->cantidad_parametros = $seccionBase->cantidad_parametros;
            $nuevaSeccion->save();


            foreach ($seccionBase->parametros as $paramBase) {
                $nuevoParametro = new Parametro();
                $nuevoParametro->id_seccion = $nuevaSeccion->id;
                $nuevoParametro->nombre = $paramBase->nombre;
                $nuevoParametro->tipo = $paramBase->tipo;
                $nuevoParametro->unidad = $paramBase->unidad;
                $nuevoParametro->validacion = $paramBase->validacion;
                $nuevoParametro->requerido = $paramBase->requerido;
                $nuevoParametro->posicion = $paramBase->posicion;
                $nuevoParametro->id_grupo_selector = $paramBase->id_grupo_selector;
                $nuevoParametro->save();
            }
        }
        return redirect()->back()->with('success', 'Formulario agregado correctamente con toda su estructura.');
    }
}
