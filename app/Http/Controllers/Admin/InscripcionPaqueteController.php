<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\CategoriaLaboratorio;
use App\Models\Configuracion;
use App\Models\Laboratorio;
use App\Models\Paquete;
use App\Models\Programa;
use App\Models\DetalleInscripcion;
use App\Models\EnsayoAptitud;
use App\Models\Formulario;
use App\Models\Inscripcion;
use App\Models\InscripcionEA;
use App\Models\NivelLaboratorio;
use App\Models\Pais;
use App\Models\Permiso;
use App\Models\TipoLaboratorio;
use App\Models\VigenciaInscripcion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class InscripcionPaqueteController extends Controller
{
    public function __construct()
    {
        $this->middleware('canany:' . Permiso::GESTION_INSCRIPCIONES . ',' . Permiso::ADMIN)->only(
            ['index', 'getInscripcionesData', 'show', 'paquetesPorPrograma', 'aprobarInscripcion']
        );
    }
    public function index()
    {
        return view('inscripcion_paquete.index', [
            'paises' => Pais::active()->get(),
            'niveles' => NivelLaboratorio::all(),
            'tipos' => TipoLaboratorio::all(),
            'departamentos' => [],
            'provincias' => [],
            'municipios' => [],
            'areas' => Area::orderBy('descripcion', 'asc')->get(),
            'now' => now()->format('Y-m-d'),
            'categorias' => CategoriaLaboratorio::all(),
            // 'paquetes' => Paquete::orderBy('descripcion', 'asc')->get(),
            'paquetes' => [],
            'gestiones' => ['2025', '2024', '2023'],
        ]);
    }
    public function getInscripcionesData(Request $request)
    {
        $query = Inscripcion::with([
            'laboratorio.pais',
            'laboratorio.tipo',
            'laboratorio.categoria',
            'laboratorio.nivel',
            'detalleInscripciones',
        ]);

        foreach (['pais', 'tipo', 'categoria', 'nivel', 'dep', 'prov', 'municipio'] as $filtro) {
            if ($valor = $request->get($filtro)) {
                $query->whereHas('laboratorio', function ($q) use ($filtro, $valor) {
                    $q->where("id_{$filtro}", $valor);
                });
            }
        }

        if ($request->filled('paquetes') && is_array($request->paquetes)) {
            $paquetesIds = $request->paquetes;
            $query->whereHas('detalleInscripciones', function ($q) use ($paquetesIds) {
                $q->whereIn('id_paquete', $paquetesIds);
            });
        }
        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('fecha_inscripcion', [
                Carbon::parse($request->fecha_inicio)->startOfDay(),
                Carbon::parse($request->fecha_fin)->endOfDay()
            ]);
        }

        if ($request->filled('gestion')) {
            $query->where('gestion', $request->gestion);
        }
        if ($request->has('status_cuenta') && $request->get('status_cuenta')) {
            Log::info('$request->status_cuenta');
            Log::info($request->status_cuenta ? '1' : '01');
            $query->where('status_cuenta', $request->status_cuenta);
        }

        if ($request->has('status_ins') && $request->get('status_ins')) {
            Log::info('$request->status_ins');
            Log::info($request->status_ins ? '1' : '01');
            $query->where('status_inscripcion', $request->status_ins);
        }
        return datatables()
            ->of($query)
            ->addColumn('nombre_lab', fn($i) => $i->laboratorio->nombre_lab ?? '-')
            ->addColumn('codigo_lab', fn($i) => $i->laboratorio->cod_lab ?? '-')
            ->addColumn('pais', fn($i) => $i->laboratorio->pais->nombre_pais ?? '-')
            ->addColumn('tipo', fn($i) => $i->laboratorio->tipo->descripcion ?? '-')
            ->addColumn('categoria', fn($i) => $i->laboratorio->categoria->descripcion ?? '-')
            ->addColumn('nivel', fn($i) => $i->laboratorio->nivel->descripcion_nivel ?? '-')
            ->addColumn('fecha', fn($i) => $i->fecha_inscripcion)
            ->addColumn('gestion', fn($i) => $i->gestion)
            ->addColumn('paquetes', fn($i) => $i->detalleInscripciones->pluck('descripcion_paquete')->implode(', '))
            ->addColumn('costo', fn($i) => number_format($i->costo_total, 2) . ' Bs.')
            ->addColumn('estado', fn($i) => $i->getStatusInscripcion())
            ->addColumn('cuenta', fn($i) => $i->getStatusCuenta())
            ->addColumn('acciones', function ($i) {
                return view('inscripcion_paquete.action-buttons', [
                    'showUrl' => route('inscripcion_paquete.show', $i->id),
                    'boletaPdf' => route('formulario_inscripcion_lab.pdf', $i->id),
                    'contratoPdf' => route('formulario_contrato_lab.pdf', $i->id),
                ])->render();
            })
            ->rawColumns(['estado', 'cuenta', 'acciones'])
            ->toJson();
    }

    public function create($labId)
    {
        $laboratorio = Laboratorio::findOrFail($labId);
        $tipoLabId = $laboratorio->id_tipo;

        $programas = Programa::active()
            ->whereHas('areas.paquetes.tiposLaboratorios', function ($query) use ($tipoLabId) {
                $query->where('tipo_laboratorio_id', $tipoLabId);
            })
            ->get();
        return view('inscripcion_paquete.create', compact('laboratorio', 'programas'));
    }

    public function paquetesPorPrograma(Request $request)
    {
        $labId = $request->lab_id;
        $laboratorio = Laboratorio::findOrFail($labId);
        $tipoLabId = $laboratorio->id_tipo;
        $paquetes = Paquete::active()
            ->whereHas('area', function ($query) use ($request) {
                $query->where('programa_id', $request->programa_id);
            })
            ->whereHas('tiposLaboratorios', function ($query) use ($tipoLabId) {
                $query->where('tipo_laboratorio_id', $tipoLabId);
            })
            ->get(['id', 'descripcion', 'costo_paquete']);
        return response()->json($paquetes);
    }

    public function store(Request $request)
    {
        if (!Gate::any([Permiso::ADMIN, Permiso::GESTION_INSCRIPCIONES, Permiso::LABORATORIO])) {
            abort(403, 'No se tiene Autorizacion para realizar esa accion');
        }
        $request->validate([
            'id_lab' => 'required|exists:laboratorio,id',
            'id_formulario' => 'nullable|exists:formulario,id',
            'paquetes' => 'required|array|min:1',
            'paquetes.*.id' => 'required|exists:paquete,id',
            'paquetes.*.costo' => 'required|integer|min:0',
            'obs_inscripcion' => 'nullable|string|max:255',
        ]);
        try {
            DB::beginTransaction();
            $total = collect($request->paquetes)->sum('costo');
            $now  = now();
            $formulario = Formulario::where('proceso', Formulario::INSCRIPCION)->first();
            $ins = Inscripcion::create([
                'id_lab' => $request->id_lab,
                'id_formulario' => optional($formulario)->id,
                'cant_paq' => count($request->paquetes),
                'costo_total' => $total,
                'obs_inscripcion' => $request->obs_inscripcion,
                'fecha_inscripcion' => $now,
                'status_cuenta' => false,
                'status_inscripcion' => true,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'gestion' => configuracion(Configuracion::GESTION_ACTUAL) ?? $request->gestion,
                'status_cuenta' => Inscripcion::STATUS_DEUDOR,
                'fecha_limite_pago' => configuracion(Configuracion::FECHA_FIN_PAGO)
            ]);



            $vigenciaInscripcion = new VigenciaInscripcion();

            $vigenciaInscripcion->status = true;
            $vigenciaInscripcion->id_inscripcion = $ins->id;
            $vigenciaInscripcion->fecha_inicio = $now;
            $vigenciaInscripcion->fecha_fin = configuracion('fecha.fin.vigencia') ?? now()->addDays(2);
            $vigenciaInscripcion->created_by = Auth::user()->id;
            $vigenciaInscripcion->updated_by = Auth::user()->id;
            $vigenciaInscripcion->save();


            foreach ($request->paquetes as $p) {
                DetalleInscripcion::create([
                    'id_inscripcion' => $ins->id,
                    'id_paquete' => $p['id'],
                    'descripcion_paquete' => $p['descripcion'],
                    'costo_paquete' => $p['costo'],
                    'observaciones' => $p['observaciones'],
                ]);
                $idsEA = EnsayoAptitud::where('id_paquete', $p['id'])
                    ->active()
                    ->pluck('descripcion', 'id');
                foreach ($idsEA as $id => $descripcion) {
                    InscripcionEA::create([
                        'id_inscripcion' => $ins->id,
                        'id_ea' => $id,
                        'descripcion_ea' => $descripcion,
                    ]);
                }
            }

            DB::commit();
            session()->flash('success', 'Inscripción registrado exitosamente.');
            return response()->json([
                'success' => true,
                'message' => 'Inscripción registrado exitosamente.',
                'inscripcion_id' => $ins->id,
                'redirect_url' => route('inscripcion_paquete.index')
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            session()->flash('error', 'Error en la Inscripción, Intente nuevamente.');
            Log::error('Error al iniciar transacción: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error en la inscripción, Intente de nuevo.',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function aprobarInscripcion(Request $request,  $id)
    {
        $ins = Inscripcion::findOrFail($id);
        $ins->status_inscripcion = Inscripcion::STATUS_APROBADO;
        $ins->updated_by = Auth::user()->id;
        $ins->updated_at = now();
        $ins->save();
        return back()->with('success', 'La inscripción fue aprobada exitosamente.');
    }

    public function show($id)
    {
        // $inscripcion = Inscripcion::with([
        //     'laboratorio.pais',
        //     'laboratorio.tipo',
        //     'laboratorio.categoria',
        //     'laboratorio.nivel',
        //     'detalleInscripciones'
        // ])->findOrFail($id);

        $inscripcion = Inscripcion::with([
            'laboratorio',
            'detalleInscripciones',
            'pagos',
            'documentos',
            'vigencia'
        ])->findOrFail($id);

        return view('inscripcion_paquete.show', compact('inscripcion'));
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
