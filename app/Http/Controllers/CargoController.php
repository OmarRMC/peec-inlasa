<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CargoController extends Controller
{
    public function index()
    {
        $cargos = Cargo::all();
        return view('cargos.index', compact('cargos'));
    }

    public function create()
    {
        return view('cargos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_cargo' => 'required|string|max:150|unique:cargo,nombre_cargo',
            'descripcion' => 'nullable|string|max:100',
            'obs' => 'nullable|string|max:200',
            'status' => 'required|boolean',
        ], [
            'nombre_cargo.required' => 'El nombre del cargo es obligatorio.',
            'nombre_cargo.unique' => 'El nombre del cargo ya existe.',
            'descripcion.max' => 'La descripción no puede exceder los 100 caracteres.',
            'obs.max' => 'La observación no puede exceder los 200 caracteres.',
            'status.required' => 'El estado es obligatorio.',
        ]);

        Cargo::create($request->all());

        return redirect()->route('cargos.index')->with('success', 'Cargo creado correctamente.');
    }

    public function show(Cargo $cargo)
    {
        return view('cargos.show', compact('cargo'));
    }

    public function edit(Cargo $cargo)
    {
        return view('cargos.edit', compact('cargo'));
    }

    public function update(Request $request, Cargo $cargo)
    {
        $request->validate([
            'nombre_cargo' => 'required|string|max:150',
            'descripcion' => 'nullable|string|max:100',
            'obs' => 'nullable|string|max:200',
            'status' => 'required|boolean',
        ]);

        $cargo->update($request->all());

        return redirect()->route('cargos.index')->with('success', 'Cargo actualizado.');
    }

    public function destroy(Cargo $cargo)
    {
        $cargo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cargo eliminado correctamente.'
        ]);
        // return redirect()->route('cargos.index')->with('success', 'Cargo eliminado.');
    }


    public function getData()
    {
        $query = Cargo::select(['id', 'nombre_cargo', 'descripcion', 'status']);

        return DataTables::of($query)
            ->addColumn('status_label', function ($cargo) {
                return $cargo->status
                    ? '<span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-green-500 text-white">Activo</span>'
                    : '<span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-red-500 text-white">Inactivo</span>';
            })
            ->addColumn('actions', function ($cargo) {
                return view('cargos.action-buttons', [
                    'editUrl' => route('cargos.edit', $cargo->id),
                    'deleteUrl' => route('cargos.destroy', $cargo->id),
                    'nombre' => $cargo->nombre_cargo,
                    'id' => $cargo->id,
                ])->render();
            })
            ->rawColumns(['status_label', 'actions'])
            ->make(true);
    }
}
