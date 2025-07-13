<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use Illuminate\Http\Request;

class PermisoController extends Controller
{
    public function index()
    {
        $permisos = Permiso::all();
        return view('permisos.index', compact('permisos'));
    }

    public function create()
    {
        return view('permisos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_permiso' => 'required|string|max:50',
            'descripcion' => 'nullable|string|max:150',
            'status' => 'required|boolean',
        ]);

        Permiso::create($request->all());

        return redirect()->route('permisos.index')->with('success', 'Permiso creado.');
    }

    public function show(Permiso $permiso)
    {
        return view('permisos.show', compact('permiso'));
    }

    public function edit(Permiso $permiso)
    {
        return view('permisos.edit', compact('permiso'));
    }

    public function update(Request $request, Permiso $permiso)
    {
        $request->validate([
            'nombre_permiso' => 'required|string|max:50',
            'descripcion' => 'nullable|string|max:150',
            'status' => 'required|boolean',
        ]);

        $permiso->update($request->all());

        return redirect()->route('permisos.index')->with('success', 'Permiso actualizado.');
    }

    public function destroy(Permiso $permiso)
    {
        $permiso->delete();

        return redirect()->route('permisos.index')->with('success', 'Permiso eliminado.');
    }
}
