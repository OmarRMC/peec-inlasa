<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paquete;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function porPrograma(Request $request)
    {
        $programaId = $request->query('programa_id');

        if (!$programaId) {
            return response()->json([]);
        }

        // Obtener el laboratorio actual (puedes ajustar esta lógica si es por auth o por otro medio)
        $laboratorio = Auth::user()->laboratorio;

        if (!$laboratorio || !$laboratorio->id_tipo) {
            return response()->json([]);
        }

        // Verificar si el programa está asociado al tipo de laboratorio
        $tipoCompatible = DB::table('tipo_laboratorio_programa')
            ->where('id_programa', $programaId)
            ->where('id_tipo', $laboratorio->id_tipo)
            ->exists();

        if (!$tipoCompatible) {
            return response()->json([]);
        }

        // Obtener áreas del programa → paquetes del área
        $paquetes = Paquete::whereHas('area.programa', function ($query) use ($programaId) {
            $query->where('programa.id', $programaId);
        })
            ->where('status', true)
            ->select('id', 'descripcion', 'costo_paquete')
            ->get();

        return response()->json($paquetes);
    }
}
