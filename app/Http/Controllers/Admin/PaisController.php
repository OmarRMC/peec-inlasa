<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pais;
use App\Models\Permiso;
use Illuminate\Http\Request;

class PaisController extends Controller
{
    public function __construct()
    {
        $this->middleware('canany:' . Permiso::ADMIN . ',' . Permiso::GESTION_GEOGRAFICA)->only(['index', 'create', 'update', 'destroy', 'show', 'edit']);
    }
    public function index()
    {
        $paises = Pais::all();
        return view('pais.index', compact('paises'));
    }

    public function create()
    {
        return view('pais.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_pais' => 'required|string|max:15|unique:pais,nombre_pais',
            'sigla_pais' => 'required|string|max:5|unique:pais,sigla_pais',
            'cod_pais' => 'required|integer|unique:pais,cod_pais',
            'status_pais' => 'required|boolean',
        ]);

        Pais::create($request->all());
        return redirect()->route('pais.index')->with('success', 'País creado correctamente.');
    }

    public function edit(Pais $pais)
    {
        return view('pais.edit', compact('pais'));
    }

    public function update(Request $request, Pais $pais)
    {
        $request->validate([
            'nombre_pais' => 'required|string|max:15|unique:pais,nombre_pais,' . $pais->id,
            'sigla_pais' => 'required|string|max:5|unique:pais,sigla_pais,' . $pais->id,
            'cod_pais' => 'required|integer|unique:pais,cod_pais,' . $pais->id,
            'status_pais' => 'required|boolean',
        ]);

        $pais->update($request->all());
        return redirect()->route('pais.index')->with('success', 'País actualizado correctamente.');
    }

    public function destroy(Pais $pais)
    {
        $pais->delete();
        return redirect()->route('pais.index')->with('success', 'País eliminado correctamente.');
    }
}
