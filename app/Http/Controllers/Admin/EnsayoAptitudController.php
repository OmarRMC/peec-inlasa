<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EnsayoAptitud;
use App\Models\Paquete;
use Illuminate\Http\Request;

class EnsayoAptitudController extends Controller
{
    private function messages()
    {
        return [
            'id_paquete.required' => 'El paquete es obligatorio.',
            'id_paquete.exists' => 'El paquete seleccionado no existe.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.string' => 'La descripción debe ser un texto válido.',
            'descripcion.max' => 'La descripción no debe exceder 100 caracteres.',
            'status.required' => 'El estado es obligatorio.',
            'status.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }

    public function index()
    {
        $ensayos = EnsayoAptitud::with('paquete')->get();
        return view('ensayo_aptitud.index', compact('ensayos'));
    }

    public function create()
    {
        $paquetes = Paquete::active()->get();
        return view('ensayo_aptitud.create', compact('paquetes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_paquete' => 'required|exists:paquete,id',
            'descripcion' => 'required|string|max:100',
            'status' => 'required|boolean',
        ], $this->messages());

        EnsayoAptitud::create($request->all());

        return redirect()->route('ensayo_aptitud.index')->with('success', 'Ensayo de Aptitud creado correctamente.');
    }

    public function edit(EnsayoAptitud $ensayoAptitud)
    {
        $paquetes = Paquete::active()->get();
        $ensayo_aptitud = $ensayoAptitud;
        return view('ensayo_aptitud.edit', compact('ensayo_aptitud', 'paquetes'));
    }

    public function update(Request $request, EnsayoAptitud $ensayoAptitud)
    {
        $request->validate([
            'id_paquete' => 'required|exists:paquete,id',
            'descripcion' => 'required|string|max:100',
            'status' => 'required|boolean',
        ], $this->messages());

        $ensayoAptitud->update($request->all());

        return redirect()->route('ensayo_aptitud.index')->with('success', 'Ensayo de Aptitud actualizado correctamente.');
    }

    public function destroy(EnsayoAptitud $ensayoAptitud)
    {
        $ensayoAptitud->delete();
        return redirect()->route('ensayo_aptitud.index')->with('success', 'Ensayo de Aptitud eliminado correctamente.');
    }
}
