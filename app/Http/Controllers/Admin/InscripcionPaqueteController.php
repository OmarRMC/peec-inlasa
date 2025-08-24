<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AprobarInscripcion;
use App\Mail\EnvioCodigoLab;
use App\Mail\EnvioObsLab;
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
use Illuminate\Support\Facades\Mail;
use PhpParser\Node\Stmt\TryCatch;

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
            'gestiones' => Configuracion::GESTION_FILTER,
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
        if (
            !(
                Gate::any([Permiso::ADMIN, Permiso::GESTION_INSCRIPCIONES]) ||
                (Gate::any([Permiso::LABORATORIO]) && Configuracion::esPeriodoInscripcion())
            )
        ) {
            return redirect()->back()->with('error', 'No tienes permisos para realizar esta acción.');
        }
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
            return redirect('/')->with('error', 'No tiene autorización para acceder a esta sección.');
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



            $gestion = configuracion(Configuracion::GESTION_ACTUAL);
            $vigenciaInscripcion = new VigenciaInscripcion();

            $vigenciaInscripcion->status = true;
            $vigenciaInscripcion->id_inscripcion = $ins->id;
            $vigenciaInscripcion->fecha_inicio = $now;
            $vigenciaInscripcion->fecha_fin = Carbon::create($gestion)->endOfYear();;
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
        $lab =  $ins->laboratorio;
        $user = $lab->usuario;
        $gestion = $ins->gestion;
        try {
            Mail::to($user->email)->send(new AprobarInscripcion($user, $lab, $gestion));
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return back()->with('warning', 'La inscripción fue aprobada correctamente, pero no se pudo enviar el correo de notificación.');
        }
        return back()->with('success', 'La inscripción fue aprobada exitosamente.');
    }

    public function anularInscripcion(Request $request,  $id)
    {
        $ins = Inscripcion::findOrFail($id);
        $ins->status_inscripcion = Inscripcion::STATUS_ANULADO;
        $ins->updated_by = Auth::user()->id;
        $ins->updated_at = now();
        $ins->save();
        return back()->with('success', 'Se anulo la Inscripcion.');
    }
    public function enRevision(Request $request,  $id)
    {
        $ins = Inscripcion::findOrFail($id);
        $ins->status_inscripcion = Inscripcion::STATUS_EN_REVISION;
        $ins->updated_by = Auth::user()->id;
        $ins->updated_at = now();
        $ins->save();
        return back()->with('success', 'La inscripción esta en revision.');
    }


    public function obsInscripcion(Request $request,  $id)
    {
        // $ins = Inscripcion::findOrFail($id);
        // $ins->status_inscripcion = Inscripcion::STATUS_APROBADO;
        // $ins->updated_by = Auth::user()->id;
        // $ins->updated_at = now();
        // $ins->save();
        $ins = Inscripcion::findOrFail($id);
        $ins->status_inscripcion = Inscripcion::STATUS_EN_OBSERVACION;
        $ins->updated_by = Auth::user()->id;
        $ins->updated_at = now();
        $ins->save();
        $lab =  $ins->laboratorio;
        $user = $lab->usuario;
        $obs = $request->observacion;
        Log::info('$obs');
        Log::info($obs);
        $titulo =  $request->titulo;

        $observaciones = array_combine($titulo, $obs);
        $observaciones = array_filter($observaciones, function ($valor) {
            return !empty(trim($valor));
        });
        Log::info($observaciones);
        try {
            Mail::to($user->email)->send(new EnvioObsLab($user, $lab, $observaciones));
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return back()->with('warning', 'Las observaciones no se pudo enviar el correo de notificación.');
        }

        return back()->with('success', 'Las observaciones fueron enviadas.');
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

    public function certificadoDesempenoIndex(Request $request)
    {
        if (!Gate::any([Permiso::ADMIN, Permiso::GESTION_CERTIFICADOS])) {
            return redirect('/')->with('error', 'No tiene autorización para acceder a esta sección.');
        }
        $ensayos = EnsayoAptitud::query()
            ->select('ensayo_aptitud.*')
            ->join('paquete', 'ensayo_aptitud.id_paquete', '=', 'paquete.id')
            ->join('area', 'paquete.id_area', '=', 'area.id')
            ->with(['paquete', 'paquete.area'])
            ->orderBy('area.descripcion', 'asc')
            ->orderBy('paquete.descripcion', 'asc')
            ->orderBy('ensayo_aptitud.descripcion', 'asc')
            ->paginate(20);
        $gestion = $request->gestion ?? now()->year;
        return view('certificados.desempeno.index', [
            'gestiones' => Configuracion::GESTION_FILTER,
            'gestion' => $gestion,
            'ensayos' => $ensayos,
        ]);
    }

    // public function certificadoDesempenoAjaxIndex(Request $request, $idEA)
    // {
    //     $query = Inscripcion::with([
    //         'laboratorio',
    //         'certificado.detalles'
    //     ])
    //         ->whereHas('certificado')
    //         ->whereHas('certificado.detalles', function ($q) {
    //             $q->where('temporal', false);
    //         });

    //     if ($request->filled('gestion')) {
    //         $query->where('gestion', $request->gestion);
    //     }
    //     if ($request->has('status_ins') && $request->get('status_ins')) {
    //         $query->where('status_inscripcion', $request->status_ins);
    //     }

    //     // Traemos todas las inscripciones
    //     $inscripciones = $query->get();

    //     // Expandimos cada inscripción en varias filas (una por detalle)
    //     $filas = $inscripciones->flatMap(function ($ins) {
    //         return $ins->certificado->detalles->map(function ($detalle) use ($ins) {
    //             return [
    //                 'created_at' => $ins->created_at,
    //                 'gestion'    => $ins->gestion,
    //                 'cod_lab'    => $ins->laboratorio->cod_lab,
    //                 'nombre_lab' => $ins->laboratorio->nombre_lab,
    //                 'wapp_lab'   => $ins->laboratorio->wapp_lab,
    //                 'mail_lab'   => $ins->laboratorio->mail_lab ?? '-',
    //                 'detalle'    => $detalle, // el detalle específico (ej. calificacion_certificado)
    //             ];
    //         });
    //     });

    //     return datatables()
    //         ->of($filas)
    //         ->addColumn('calificacion', fn($row) => $row['detalle']->calificacion_certificado ?? '-')
    //         ->addColumn('acciones', function ($row) {
    //             return view('responsable.certificados.action-buttons', [
    //                 'updateUrl' => route('detalle-certificado.update', $row['detalle']),
    //                 'desempeno' => $row['detalle']->calificacion_certificado ?? '',
    //             ])->render();
    //         })
    //         ->rawColumns(['acciones'])
    //         ->toJson();
    // }

    public function certificadoDesempenoAjaxIndex(Request $request, $idEa)
    {
        $query = InscripcionEA::with([
            'inscripcion',
            'inscripcion.laboratorio',
            'inscripcion.certificado.detalles'
        ])
            ->where('inscripcion_ea.id_ea', $idEa)
            ->whereHas('inscripcion', function ($q) {
                $q->Aprobado();
            })
            ->whereHas('inscripcion.certificado.detalles', function ($q) use ($idEa) {
                $q->where('temporal', false)
                    ->where('id_ea', $idEa);
            });
        if ($request->filled('gestion')) {
            $query->whereHas('inscripcion', function ($q) use ($request) {
                $q->where('gestion', $request->gestion);
            });
        }
        return datatables()
            ->of($query)
            ->addColumn('created_at', fn($ea) => $ea->inscripcion->certificado->detalles->first()?->created_at ?? '-')
            ->addColumn('nombre_lab', fn($ea) => $ea->inscripcion->laboratorio->nombre_lab)
            ->addColumn('cod_lab', fn($ea) => $ea->inscripcion->laboratorio->cod_lab)
            ->addColumn('wapp_lab', fn($ea) => $ea->inscripcion->laboratorio->wapp_lab)
            ->addColumn('mail_lab', fn($ea) => $ea->inscripcion->laboratorio->mail_lab ?? '-')
            ->addColumn('gestion', fn($ea) => $ea->inscripcion->gestion ?? '-')
            ->addColumn('acciones', function ($ea) {
                return view('responsable.certificados.action-buttons', [
                    'updateUrl' => route('detalle-certificado.update', $ea->inscripcion->certificado->detalles->first()?->id),
                    'desempeno' => $ea->inscripcion->certificado?->detalles?->first()?->calificacion_certificado ?? '',
                ])->render();
            })
            ->rawColumns(['acciones'])
            ->toJson();
    }

    //certificadoDesempenoListLabs
    public function certificadoDesempenoListLabs($id)
    {
        $idEA = $id;
        $gestiones = Configuracion::GESTION_FILTER;
        $ensayoA = EnsayoAptitud::findOrFail($idEA);
        return view('certificados.desempeno.show', compact('idEA', 'gestiones', 'ensayoA'));
    }
}
