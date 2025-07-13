<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departamento;
use App\Models\Pais;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DepartamentoController extends Controller
{
    public function index()
    {
        $departamentos = Departamento::with('pais')->latest()->get();
        return view('departamento.index', compact('departamentos'));
    }

    public function create()
    {
        $paises = Pais::where('status_pais', true)->get();
        return view('departamento.create', compact('paises'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_dep' => 'required|string|max:20|unique:departamento,nombre_dep',
            'sigla_dep' => 'required|string|max:5|unique:departamento,sigla_dep',
            'id_pais' => 'required|exists:pais,id',
            'status_dep' => 'required|boolean',
        ], $this->messages());

        Departamento::create($validated);

        return redirect()->route('departamento.index')->with('success', 'Departamento creado correctamente.');
    }

    public function edit(Departamento $departamento)
    {
        $paises = Pais::where('status_pais', true)->get();
        return view('departamento.edit', compact('departamento', 'paises'));
    }

    public function update(Request $request, Departamento $departamento)
    {
        $validated = $request->validate([
            'nombre_dep' => [
                'required',
                'string',
                'max:20',
                Rule::unique('departamento', 'nombre_dep')->ignore($departamento->id)
            ],
            'sigla_dep' => [
                'required',
                'string',
                'max:5',
                Rule::unique('departamento', 'sigla_dep')->ignore($departamento->id)
            ],
            'id_pais' => 'required|exists:pais,id',
            'status_dep' => 'required|boolean',
        ], $this->messages());

        $departamento->update($validated);

        return redirect()->route('departamento.index')->with('success', 'Departamento actualizado correctamente.');
    }

    public function destroy(Departamento $departamento)
    {
        $departamento->delete();
        return redirect()->route('departamento.index')->with('success', 'Departamento eliminado correctamente.');
    }

    /**
     * Mensajes personalizados de validación
     */
    protected function messages()
    {
        return [
            'nombre_dep.required' => 'El nombre del departamento es obligatorio.',
            'nombre_dep.max' => 'El nombre del departamento no debe superar los 20 caracteres.',
            'nombre_dep.unique' => 'Este nombre de departamento ya existe.',

            'sigla_dep.required' => 'La sigla del departamento es obligatoria.',
            'sigla_dep.max' => 'La sigla no debe superar los 5 caracteres.',
            'sigla_dep.unique' => 'Esta sigla ya está en uso.',

            'id_pais.required' => 'Debe seleccionar un país.',
            'id_pais.exists' => 'El país seleccionado no es válido.',

            'status_dep.required' => 'Debe indicar el estado del departamento.',
            'status_dep.boolean' => 'El estado debe ser válido (activo o inactivo).',
        ];
    }
}
