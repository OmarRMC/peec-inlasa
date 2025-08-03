<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Municipio;
use App\Models\Permiso;
use App\Models\Provincia;
use Illuminate\Http\Request;

class MunicipioController extends Controller
{

    public function __construct()
    {
        $this->middleware('canany:' . Permiso::ADMIN . ',' . Permiso::GESTION_GEOGRAFICA)->only(['index', 'create', 'update', 'destroy', 'show', 'edit']);
    }
    public function index()
    {
        $municipios = Municipio::with('provincia')->latest()->get();
        return view('municipio.index', compact('municipios'));
    }

    public function create()
    {
        $provincias = Provincia::orderBy('nombre_prov')->get();
        return view('municipio.create', compact('provincias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_prov' => 'required|exists:provincia,id',
            'nombre_municipio' => 'required|string|min:3|max:70|unique:municipio,nombre_municipio',
            'cod_municipio' => 'required|numeric|unique:municipio,cod_municipio',
            'status_municipio' => 'required|boolean',
        ], $this->messages());

        Municipio::create($request->all());
        return redirect()->route('municipio.index')->with('success', 'Municipio creado correctamente.');
    }

    public function edit(Municipio $municipio)
    {
        $provincias = Provincia::orderBy('nombre_prov')->get();
        return view('municipio.edit', compact('municipio', 'provincias'));
    }

    public function update(Request $request, Municipio $municipio)
    {
        $request->validate([
            'id_prov' => 'required|exists:provincia,id',
            'nombre_municipio' => 'required|string|min:3|max:70|unique:municipio,nombre_municipio,' . $municipio->id,
            'cod_municipio' => 'required|numeric|unique:municipio,cod_municipio,' . $municipio->id,
            'status_municipio' => 'required|boolean',
        ], $this->messages());

        $municipio->update($request->all());
        return redirect()->route('municipio.index')->with('success', 'Municipio actualizado correctamente.');
    }

    public function destroy(Municipio $municipio)
    {
        $municipio->delete();
        return redirect()->route('municipio.index')->with('success', 'Municipio eliminado correctamente.');
    }

    private function messages()
    {
        return [
            'id_prov.required' => 'Debe seleccionar una provincia.',
            'id_prov.exists' => 'La provincia seleccionada no es válida.',
            'nombre_municipio.required' => 'El nombre del municipio es obligatorio.',
            'nombre_municipio.unique' => 'Ya existe un municipio con ese nombre.',
            'cod_municipio.required' => 'El código es obligatorio.',
            'cod_municipio.unique' => 'El código ya está registrado.',
            'status_municipio.required' => 'Debe seleccionar un estado.',
        ];
    }

    public function getDataAjax(Provincia $provincia)
    {
        $municipios = Municipio::where('id_prov', $provincia->id)->orderBy('nombre_municipio')->get();
        return response()->json($municipios);
    }
}
