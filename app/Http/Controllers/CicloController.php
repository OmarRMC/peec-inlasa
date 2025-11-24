<?php

namespace App\Http\Controllers;

use App\Models\Ciclo;
use App\Models\EnsayoAptitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class CicloController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($idEa)
    {
        $ensayo = EnsayoAptitud::with('ciclos')->find($idEa);
        if (!$ensayo) {
            return redirect()->back()->with('error', 'Ensayo no encontrado.');
        }
        $ciclos = $ensayo->ciclos()->orderBy('numero')->get();
        return view('admin.ciclos.index', compact('ensayo', 'ciclos'));
    }

    public function toggle(Request $request, $id)
    {
        $ciclo = Ciclo::find($id);
        if (!$ciclo) {
            return redirect()->back()->with('error', 'Ciclo no encontrado.');
        }
        $ciclo->estado = !$ciclo->estado;
        $ciclo->update();

        return redirect()->back()->with('success', 'Estado del ciclo actualizado.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validateData($request);
        Ciclo::create($data);
        // return redirect()->route('admin.ciclos.index', ['idEa' => $request->id_ensayo])->with('success', 'Ciclo creado correctamente.');
        return redirect()->back()->with('success', 'Ciclo eliminado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ciclo $ciclo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ciclo $ciclo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        Log::info('Info sadatos');
        $ciclo = Ciclo::find($id);
        if (!$ciclo) {
            return redirect()->back()->with('error', 'Ciclo no encontrado.');
        }
        $data = $this->validateData($request, $ciclo->id);
        $ciclo->update($data);
        return redirect()->route('admin.ciclos.index', ['idEa' => $ciclo->id_ensayo])->with('success', 'Ciclo actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $ciclo = Ciclo::find($id);
        Log::info($ciclo);
        if (!$ciclo) {
            return redirect()->back()->with('error', 'Ciclo no encontrado.');
        }
        $ciclo->delete();
        return redirect()->back()->with('success', 'Ciclo eliminado correctamente.');
    }

    private function validateData(Request $request, $id = null)
    {
        return $request->validate([
            'id_ensayo' => ['required', 'exists:ensayo_aptitud,id'],
            'nombre'            => ['required', 'string', 'max:100'],
            'numero'            => [
                'required',
                'integer',
                'min:1',
                Rule::unique('ciclos', 'numero')
                    ->where('id_ensayo', $request->id_ensayo)
                    ->ignore($id)
            ],
            'fecha_inicio_envio_muestras'   => ['required', 'date'],
            'fecha_fin_envio_muestras'      => ['required', 'date', 'after_or_equal:fecha_inicio_envio_muestras'],
            'fecha_inicio_envio_resultados' => ['required', 'date', 'after_or_equal:fecha_fin_envio_muestras'],
            'fecha_fin_envio_resultados'    => ['required', 'date', 'after_or_equal:fecha_inicio_envio_resultados'],
            'fecha_inicio_envio_reporte'    => ['required', 'date', 'after_or_equal:fecha_fin_envio_resultados'],
            'fecha_fin_envio_reporte'       => ['required', 'date', 'after_or_equal:fecha_inicio_envio_reporte'],
        ], [
            'id_ensayo.required' => 'El ensayo es obligatorio.',
            'id_ensayo.exists'   => 'El ensayo seleccionado no existe.',
            'nombre.required'            => 'El nombre del ciclo es obligatorio.',
            'numero.required'            => 'El número es obligatorio.',
            'numero.unique'              => 'Ese número ya está registrado para este ensayo.',
            'fecha_inicio_envio_muestras.required' => 'Debe indicar la fecha de inicio de envío de muestras.',
            'fecha_fin_envio_muestras.after_or_equal' => 'La fecha fin de envío de muestras no puede ser antes del inicio.',
            'fecha_inicio_envio_resultados.after_or_equal' => 'El inicio de resultados debe ser después del fin de muestras.',
            'fecha_fin_envio_resultados.after_or_equal' => 'El fin de resultados debe ser después del inicio.',
            'fecha_inicio_envio_reporte.after_or_equal' => 'El inicio de reporte debe ser después del fin de resultados.',
            'fecha_fin_envio_reporte.after_or_equal' => 'El fin de reporte debe ser después del inicio.',
        ]);
    }
}
