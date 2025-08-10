<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Permiso;
use App\Models\Programa;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AreaController extends Controller
{
    public function __construct()
    {
        $this->middleware('canany:' . Permiso::ADMIN . ',' . Permiso::GESTION_PROGRAMAS_AREAS_PAQUETES_EA)->only(['index', 'create', 'update', 'destroy', 'show', 'edit']);
    }
    private function messages()
    {
        return [
            'id_programa.required' => 'El programa es obligatorio.',
            'id_programa.exists' => 'El programa seleccionado no existe.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.unique' => 'La descripción ya existe.',
            'descripcion.string' => 'La descripción debe ser un texto válido.',
            'descripcion.max' => 'La descripción no debe exceder 50 caracteres.',
            'status.required' => 'El estado es obligatorio.',
            'status.boolean' => 'El estado debe ser verdadero o falso.',
            'max_paquetes_inscribir.required' => 'La cantidad máxima de paquetes a inscribir es obligatoria.',
            'max_paquetes_inscribir.integer' => 'La cantidad máxima debe ser un número entero.',
        ];
    }

    public function index()
    {
        $areas = Area::with('programa')->orderBy('created_at', 'desc')->paginate(20);

        return view('area.index', compact('areas'));
    }

    public function create()
    {
        $programas = Programa::active()->get();
        return view('area.create', compact('programas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_programa' => 'required|exists:programa,id',
            'descripcion' => [
                'required',
                'string',
                'max:50',
                Rule::unique('area', 'descripcion')
                    ->where(fn($query) => $query->where('id_programa', $request->id_programa)),
            ],
            'status' => 'required|boolean',
            'max_paquetes_inscribir' => 'required|integer',
        ], $this->messages());

        Area::create($request->all());

        return redirect()->route('area.index')->with('success', 'Área registrado correctamente.');
    }

    public function edit(Area $area)
    {
        $programas = Programa::active()->get();
        return view('area.edit', compact('area', 'programas'));
    }

    public function update(Request $request, Area $area)
    {
        $request->validate([
            'id_programa' => 'required|exists:programa,id',
            'descripcion' => [
                'required',
                'string',
                'max:50',
                Rule::unique('area', 'descripcion')
                    ->ignore($area->id)
                    ->where(fn($query) => $query->where('id_programa', $request->id_programa)),
            ],
            'status' => 'required|boolean',
            'max_paquetes_inscribir' => 'required|integer',
        ], $this->messages());
        $area->update($request->all());

        return redirect()->route('area.index')->with('success', 'Área actualizada correctamente.');
    }

    public function destroy(Area $area)
    {
        $area->delete();
        return redirect()->route('area.index')->with('success', 'Área eliminada correctamente.');
    }
}
