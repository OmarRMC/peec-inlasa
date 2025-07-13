<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paquete;
use App\Models\Area;
use Illuminate\Http\Request;

class PaqueteController extends Controller
{
    private function messages()
    {
        return [
            'id_area.required' => 'El área es obligatoria.',
            'id_area.exists' => 'El área seleccionada no existe.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.string' => 'La descripción debe ser un texto válido.',
            'descripcion.max' => 'La descripción no debe exceder 100 caracteres.',
            'costo_paquete.required' => 'El costo del paquete es obligatorio.',
            'costo_paquete.integer' => 'El costo debe ser un número entero.',
            'status.required' => 'El estado es obligatorio.',
            'status.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }

    public function index()
    {
        $paquetes = Paquete::with('area')->paginate(10);
        return view('paquete.index', compact('paquetes'));
    }

    public function create()
    {
        $areas = Area::active()->get();
        return view('paquete.create', compact('areas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_area' => 'required|exists:area,id',
            'descripcion' => 'required|string|max:100|unique:paquete,descripcion',
            'costo_paquete' => 'required|integer',
            'status' => 'required|boolean',
        ], $this->messages());

        Paquete::create($request->all());

        return redirect()->route('paquete.index')->with('success', 'Paquete creado correctamente.');
    }

    public function edit(Paquete $paquete)
    {
        $areas = Area::active()->get();
        return view('paquete.edit', compact('paquete', 'areas'));
    }

    public function update(Request $request, Paquete $paquete)
    {
        $request->validate([
            'id_area' => 'required|exists:area,id',
            'descripcion' => 'required|string|max:100|unique:paquete,descripcion,' . $paquete->id,
            'costo_paquete' => 'required|integer',
            'status' => 'required|boolean',
        ], $this->messages());

        $paquete->update($request->all());

        return redirect()->route('paquete.index')->with('success', 'Paquete actualizado correctamente.');
    }

    public function destroy(Paquete $paquete)
    {
        $paquete->delete();
        return redirect()->route('paquete.index')->with('success', 'Paquete eliminado correctamente.');
    }
}
