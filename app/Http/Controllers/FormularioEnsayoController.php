<?php

namespace App\Http\Controllers;

use App\Models\EnsayoAptitud;
use App\Models\FormularioEnsayo;
use App\Models\Programa;
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
        $formularios = $ensayo->formularios;
        return view('admin.formularios.show', compact('ensayo', 'formularios'));
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'id_ensayo' => ['required', 'exists:ensayo_aptitud,id'],
                'nombre'            => ['required', 'string', 'max:255'],
                'codigo'            => ['nullable', 'string', 'max:80', 'unique:formularios,codigo'],
                'nota'              => ['nullable', 'string'],
            ],
            [
                'id_ensayo.required' => 'El campo ensayo de aptitud es obligatorio.',
                'id_ensayo.exists'   => 'El ensayo de aptitud seleccionado no existe.',
                'nombre.required'            => 'El nombre del formulario es obligatorio.',
                'nombre.max'                 => 'El nombre no puede superar los 255 caracteres.',
                'codigo.max'                 => 'El código no puede superar los 80 caracteres.',
                'codigo.unique'              => 'El código ya está en uso. Debe ser único.',
            ]
        );
        $ensayo = EnsayoAptitud::find($request->id_ensayo);
        if (!$ensayo) {
            return redirect()->back()->with('error', 'Ensayo de Aptitud no encontrado.');
        }

        $formulario = new FormularioEnsayo();
        $formulario->id_ensayo = $request->id_ensayo;
        $formulario->nombre = $request->nombre;
        $formulario->codigo = $request->codigo;
        $formulario->nota = $request->nota;
        $formulario->save();

        return redirect()
            ->route('admin.formularios.show', ['idEA' => $ensayo->id])
            ->with('success', 'Formulario creado exitosamente.');
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
}
