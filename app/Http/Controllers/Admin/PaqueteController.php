<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paquete;
use App\Models\Area;
use App\Models\Configuracion;
use App\Models\Laboratorio;
use App\Models\Permiso;
use App\Models\Programa;
use App\Models\TipoLaboratorio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class PaqueteController extends Controller
{
    public function __construct()
    {
        $this->middleware('canany:' . Permiso::ADMIN . ',' . Permiso::GESTION_PROGRAMAS_AREAS_PAQUETES_EA)->only(['index', 'create', 'update', 'destroy', 'show', 'edit']);
    }
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
        $paquetes = Paquete::with(['area.programa', 'tiposLaboratorios'])
            ->orderBy(
                Area::select('id_programa')
                    ->whereColumn('area.id', 'paquete.id_area')
            )
            ->orderBy(
                'id_area'
            )
            ->orderBy('descripcion')
            ->paginate(20);
        return view('paquete.index', compact('paquetes'));
    }

    public function create()
    {
        $areas = Area::active()->get();
        $tiposLaboratorios = TipoLaboratorio::active()->get();
        return view('paquete.create', compact('areas', 'tiposLaboratorios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_area' => 'required|exists:area,id',
            'descripcion' => [
                'required',
                'string',
                'max:100',
                Rule::unique('paquete', 'descripcion')
                    ->where(fn($query) => $query->where('id_area', $request->id_area)),
            ],
            'costo_paquete' => [
                'required',
                'numeric',
                'min:0',
                'max:15000',
                'regex:/^\d+(\.\d{1,2})?$/'
            ],
            'descuento' => 'nullable|numeric|min:0|max:100',
            'max_participantes' => 'required|integer|min:0',
            'tipo_laboratorio_ids' => 'array',
            'tipo_laboratorio_ids.*' => 'exists:tipo_laboratorio,id',
            'status' => 'required|boolean',
        ], $this->messages());

        $paquete = Paquete::create($request->all());
        $paquete->tiposLaboratorios()->sync($request['tipo_laboratorio_ids'] ?? []);
        return redirect()->route('paquete.index')->with('success', 'Paquete creado correctamente.');
    }

    public function edit(Paquete $paquete)
    {
        $areas = Area::active()->get();
        $tiposLaboratorios = TipoLaboratorio::active()->get();
        $tiposLaboratoriosSelecionados  = $paquete->tiposLaboratorios()->get();
        return view('paquete.edit', compact('paquete', 'areas', 'tiposLaboratorios', 'tiposLaboratoriosSelecionados'));
    }

    public function update(Request $request, Paquete $paquete)
    {
        $request->validate([
            'id_area' => 'required|exists:area,id',
            'descripcion' => [
                'required',
                'string',
                'max:100',
                Rule::unique('paquete', 'descripcion')
                    ->ignore($paquete->id)
                    ->where(fn($query) => $query->where('id_area', $request->id_area)),
            ],
            'costo_paquete' => [
                'required',
                'numeric',
                'min:0',
                'max:15000',
                'regex:/^\d+(\.\d{1,2})?$/'
            ],
            'descuento' => 'nullable|numeric|min:0|max:100',
            'max_participantes' => 'required|integer|min:0',
            'tipo_laboratorio_ids' => 'array',
            'tipo_laboratorio_ids.*' => 'exists:tipo_laboratorio,id',
            'status' => 'required|boolean',
        ], $this->messages());

        $paquete->update($request->all());
        $paquete->tiposLaboratorios()->sync($request['tipo_laboratorio_ids'] ?? []);

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

        $programa = Programa::find($programaId);
        if (!$programa) {
            return response()->json([]);
        }

        $programa->load(['areas.paquetes']);
        $data = [];

        foreach ($programa->areas as $area) {
            foreach ($area->paquetes as $paquete) {
                $data[] = [
                    'paquete_id' => $paquete->id,
                    'costo' => $paquete->costo_paquete,
                    'costo_final' => $paquete->costo_con_descuento,
                    'nombre_paquete' => $paquete->descripcion,
                    'area_id' => $area->id,
                    'nombre_area' => $area->descripcion
                ];
            }
        }

        return response()->json($data);
    }

    public function getPaquetesPorPrograma(Request $request)
    {
        $programaId = $request->query('programa_id');
        $labId = $request->lab_id;
        $user = Auth::user();
        if ($user->isLaboratorio()) {
            $laboratorio = $user->laboratorio;
            if (!Configuracion::esPeriodoInscripcion()) {
                return datatables()->of(collect())->toJson();
            }
        } else {
            $laboratorio = Laboratorio::findOrFail($labId);
        }
        $tipoLabId = $laboratorio->id_tipo;
        $aplicaDescuento = $laboratorio->tiene_descuento;

        if (!$programaId) {
            return datatables()->of(collect())->toJson();
        }

        $gestion = configuracion(Configuracion::GESTION_INSCRIPCION);
        $idsPaquetes = $laboratorio->paquetesPendientes($gestion);
        $paquetes = collect();

        $paquetesDB = Paquete::with(['area', 'detalleInscripciones.inscripcion'])->active()
            ->whereNotIn('id', $idsPaquetes)
            ->whereHas('area', function ($query) use ($request) {
                $query->where('id_programa', $request->programa_id);
            })
            ->whereHas('tiposLaboratorios', function ($query) use ($tipoLabId) {
                $query->where('tipo_laboratorio_id', $tipoLabId);
            })
            ->where('max_participantes', '>', 0)
            ->get()
            ->filter(function ($paquete) {
                $gestionActual = configuracion(Configuracion::GESTION_INSCRIPCION);
                $inscritosActuales = $paquete->detalleInscripciones->filter(function ($detalle) use ($gestionActual) {
                    return optional($detalle->inscripcion)->gestion == $gestionActual;
                })->count();
                return $inscritosActuales < $paquete->max_participantes;
            });

        // detalles de incripciones

        if (!$paquetesDB) {
            return datatables()->of(collect())->toJson();
        }

        foreach ($paquetesDB as $paquete) {
            $paquetes->push([
                'id' => $paquete->id,
                'nombre_paquete' =>  $paquete->descuento > 0 && $aplicaDescuento ? "{$paquete->descripcion} {$paquete->descuento_html}" : $paquete->descripcion,
                'costo' => $aplicaDescuento? $paquete->precio_final : $paquete->costo_paquete,
                'descuento' => $paquete->descuento,
                'costo_con_descuento' => $paquete->descuento > 0 && $aplicaDescuento ? "{$paquete->costo_paquete} Bs <br/> {$paquete->precio_final_html}"   : $paquete->costo_paquete,
                'nombre_area' => $paquete->area->descripcion,
                'status' => $paquete->status ?? 1,
            ]);
        }

        return DataTables::of($paquetes)
            ->addColumn('acciones', function ($row) {
                return '
                <div class="flex space-x-1 justify-center">
                    <button 
                        class="agregar-paquete bg-green-100 hover:bg-green-200 text-green-700 px-2 py-1 rounded shadow-sm"
                        data-id="' . $row['id'] . '" 
                        data-paquete="' . e($row['nombre_paquete']) . '" 
                        data-costo="' . $row['costo'] . '" 
                        data-descuento="' . $row['descuento'] . '" 
                        data-area-id="' . e($row['nombre_area']) . '" 
                        data-area="' . e($row['nombre_area']) . '"
                        data-tippy-content="Agregar paquete"
                    >
                    <i class="fas fa-plus-circle"></i>
                    </button>
                </div>';
            })
            ->rawColumns(['acciones', 'costo_con_descuento', 'nombre_paquete'])
            ->toJson();
    }

    public function getPaquetePorAreaAjax(Request $request, $id)
    {
        $area = Area::with('paquetes')->findOrFail($id);
        $paquetes = $area->paquetes;
        return response()->json($paquetes);
    }
}
