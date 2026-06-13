<?php

namespace App\Http\Controllers\responsable;

use App\Exports\InscripcionesEaLabExport;
use App\Http\Controllers\Controller;
use App\Models\CategoriaLaboratorio;
use App\Models\Certificado;
use App\Models\Configuracion;
use App\Models\DetalleCertificado;
use App\Models\EnsayoAptitud;
use App\Models\Inscripcion;
use App\Models\InscripcionEA;
use App\Models\NivelLaboratorio;
use App\Models\Pais;
use App\Models\Permiso;
use App\Models\PlantillaCertificado;
use App\Models\TipoLaboratorio;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Normalizer;

class LaboratorioController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:' . Permiso::RESPONSABLE)->only(['create', 'update', 'destroy', 'show', 'edit']);
    }

    public function misEnsayos(Request $request)
    {
        $responsable = Auth::user();
        $gestion = (int) $request->query('gestion', now()->year);

        if (Gate::any([Permiso::JEFE_PEEC])) {
            $ensayos = EnsayoAptitud::with('paquete.area')->orderBy('descripcion')->get();
        } else {
            $ensayos = $responsable->load('responsablesEA.paquete.area')->responsablesEA->sortBy('descripcion')->values();
        }

        $idsEa = $ensayos->pluck('id')->all();
        $estadosActivos = [Inscripcion::STATUS_APROBADO, Inscripcion::STATUS_VENCIDO];

        $conteoPorEa = InscripcionEA::whereIn('id_ea', $idsEa)
            ->whereHas('inscripcion', fn($q) => $q->where('gestion', $gestion)->whereIn('status_inscripcion', $estadosActivos))
            ->join('inscripcion', 'inscripcion.id', '=', 'inscripcion_ea.id_inscripcion')
            ->selectRaw('inscripcion_ea.id_ea, COUNT(DISTINCT inscripcion.id_lab) as inscritos')
            ->groupBy('inscripcion_ea.id_ea')
            ->pluck('inscritos', 'id_ea');

        $totalLabsUnicos = InscripcionEA::whereIn('id_ea', $idsEa)
            ->whereHas('inscripcion', fn($q) => $q->where('gestion', $gestion)->whereIn('status_inscripcion', $estadosActivos))
            ->join('inscripcion', 'inscripcion.id', '=', 'inscripcion_ea.id_inscripcion')
            ->distinct('inscripcion.id_lab')
            ->count('inscripcion.id_lab');

        $totalInscritos = $conteoPorEa->sum();

        $gestiones = Inscripcion::rangoGestion([
            'status_inscripcion' => $estadosActivos,
        ]);

        return view('responsable.lab.mis-ensayos', [
            'ensayos' => $ensayos,
            'conteoPorEa' => $conteoPorEa,
            'totalLabsUnicos' => $totalLabsUnicos,
            'totalInscritos' => $totalInscritos,
            'gestion' => $gestion,
            'gestiones' => $gestiones,
        ]);
    }

    public function index(Request $request, $idEa)
    {
        $responsable = Auth::user();
        $ensayosAptitud = null;
        if (Gate::any([Permiso::JEFE_PEEC])) {
            $ensayosAptitud = EnsayoAptitud::find($idEa);
        }
        if (Gate::allows(Permiso::RESPONSABLE)) {
            $ensayosAptitud = $responsable->responsablesEA->find($idEa);
        }
        if (!$ensayosAptitud) {
            return redirect('/')->with('error', 'No se tiene el ensayo de aptitud solicitado.');
        }
        $ensayosAptitud->loadMissing('paquete');
        $laboratorios = InscripcionEA::with('inscripcion.laboratorio')
            ->where('id_ea', $idEa)
            ->whereHas('inscripcion', function ($q) {
                $q->where('gestion', now()->year);
            })
            ->get()
            ->pluck('inscripcion.laboratorio')
            ->unique('id')
            ->values();
        $paises = Pais::active()->get();
        $tipos = TipoLaboratorio::active()->get();
        $categorias = CategoriaLaboratorio::active()->get();
        $niveles = NivelLaboratorio::active()->get();
        $gestiones = Inscripcion::rangoGestion([
            'status_inscripcion' => [Inscripcion::STATUS_APROBADO, Inscripcion::STATUS_VENCIDO],
        ]);
        return view('responsable.lab.index', compact('laboratorios', 'paises', 'tipos', 'categorias', 'niveles', 'idEa', 'ensayosAptitud', 'gestiones'));
    }

    public function getData(Request $request, $idEa)
    {
        $responsable = Auth::user();
        $ensayosAptitud = null;
        if (Gate::any([Permiso::JEFE_PEEC])) {
            $ensayosAptitud = EnsayoAptitud::find($idEa);
        }
        if (Gate::any([Permiso::RESPONSABLE])) {
            $ensayosAptitud = $responsable->responsablesEA->find($idEa);
        }
        if (!$ensayosAptitud) {
            return collect();
        }
        $query = InscripcionEA::with(['inscripcion', 'inscripcion.laboratorio.departamento', 'inscripcion.laboratorio.usuario'])
            ->where('id_ea', $idEa)
            ->whereHas('inscripcion', function ($q) use ($request) {
                if ($request->filled('gestion')) {
                    $q->where('gestion', $request->gestion);
                }
                $q->aprobadoOrVencido();
            });
        // Log::info($Inscripciones);
        // Consultar solo los laboratorios filtrados por ID
        // $query = Laboratorio::whereIn('id', $laboratorioIds)
        //     ->with(['pais', 'usuario', 'departamento', 'provincia', 'municipio', 'tipo', 'categoria', 'nivel']);

        // Aplicar filtros adicionales
        foreach (['pais', 'dep', 'prov', 'mun', 'tipo', 'categoria', 'nivel'] as $f) {
            if ($val = $request->get($f)) {
                $query->where("id_{$f}", $val);
            }
        }

        // Retornar respuesta para DataTables
        return datatables()
            ->of($query)
            ->addColumn('fecha_inscripcion', fn($ins) => $ins->inscripcion->fecha_inscripcion)
            ->addColumn('nombre_lab', fn($ins) => $ins->inscripcion->laboratorio->nombre_lab)
            ->addColumn('mail_lab', fn($ins) => $ins->inscripcion->laboratorio->mail_lab)
            ->addColumn('wapp_lab', fn($ins) => $ins->inscripcion->laboratorio->wapp_lab)
            // ->addColumn('nombre_lab', fn($ins) => $ins->inscripcion->laboratorio->nombre_lab)
            ->addColumn('nombre_dep', fn($ins) => $ins->inscripcion->laboratorio->departamento->nombre_dep ?? '-')
            ->addColumn('provincia_nombre', fn($ins) => $ins->inscripcion->laboratorio->provincia->nombre_prov ?? '-')
            ->addColumn('municipio_nombre', fn($ins) => $ins->inscripcion->laboratorio->municipio->nombre_municipio ?? '-')
            ->addColumn('codigo', fn($ins) => $ins->inscripcion->laboratorio->cod_lab)
            ->addColumn('tipo_nombre', fn($ins) => $ins->inscripcion->laboratorio->tipo->nombre_tipo ?? '-')
            ->addColumn('categoria_nombre', fn($ins) => $ins->inscripcion->laboratorio->categoria->nombre_categoria ?? '-')
            ->addColumn('nivel_nombre', fn($ins) => $ins->inscripcion->laboratorio->nivel->nombre ?? '-')
            ->addColumn('email', fn($ins) => $ins->inscripcion->laboratorio->usuario->email ?? '-')
            ->addColumn('status_label', fn($ins) => $ins->inscripcion->laboratorio->status ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-danger">Inactivo</span>')
            ->addColumn('status_inscripcion', fn($ins) => $ins->inscripcion->getStatusInscripcion() ?? '-')
            ->addColumn('actions', function ($ins) {
                return view('responsable.lab.action-buttons', [
                    // 'showUrl' => route('laboratorio.show', $lab->id),
                    // 'editUrl' => route('laboratorio.edit', $lab->id),
                    // 'deleteUrl' => route('laboratorio.destroy', $lab->id),
                    // 'inscribirUrl' => route('inscripcion.create', $lab->id),
                    // 'nombre' => $lab->nombre_lab,
                    // 'id' => $lab->id,
                    // 'activo' => $lab->status,
                ])->render();
            })
            ->rawColumns(['status_label', 'actions', 'status_inscripcion'])
            ->toJson();
    }

    /**
     * Exporta la lista de laboratorios inscritos a un EA en Excel, CSV, PDF o JSON.
     * Reutiliza el mismo control de acceso y filtro (gestión + aprobadoOrVencido) que getData().
     */
    public function exportInscripciones(Request $request, $idEa)
    {
        $responsable = Auth::user();
        $ensayosAptitud = null;
        if (Gate::any([Permiso::JEFE_PEEC])) {
            $ensayosAptitud = EnsayoAptitud::find($idEa);
        }
        if (Gate::any([Permiso::RESPONSABLE])) {
            $ensayosAptitud = $responsable->responsablesEA->find($idEa);
        }
        if (!$ensayosAptitud) {
            return redirect('/')->with('error', 'No se tiene el ensayo de aptitud solicitado.');
        }
        $ensayosAptitud->loadMissing('paquete');

        $gestion = $request->query('gestion', now()->year);

        $inscripciones = InscripcionEA::with(['inscripcion', 'inscripcion.laboratorio.departamento'])
            ->where('id_ea', $idEa)
            ->whereHas('inscripcion', function ($q) use ($gestion) {
                $q->where('gestion', $gestion)->aprobadoOrVencido();
            })
            ->get();

        // Filas en el mismo orden que las columnas visibles de la tabla.
        $laboratorios = $inscripciones->map(function ($ins) {
            $lab = $ins->inscripcion->laboratorio;
            return [
                $ins->inscripcion->fecha_inscripcion,
                $lab->cod_lab,
                $lab->nombre_lab,
                $lab->mail_lab,
                $lab->wapp_lab,
                $lab->departamento->nombre_dep ?? '-',
                $ins->inscripcion->status_inscripcion_label,
            ];
        })->values();

        $format = $request->query('format', 'xlsx');
        $fileName = 'InscripcionesEA_' . now()->format('Y-m-d_H-i-s') . '.' . $format;

        if ($format === 'json') {
            $jsonContent = $laboratorios->map(fn($fila) => [
                'fecha_hora' => $fila[0],
                'codigo' => $fila[1],
                'nombre' => $fila[2],
                'correo' => $fila[3],
                'whatsapp' => $fila[4],
                'departamento' => $fila[5],
                'estado' => $fila[6],
            ])->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return response()->streamDownload(function () use ($jsonContent) {
                echo $jsonContent;
            }, $fileName, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);
        }

        if ($format === 'pdf') {
            $generadoPor = trim("{$responsable->nombre} {$responsable->ap_paterno} {$responsable->ap_materno}");
            return Pdf::loadView('responsable.lab.exports.inscripciones-pdf', [
                'laboratorios' => $laboratorios,
                'ensayosAptitud' => $ensayosAptitud,
                'gestion' => $gestion,
                'generado_en' => now(),
                'generado_por' => $generadoPor !== '' ? $generadoPor : ($responsable->username ?? 'Usuario'),
            ])->setPaper('a4', 'landscape')->download($fileName);
        }

        $export = new InscripcionesEaLabExport($laboratorios);
        switch ($format) {
            case 'csv':
                return Excel::download($export, $fileName, \Maatwebsite\Excel\Excel::CSV);
            case 'xlsx':
            default:
                return Excel::download($export, $fileName);
        }
    }

    public function showUploadCertificado(Request $request, $id)
    {
        if (!Gate::any([Permiso::JEFE_PEEC]) && !Configuracion::estaHabilitadoCargarCertificado()) {
            return redirect('/')->with('error', 'El periodo para la carga de desempeño no está habilitado actualmente.');
        }
        $responsable = Auth::user();
        $ensayosAptitud = null;
        if (Gate::any([Permiso::JEFE_PEEC])) {
            $ensayosAptitud = EnsayoAptitud::find($id);
        }
        if (Gate::allows(Permiso::RESPONSABLE)) {
            $ensayosAptitud = $responsable->responsablesEA->findOrFail($id);
        }
        if (!$ensayosAptitud) {
            return redirect('/')->with('error', 'No se tiene el ensayo de aptitud solicitado.');
        }
        $paquete = $ensayosAptitud->paquete;
        $idEA = $ensayosAptitud->id;

        $eaDesc = mb_strtolower(
            trim(Normalizer::normalize($ensayosAptitud->descripcion, Normalizer::FORM_C)),
            'UTF-8',
        );
        $paqueteDesc = mb_strtolower(
            trim(Normalizer::normalize($paquete->descripcion, Normalizer::FORM_C)),
            'UTF-8',
        );
        $descripcion = $ensayosAptitud->descripcion;
        if ($eaDesc != $paqueteDesc) {
            $descripcion = "</br>  $paquete->descripcion / $ensayosAptitud->descripcion";
        }

        $gestion = $request->query('gestion', now()->year);
        $gestiones = Inscripcion::rangoGestion([
            'status_inscripcion' => [Inscripcion::STATUS_APROBADO, Inscripcion::STATUS_VENCIDO],
        ]);
        return view('responsable.upload_certificados', compact('ensayosAptitud', 'idEA', 'gestion', 'descripcion', 'gestiones'));
    }
    public function uploadCertificadoData(Request $request, $id)
    {
        if (!Gate::any([Permiso::JEFE_PEEC]) && !Configuracion::estaHabilitadoCargarCertificado()) {
             return redirect()->back()->with('notice', [
                'title' => 'Error del periodo de registro',
                'message' => 'El periodo para la carga de desempeño no está habilitado actualmente.',
                'type' => 'error',
            ]);
        }
        $responsable = Auth::user();
        $ensayosAptitud = null;
        if (Gate::any([Permiso::JEFE_PEEC])) {
            $ensayosAptitud = EnsayoAptitud::find($id);
        } else if (Gate::allows(Permiso::RESPONSABLE)) {
            $ensayosAptitud = $responsable->responsablesEA->find($id);
        }
        if (!$ensayosAptitud) {
            return redirect()->back()->with('error', 'No se tiene el ensayo de aptitud solicitado.');
        }

        $request->validate([
            'archivo' => 'required|mimes:csv,txt|max:2048',
        ]);

        $path = $request->file('archivo')->getRealPath();
        $rows = collect();
        if (($handle = fopen($path, 'r')) !== false) {
            $header = fgetcsv($handle, 1000, ','); // cabecera
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                $rows->push([
                    'codigoLab' => $data[0] ?? null,
                    'desempeno' => $data[1] ?? null,
                ]);
            }
            fclose($handle);
        }
        $inscripciones = InscripcionEA::with([
            'inscripcion',
            'ensayoAptitud.paquete.area',
            'inscripcion.laboratorio.departamento',
            'inscripcion.laboratorio.usuario'
        ])
            ->where('id_ea', $id)
            ->whereHas('inscripcion', function ($q) use ($request) {
                $q->where('gestion', $request->query('gestion', $request->query('gestion', now()->year)))
                    ->aprobadoOrVencido();
            })
            ->get();

        $configDirGen = configuracion(Configuracion::CARGO_DIRECTORA_GENERAL);
        $configCoordRed = configuracion(Configuracion::CARGO_COORDINADORA_RED);
        $configEvalExt = configuracion(Configuracion::CARGO_EVALUACION_EXTERNA);

        $plantillaSeleccionada = PlantillaCertificado::where('activo', PlantillaCertificado::SELECCIONADO)->first();
        foreach ($inscripciones as $inscripcionEA) {
            $inscripcion = $inscripcionEA->inscripcion;
            $lab = $inscripcion->laboratorio;

            $fila = $rows->first(function ($row) use ($lab) {
                return $row['codigoLab'] == $lab->cod_lab;
            });

            if (!$fila) {
                Log::info('No se encontró fila para el laboratorio: ', [
                    'codigo' => $lab->cod_lab,
                    'nombre' => $lab->nombre_lab,
                    'id' => $lab->id,
                    'inscripcion_id' => $inscripcion->id,
                ]);
                continue;
            }

            $certificado = Certificado::firstOrCreate(
                ['id_inscripcion' => $inscripcion->id],
                [
                    'gestion_certificado' => $inscripcion->gestion,
                    'nombre_laboratorio' => $lab->nombre_lab,
                    'cod_lab' => $lab->cod_lab,
                    'status_certificado' => 0,
                    'plantilla_certificado_id' => $plantillaSeleccionada?->id,
                ]
            );
            DetalleCertificado::updateOrCreate(
                [
                    'id_certificado' => $certificado->id,
                    'id_ea'          => $id,
                ],
                [
                    'detalle_ea' => $inscripcionEA->descripcion_ea,
                    'detalle_area' => $inscripcionEA->ensayoAptitud->paquete->area->descripcion ?? null,
                    'calificacion_certificado' => $fila['desempeno'] != 'NULL' ? $fila['desempeno'] : null,
                    'updated_by' => $responsable->id,
                    'temporal' => true,
                ]
            );
        }
        return back()->with('success', 'Archivo procesado correctamente, Puedes revisar en al sección de Revision');
    }

    public function getLaboratoriosDesempenoTemporal(Request $request, $idEa)
    {
        $responsable = Auth::user();
        $ensayosAptitud = null;
        if (Gate::any([Permiso::JEFE_PEEC])) {
            $ensayosAptitud = EnsayoAptitud::find($idEa);
        } else if (Gate::allows(Permiso::RESPONSABLE)) {
            $ensayosAptitud = $responsable->responsablesEA->find($idEa);
        }
        if (!$ensayosAptitud) {
            return redirect('/')->with('error', 'No se tiene el ensayo de aptitud solicitado.');
        }
        $gestion = $request->query('gestion', now()->year);
        $query = Inscripcion::with([
            'laboratorio',
            'certificado',
            'certificado.detalles' => function ($q) use ($idEa) {
                $q->where('temporal', true)
                    ->where('id_ea', $idEa);
            }
        ])
            ->where('gestion',  $gestion)
            ->aprobadoOrVencido()
            ->whereHas('ensayos', function ($q) use ($idEa) {
                $q->where('id_ea', $idEa);
            })
            ->whereHas('certificado.detalles', function ($q) use ($idEa) {
                $q->where('temporal', true)
                    ->where('id_ea', $idEa);
            })->get();

        return datatables()
            ->of($query)
            ->addColumn('created_at', fn($insc) => $insc->certificado->detalles->first()?->created_at ?? '-')
            ->addColumn('updated_at', fn($insc) => $insc->certificado->detalles->first()?->updated_at ?? '-')
            ->addColumn('nombre_lab', fn($insc) => $insc->laboratorio->nombre_lab)
            ->addColumn('cod_lab', fn($insc) => $insc->laboratorio->cod_lab)
            ->addColumn('wapp_lab', fn($insc) => $insc->laboratorio->wapp_lab)
            ->addColumn('mail_lab', fn($insc) => $insc->laboratorio->mail_lab ?? '-')
            ->addColumn('actions', function ($insc) {
                return view('responsable.certificados.action-buttons', [
                    'updateUrl' => route('detalle-certificado.update', $insc->certificado->detalles->first()?->id),
                    'desempeno' => $insc->certificado->detalles->first()?->calificacion_certificado ?? '',
                ])->render();
            })
            ->rawColumns(['actions'])
            ->toJson();
    }

    public function confirmarDatosCertificados(Request $request, $idEa)
    {
        // if (!Gate::any([Permiso::RESPONSABLE])) {
        //     return redirect('/')->withErrors(['error' => 'No tienes permiso para realizar esta acción.']);
        // }
        $responsable = Auth::user();
        $ensayosAptitud = null;
        if (Gate::any([Permiso::JEFE_PEEC])) {
            $ensayosAptitud = EnsayoAptitud::find($idEa);
        } else if (Gate::allows(Permiso::RESPONSABLE)) {
            $ensayosAptitud = $responsable->responsablesEA->find($idEa);
        }
        if (!$ensayosAptitud) {
            return redirect('/')->with('error', 'No se tiene el ensayo de aptitud solicitado.');
        }
        $gestion  = $request->gestion ?? now()->year;
        $query = Inscripcion::with([
            'laboratorio',
            'certificado',
            'certificado.detalles' => function ($q) use ($idEa) {
                $q->where('temporal', true)
                    ->where('id_ea', $idEa);
            }
        ])
            ->where('gestion', $gestion)
            ->aprobadoOrVencido()
            ->whereHas('ensayos', function ($q) use ($idEa) {
                $q->where('id_ea', $idEa);
            })
            ->whereHas('certificado.detalles', function ($q) use ($idEa) {
                $q->where('temporal', true)
                    ->where('id_ea', $idEa);
            })->get();
        try {
            foreach ($query as $inscripcion) {
                $certificado = $inscripcion->certificado;
                $detalle = $certificado->detalles->first();

                if ($detalle) {
                    $detalle->temporal = false;
                    $detalle->updated_by = $responsable->id;
                    $detalle->save();
                    $certificado->status_certificado = 1;
                    $certificado->save();
                }
            }
        } catch (\Exception $e) {
            Log::error('Error al confirmar datos de certificados: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Ocurrió un error al confirmar los datos de los certificados. Por favor, inténtalo de nuevo.']);
        }
        return redirect()->back()->with('success', 'Datos de certificados confirmados correctamente.');
    }

    public function getLaboratoriosDesempenoConfirmados(Request $request,  $idEa)
    {
        $responsable = Auth::user();
        $ensayosAptitud = null;
        if (Gate::any([Permiso::JEFE_PEEC])) {
            $ensayosAptitud = EnsayoAptitud::find($idEa);
        } else if (Gate::allows(Permiso::RESPONSABLE)) {
            $ensayosAptitud = $responsable->responsablesEA->find($idEa);
        }
        if (!$ensayosAptitud) {
            return redirect('/')->with('error', 'No se tiene el ensayo de aptitud solicitado.');
        }
        $gestion = $request->query('gestion', now()->year);
        $query = Inscripcion::with([
            'laboratorio',
            'certificado',
            'certificado.detalles' => function ($q) use ($idEa) {
                $q->where('temporal', false)
                    ->where('id_ea', $idEa);
            }
        ])
            ->where('gestion', $gestion)
            ->aprobadoOrVencido()
            ->whereHas('ensayos', function ($q) use ($idEa) {
                $q->where('id_ea', $idEa);
            })
            ->whereHas('certificado.detalles', function ($q) use ($idEa) {
                $q->where('temporal', false)
                    ->where('id_ea', $idEa);
            })->get();
        return datatables()
            ->of($query)
            ->addColumn('created_at', fn($ins) => $ins->certificado->detalles->first()?->created_at ?? '-')
            ->addColumn('updated_at', fn($ins) => $ins->certificado->detalles->first()?->updated_at ?? '-')
            ->addColumn('nombre_lab', fn($ins) => $ins->laboratorio->nombre_lab)
            ->addColumn('cod_lab', fn($ins) => $ins->laboratorio->cod_lab)
            ->addColumn('wapp_lab', fn($ins) => $ins->laboratorio->wapp_lab)
            ->addColumn('mail_lab', fn($ins) => $ins->laboratorio->mail_lab ?? '-')
            ->addColumn('desempeno', fn($ins) => $ins->certificado->detalles->first()?->calificacion_certificado ?? '')
            ->toJson();
    }
}
