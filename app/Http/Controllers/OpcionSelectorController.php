<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\GrupoSelector;
use App\Models\OpcionSelector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OpcionSelectorController extends Controller
{
    public function store(Request $request, $grupoId)
    {
        $request->validate([
            'valor' => 'required|string|max:255',
            'etiqueta' => 'string|max:255',
            'id_grupo_hijo' => 'nullable|exists:grupos_selectores,id',
        ]);


        GrupoSelector::findOrFail($grupoId)
            ->opciones()
            ->create($request->only('valor', 'etiqueta', 'id_grupo_hijo'));

        return back()->with('success', 'Opción agregada correctamente.');
    }

    public function update(Request $request, OpcionSelector $opcion)
    {
        $request->validate([
            'valor' => 'required|string|max:255',
            'etiqueta' => 'string|max:255',
            'id_grupo_hijo' => 'nullable|exists:grupos_selectores,id',
        ]);
        Log::info('Actualizando opción con datos:', $request->only('valor', 'etiqueta', 'id_grupo_hijo'));

        $opcion->update($request->only('valor', 'etiqueta', 'id_grupo_hijo'));

        return back()->with('success', 'Opción actualizada correctamente.');
    }

    public function destroy(OpcionSelector $opcion)
    {
        $opcion->delete();
        return back()->with('success', 'Opción eliminada correctamente.');
    }
}
