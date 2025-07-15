<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departamento;
use App\Models\Provincia;
use Illuminate\Http\Request;

class ProvinciaController extends Controller
{
    public function index()
    {
        $provincias = Provincia::with('departamento')->latest()->get();
        return view('provincia.index', compact('provincias'));
    }

    public function create()
    {
        $departamentos = Departamento::orderBy('nombre_dep')->get();
        return view('provincia.create', compact('departamentos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_dep' => 'required|exists:departamento,id',
            'nombre_prov' => 'required|string|min:3|max:50|unique:provincia,nombre_prov',
            'cod_prov' => 'required|numeric|unique:provincia,cod_prov',
            'status_prov' => 'required|boolean',
        ], $this->messages());

        Provincia::create($request->all());
        return redirect()->route('provincia.index')->with('success', 'Provincia creada correctamente.');
    }

    public function edit(Provincia $provincia)
    {
        $departamentos = Departamento::orderBy('nombre_dep')->get();
        return view('provincia.edit', compact('provincia', 'departamentos'));
    }

    public function update(Request $request, Provincia $provincia)
    {
        $request->validate([
            'id_dep' => 'required|exists:departamento,id',
            'nombre_prov' => 'required|string|min:3|max:50|unique:provincia,nombre_prov,' . $provincia->id,
            'cod_prov' => 'required|numeric|unique:provincia,cod_prov,' . $provincia->id,
            'status_prov' => 'required|boolean',
        ], $this->messages());

        $provincia->update($request->all());
        return redirect()->route('provincia.index')->with('success', 'Provincia actualizada correctamente.');
    }

    public function destroy(Provincia $provincia)
    {
        $provincia->delete();
        return redirect()->route('provincia.index')->with('success', 'Provincia eliminada correctamente.');
    }

    private function messages()
    {
        return [
            'id_dep.required' => 'Debe seleccionar un departamento.',
            'id_dep.exists' => 'El departamento seleccionado no es válido.',
            'nombre_prov.required' => 'El nombre de la provincia es obligatorio.',
            'nombre_prov.unique' => 'Ya existe una provincia con ese nombre.',
            'cod_prov.required' => 'El código es obligatorio.',
            'cod_prov.unique' => 'El código ya está registrado.',
            'status_prov.required' => 'Debe seleccionar un estado.',
        ];
    }

    public function getDataAjax(Departamento $departamento)
    {
        $provincias = Provincia::where('id_dep', $departamento->id)->get();
        return response()->json($provincias);
    }
}
