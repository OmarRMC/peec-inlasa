<?php

namespace App\Http\Controllers;

use App\Models\EnsayoAptitud;
use App\Models\Formulario;
use App\Models\FormularioEnsayo;
use App\Models\Parametro;
use App\Models\Programa;
use App\Models\Seccion;
use Illuminate\Http\Request;
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

    public function edit($id)
    {
        $formulario = FormularioEnsayo::with([
            'secciones.parametros.grupoSelector.opciones'
        ])->find($id);
        if (!$formulario) {
            return redirect()->route('admin.formularios.ea')->with('error', 'Formulario no encontrado.');
        }
        $grupos = [];
        return view('admin.formularios.edit', compact('formulario', 'grupos'));
    }

    public function updateEstructura(Request $request, $id)
    {
        $formulario = FormularioEnsayo::findOrFail($id);

        $data = $request->validate([
            'secciones' => 'required|array',
            'secciones.*.nombre' => 'required|string|max:255',
            'secciones.*.descripcion' => 'nullable|string',
            'secciones.*.cantidad_parametros' => 'nullable|integer',
            'secciones.*.parametros' => 'array',
            'secciones.*.parametros.*.nombre' => 'required|string|max:255',
            'secciones.*.parametros.*.tipo' => 'required|string|in:text,number,date,select,checkbox,textarea',
            'secciones.*.parametros.*.unidad' => 'nullable|string|max:50',
            'secciones.*.parametros.*.validacion' => 'nullable|string',
            'secciones.*.parametros.*.requerido' => 'nullable|boolean',
            'secciones.*.parametros.*.posicion' => 'nullable|integer',
            'secciones.*.parametros.*.grupo_selector_id' => 'nullable|exists:grupos_selectores,id',
        ]);

        Log::info('Guardando estructura del formulario', $data);

        $formulario->secciones()->delete();

        foreach ($data['secciones'] as $sec) {
            $seccion = $formulario->secciones()->create([
                'nombre' => $sec['nombre'],
                'descripcion' => $sec['descripcion'] ?? null,
                'cantidad_parametros' => max(
                    (int) ($sec['cantidad_parametros'] ?? 0),
                    count($sec['parametros'] ?? [])
                ),
            ]);

            foreach ($sec['parametros'] ?? [] as $param) {
                $seccion->parametros()->create([
                    'nombre' => $param['nombre'],
                    'tipo' => $param['tipo'],
                    'unidad' => $param['unidad'] ?? null,
                    'validacion' => $param['validacion'] ?? null,
                    'requerido' => isset($param['requerido']) ? 1 : 0,
                    'posicion' => $param['posicion'] ?? 0,
                    'id_grupo_selector' => $param['grupo_selector_id'] ?? null,
                ]);
            }
        }

        return redirect()->route('admin.formularios.edit', $id)
            ->with('success', 'Formulario actualizado correctamente ✅');
    }

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
