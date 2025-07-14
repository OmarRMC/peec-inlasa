<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NivelLaboratorio;
use Illuminate\Http\Request;

class NivelLaboratorioController extends Controller
{
    public function index()
    {
        $niveles = NivelLaboratorio::orderBy('id', 'DESC')->get();
        return view('nivel_laboratorio.index', compact('niveles'));
    }

    public function create()
    {
        return view('nivel_laboratorio.create');
    }

    public function store(Request $request)
    {

        $validated = $request->validate($this->rules(), $this->messages());
        NivelLaboratorio::create($validated);

        return redirect()->route('nivel_laboratorio.index')->with('success', 'Nivel de laboratorio registrado correctamente.');
    }

    public function edit(NivelLaboratorio $nivelLaboratorio)
    {
        return view('nivel_laboratorio.edit', compact('nivelLaboratorio'));
    }

    public function update(Request $request, NivelLaboratorio $nivel_laboratorio)
    {
        $validated = $request->validate($this->rules($nivel_laboratorio->id), $this->messages());

        $nivel_laboratorio->update($validated);

        return redirect()->route('nivel_laboratorio.index')->with('success', 'Nivel de laboratorio actualizado correctamente.');
    }

    public function destroy(NivelLaboratorio $nivel_laboratorio)
    {
        $nivel_laboratorio->delete();

        return redirect()->route('nivel_laboratorio.index')->with('success', 'Nivel de laboratorio eliminado correctamente.');
    }

    // 🔐 Validaciones
    private function rules($id = null)
    {
        return [
            'descripcion_nivel' => 'required|string|min:3|max:100|regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ0-9\s]+$/|unique:nivel_laboratorio,descripcion_nivel' . ($id ? ',' . $id : ''),
            'status_nivel' => 'required|boolean'
        ];
    }

    // 📢 Mensajes personalizados
    private function messages()
    {
        return [
            'descripcion_nivel.required' => 'El nombre del nivel es obligatorio.',
            'descripcion_nivel.unique' => 'Este nombre de nivel ya está registrado.',
            'descripcion_nivel.regex' => 'El nombre solo puede contener letras y espacios.',
            'descripcion_nivel.min' => 'Debe tener al menos 3 caracteres.',
            'descripcion_nivel.max' => 'No debe exceder los 100 caracteres.',
            'status_nivel.required' => 'El estado es obligatorio.',
            'status_nivel.boolean' => 'El valor del estado debe ser válido.'
        ];
    }
}
