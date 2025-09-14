<?php

namespace App\Http\Controllers;

use App\Models\GrupoSelector;
use App\Models\OpcionSelector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GrupoSelectorController extends Controller
{
    public function buscar(Request $request)
    {
        $search = $request->get('q', '');
        $grupos = GrupoSelector::with('opciones')
            ->where('nombre', 'like', "%$search%")
            ->limit(20)
            ->get();

        return response()->json($grupos);
    }

    public function guardar(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:grupos_selectores,nombre',
            'opciones' => 'nullable|array',
            'opciones.*' => 'string|max:255'
        ]);
        Log::info('Guardando grupo selector', $request->all());

        $grupo = GrupoSelector::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion
        ]);

        if ($request->filled('opciones')) {
            foreach ($request->opciones as $i => $opcion) {
                OpcionSelector::create([
                    'id_grupo_selector' => $grupo->id,
                    'valor' => strtolower($opcion),
                    'etiqueta' => $opcion,
                    'posicion' => $i
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'grupo' => $grupo
        ]);
    }

    public function eliminar(Request $request, $id)
    {
        $grupo = GrupoSelector::find($id);
        if (!$grupo) {
            return response()->json(['success' => false, 'message' => 'Grupo no encontrado'], 404);
        }

        try {
            $grupo->opciones()->delete();
            $grupo->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error al eliminar grupo selector: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al eliminar el grupo'], 500);
        }
    }
}
