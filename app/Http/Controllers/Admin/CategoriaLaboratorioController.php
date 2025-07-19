<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoriaLaboratorio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoriaLaboratorioController extends Controller
{

    public function index()
    {
        $categorias = CategoriaLaboratorio::latest()->paginate(10);
        return view('categoria_laboratorio.index', compact('categorias'));
    }

    public function create()
    {
        return view('categoria_laboratorio.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'descripcion' => 'required|string|min:3|max:25|unique:categoria,descripcion',
            'status' => 'required|boolean',
        ], $this->mensajes());

        CategoriaLaboratorio::create($request->all());

        return redirect()->route('categoria_laboratorio.index')->with('success', 'Categoría registrado exitosamente.');
    }

    public function edit(CategoriaLaboratorio $categoria_laboratorio)
    {
        return view('categoria_laboratorio.edit', compact('categoria_laboratorio'));
    }

    public function update(Request $request, CategoriaLaboratorio $categoria_laboratorio)
    {
        Log::info($categoria_laboratorio->id);
        $request->validate([
            'descripcion' => 'required|string|min:3|max:25|unique:categoria,descripcion,' . $categoria_laboratorio->id,
            'status' => 'required|boolean',
        ], $this->mensajes());

        $categoria_laboratorio->update($request->all());

        return redirect()->route('categoria_laboratorio.index')->with('success', 'Categoría actualizada exitosamente.');
    }

    public function destroy(CategoriaLaboratorio $categoria_laboratorio)
    {
        $categoria_laboratorio->delete();
        return redirect()->route('categoria_laboratorio.index')->with('success', 'Categoría eliminada.');
    }

    protected function mensajes()
    {
        return [
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.unique' => 'La descripción ya existe.',
            'descripcion.min' => 'La descripción debe tener al menos 3 caracteres.',
            'descripcion.max' => 'La descripción no puede tener más de 25 caracteres.',
            'status.required' => 'El estado es obligatorio.',
            'status.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }
}
