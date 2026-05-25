<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\GrupoSelector;
use App\Models\OpcionSelector;
use Illuminate\Http\Request;

class OpcionSelectorController extends Controller
{
    public function store(Request $request, $grupoId)
    {
        $request->validate([
            'valor' => 'required|string|max:255',
            'id_subgrupo' => 'nullable|exists:grupos_selectores,id',
        ]);

        GrupoSelector::findOrFail($grupoId)
            ->opciones()
            ->create($request->only('valor', 'id_subgrupo'));

        return back()->with('success', 'Opción agregada correctamente.');
    }

    public function update(Request $request, OpcionSelector $opcion)
    {
        $request->validate([
            'valor' => 'required|string|max:255',
            'id_subgrupo' => 'nullable|exists:grupos_selectores,id',
        ]);

        $opcion->update($request->only('valor', 'id_subgrupo'));

        return back()->with('success', 'Opción actualizada correctamente.');
    }

    public function destroy(OpcionSelector $opcion)
    {
        $opcion->delete();
        return back()->with('success', 'Opción eliminada correctamente.');
    }
}
