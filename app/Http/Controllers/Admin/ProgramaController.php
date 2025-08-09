<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permiso;
use App\Models\Programa;
use App\Traits\General;
use Illuminate\Http\Request;

class ProgramaController extends Controller
{
    use General;

    public function __construct()
    {
        $this->middleware('canany:' . Permiso::ADMIN . ',' . Permiso::GESTION_PROGRAMAS_AREAS_PAQUETES_EA)->only(['index', 'create', 'update', 'destroy', 'show', 'edit']);
    }
    private function messages()
    {
        return [
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.unique' => 'La descripción ya existe.',
            'descripcion.string' => 'La descripción debe ser un texto válido.',
            'descripcion.max' => 'La descripción no debe exceder 70 caracteres.',
            'status.required' => 'El estado es obligatorio.',
            'status.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }

    public function index()
    {
        $programas = Programa::orderBy('created_at', 'desc')->paginate(20);
        return view('programa.index', compact('programas'));
    }

    public function create()
    {
        return view('programa.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'descripcion' => 'required|string|max:70|unique:programa,descripcion',
            'status' => 'required|boolean',
        ], $this->messages());

        Programa::create($request->all());

        return redirect()->route('programa.index')->with('success', 'Programa creado correctamente.');
    }

    public function edit(Programa $programa)
    {
        return view('programa.edit', compact('programa'));
    }

    public function update(Request $request, Programa $programa)
    {
        $request->validate([
            'descripcion' => 'required|string|max:70',
            'status' => 'required|boolean',
        ], $this->messages());

        $programa->update($request->all());

        return redirect()->route('programa.index')->with('success', 'Programa actualizado correctamente.');
    }

    public function destroy(Programa $programa)
    {
        $programa->delete();
        return redirect()->route('programa.index')->with('success', 'Programa eliminado correctamente.');
    }
}
