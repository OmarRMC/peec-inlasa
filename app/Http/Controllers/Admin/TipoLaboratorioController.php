<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TipoLaboratorio;
use Illuminate\Http\Request;

class TipoLaboratorioController extends Controller
{
    public function index()
    {
        $tipos = TipoLaboratorio::orderBy('descripcion')->get();
        return view('tipo_laboratorio.index', compact('tipos'));
    }

    public function create()
    {
        return view('tipo_laboratorio.create');
    }

    public function store(Request $request)
    {
        $this->validateRequest($request);

        TipoLaboratorio::create($request->all());

        return redirect()->route('tipo_laboratorio.index')->with('success', 'Tipo de laboratorio registrado correctamente.');
    }

    public function edit(TipoLaboratorio $tipo_laboratorio)
    {
        return view('tipo_laboratorio.edit', compact('tipo_laboratorio'));
    }

    public function update(Request $request, TipoLaboratorio $tipo_laboratorio)
    {
        $this->validateRequest($request, $tipo_laboratorio->id);

        $tipo_laboratorio->update($request->all());

        return redirect()->route('tipo_laboratorio.index')->with('success', 'Tipo de laboratorio actualizado correctamente.');
    }

    public function destroy(TipoLaboratorio $tipo_laboratorio)
    {
        $tipo_laboratorio->delete();

        return redirect()->route('tipo_laboratorio.index')->with('success', 'Tipo de laboratorio eliminado correctamente.');
    }

    private function validateRequest(Request $request, $id = null)
    {
        $request->validate([
            'descripcion' => 'required|string|max:25|unique:tipo_laboratorio,descripcion,' . $id,
            'status' => 'required|boolean',
        ], [
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.max' => 'La descripción no debe exceder los 25 caracteres.',
            'descripcion.unique' => 'Este tipo de laboratorio ya existe.',
            'status.required' => 'Debe seleccionar el estado.',
            'status.boolean' => 'El estado debe ser válido.',
        ]);
    }
}
