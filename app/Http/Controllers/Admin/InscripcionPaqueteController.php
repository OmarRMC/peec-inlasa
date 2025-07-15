<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laboratorio;
use App\Models\Paquete;
use App\Models\Programa;
use App\Models\TipoLaboratorioPrograma;
use App\Models\InscripcionPaquete;
use App\Models\DetalleInscripcion;
use App\Models\Inscripcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InscripcionPaqueteController extends Controller
{
    public function create($labId)
    {
        $laboratorio = Laboratorio::findOrFail($labId);
        $programas = $laboratorio->tipo->programas()->get();

        // $programas = Programa::whereHas('tipos', fn($q) => $q->where('id_tipo', $tipoLab))
        //     ->where('status', true)
        //     ->get();

        return view('inscripcion_paquete.create', compact('laboratorio', 'programas'));
    }

    public function paquetesPorPrograma(Request $request)
    {
        $paquetes = Paquete::where('id_area', $request->programa_id)
            ->where('status', true)
            ->get(['id', 'descripcion', 'costo_paquete']);
        return response()->json($paquetes);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_lab' => 'required|exists:laboratorio,id',
            'id_formulario' => 'nullable|exists:formulario,id',
            'paquetes' => 'required|array|min:1',
            'paquetes.*.id' => 'required|exists:paquete,id',
            'paquetes.*.costo' => 'required|integer|min:0',
            'obs_inscripcion' => 'nullable|string|max:255',
            'gestion' => 'required|string|max:10',
        ]);

        DB::transaction(function () use ($request) {
            $total = collect($request->paquetes)->sum('costo');
            $ins = Inscripcion::create([
                'id_lab' => $request->id_lab,
                'id_formulario' => $request->id_formulario,
                'cant_paq' => count($request->paquetes),
                'costo_total' => $total,
                'obs_inscripcion' => $request->obs_inscripcion,
                'fecha_inscripcion' => now(),
                'status_cuenta' => false,
                'status_inscripcion' => true,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'gestion' => $request->gestion,
            ]);

            foreach ($request->paquetes as $p) {
                DetalleInscripcion::create([
                    'id_inscripcion' => $ins->id,
                    'id_paquete' => $p['id'],
                    'descripcion_paquete' => Paquete::find($p['id'])->descripcion,
                    'costo_paquete' => $p['costo'],
                ]);
            }
        });

        return redirect()->route('laboratorio.show', $request->id_lab)
            ->with('success', 'Inscripción creada exitosamente.');
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'id_lab' => 'required|exists:laboratorio,id',
    //         'gestion' => 'required|string|max:10',
    //         'paquetes' => 'required|array|min:1',
    //         'paquetes.*.id_paquete' => 'required|exists:paquete,id',
    //         'paquetes.*.descripcion_paquete' => 'required|string|max:100',
    //         'paquetes.*.costo_paquete' => 'required|numeric|min:0',
    //     ]);

    //     DB::beginTransaction();

    //     try {
    //         // Crear inscripción
    //         $inscripcion = Inscripcion::create([
    //             'id_lab' => $request->id_lab,
    //             'id_formulario' => null,
    //             'cant_paq' => count($request->paquetes),
    //             'costo_total' => collect($request->paquetes)->sum('costo_paquete'),
    //             'obs_inscripcion' => null,
    //             'fecha_inscripcion' => now(),
    //             'status_cuenta' => false,
    //             'status_inscripcion' => true,
    //             'created_by' => auth()->id(),
    //             'updated_by' => auth()->id(),
    //             'gestion' => $request->gestion,
    //         ]);

    //         // Crear detalle por paquete
    //         foreach ($request->paquetes as $item) {
    //             DetalleInscripcion::create([
    //                 'id_inscripcion' => $inscripcion->id,
    //                 'id_paquete' => $item['id_paquete'],
    //                 'descripcion_paquete' => $item['descripcion_paquete'],
    //                 'costo_paquete' => $item['costo_paquete'],
    //             ]);
    //         }

    //         DB::commit();

    //         return response()->json(['success' => true]);
    //     } catch (\Throwable $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Error al registrar inscripción',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }
}
