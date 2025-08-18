<?php

namespace App\Http\Controllers\responsable;

use App\Http\Controllers\Controller;
use App\Models\CategoriaLaboratorio;
use App\Models\Certificado;
use App\Models\Configuracion;
use App\Models\DetalleCertificado;
use App\Models\Inscripcion;
use App\Models\InscripcionEA;
use App\Models\NivelLaboratorio;
use App\Models\Pais;
use App\Models\Permiso;
use App\Models\TipoLaboratorio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use SebastianBergmann\Type\TrueType;

class LaboratorioController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:' . Permiso::RESPONSABLE)->only(['index', 'create', 'update', 'destroy', 'show', 'edit']);
    }

    public function index(Request $request, $idEa)
    {
        $responsable = Auth::user();
        $ensayosAptitud = $responsable->responsablesEA->findOrFail($idEa);
        $laboratorios = InscripcionEA::with('inscripcion.laboratorio')
            ->where('id_ea', $idEa)
            ->get()
            ->pluck('inscripcion.laboratorio')
            ->unique('id')
            ->values();
        $paises = Pais::active()->get();
        $tipos = TipoLaboratorio::active()->get();
        $categorias = CategoriaLaboratorio::active()->get();
        $niveles = NivelLaboratorio::active()->get();
        return view('responsable.lab.index', compact('laboratorios', 'paises', 'tipos', 'categorias', 'niveles', 'idEa', 'ensayosAptitud'));
    }

    public function getData(Request $request, $idEa)
    {
        $responsable = Auth::user();
        $responsable->responsablesEA->findOrFail($idEa);

        Log::info('$idEa');
        Log::info($idEa);
        $query = InscripcionEA::with(['inscripcion', 'inscripcion.laboratorio.departamento', 'inscripcion.laboratorio.usuario'])
            ->where('id_ea', $idEa)
            ->whereHas('inscripcion', function ($q) {
                $q->whereYear('fecha_inscripcion', now());
                $q->Aprobado();
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

    public function showUploadCertificado($id)
    {
        $responsable = Auth::user();
        $ensayosAptitud = $responsable->responsablesEA->findOrFail($id);
        $idEA = $ensayosAptitud->id;
        $gestion = configuracion(Configuracion::REGISTRO_PONDERACIONES_CERTIFICADOS_GESTION) ?? now()->year;
        return view('responsable.upload_certificados', compact('ensayosAptitud', 'idEA', 'gestion'));
    }
    public function uploadCertificadoData(Request $request, $id)
    {
        $responsable = Auth::user();

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
                    'nombreLab' => $data[1] ?? null,
                    'desempeno' => $data[2] ?? null,
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
            ->whereHas('inscripcion', function ($q) {
                $q->whereYear('fecha_inscripcion', configuracion(Configuracion::REGISTRO_PONDERACIONES_CERTIFICADOS_GESTION) ?? now()->year)
                    ->Aprobado();
            })
            ->get();

        $configDirGen = configuracion(Configuracion::CARGO_DIRECTORA_GENERAL);
        $configCoordRed = configuracion(Configuracion::CARGO_COORDINADORA_RED);
        $configEvalExt = configuracion(Configuracion::CARGO_EVALUACION_EXTERNA);

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
                    'nombre_coordinador' => $configCoordRed->nombre,
                    'nombre_jefe' => $configEvalExt->nombre,
                    'nombre_director' => $configDirGen->nombre,
                    'firma_coordinador' => $configCoordRed->imagen,
                    'firma_jefe' => $configEvalExt->imagen,
                    'firma_director' => $configDirGen->imagen,
                    'nombre_laboratorio' => $lab->nombre_lab,
                    'cod_lab' => $lab->cod_lab,
                    'codigo_certificado' => null,
                    'tipo_certificado' => 1,
                    'status_certificado' => 0,
                    'publicado' => 0,
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

    public function getLaboratoriosDesempenoTemporal($idEa)
    {
        $responsable = Auth::user();
        $responsable->responsablesEA->findOrFail($idEa);

        $query = Inscripcion::with([
            'laboratorio',
            'certificado',
            'certificado.detalles' => function ($q) use ($idEa) {
                $q->where('temporal', true)
                    ->where('id_ea', $idEa);
            }
        ])
            ->where('gestion', configuracion(Configuracion::REGISTRO_PONDERACIONES_CERTIFICADOS_GESTION) ?? now()->year)
            ->Aprobado()
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
        if (!Gate::any([Permiso::RESPONSABLE])) {
            return redirect('/')->withErrors(['error' => 'No tienes permiso para realizar esta acción.']);
        }
        $responsable = Auth::user();
        $responsable->responsablesEA->findOrFail($idEa);

        $query = Inscripcion::with([
            'laboratorio',
            'certificado',
            'certificado.detalles' => function ($q) use ($idEa) {
                $q->where('temporal', true)
                    ->where('id_ea', $idEa);
            }
        ])
            ->where('gestion', configuracion(Configuracion::REGISTRO_PONDERACIONES_CERTIFICADOS_GESTION) ?? now()->year)
            ->Aprobado()
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
                    $certificado->publicado = 0;
                    $certificado->save();
                }
            }
        } catch (\Exception $e) {
            Log::error('Error al confirmar datos de certificados: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Ocurrió un error al confirmar los datos de los certificados. Por favor, inténtalo de nuevo.']);
        }
        return redirect()->back()->with('success', 'Datos de certificados confirmados correctamente.');
    }

    public function getLaboratoriosDesempenoConfirmados($idEa)
    {
        $responsable = Auth::user();
        $responsable->responsablesEA->findOrFail($idEa);
        $query = Inscripcion::with([
            'laboratorio',
            'certificado',
            'certificado.detalles' => function ($q) use ($idEa) {
                $q->where('temporal', false)
                    ->where('id_ea', $idEa);
            }
        ])
            ->where('gestion', configuracion(Configuracion::REGISTRO_PONDERACIONES_CERTIFICADOS_GESTION) ?? now()->year)
            ->Aprobado()
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
            ->addColumn('nombre_lab', fn($ins) => $ins->laboratorio->nombre_lab)
            ->addColumn('cod_lab', fn($ins) => $ins->laboratorio->cod_lab)
            ->addColumn('wapp_lab', fn($ins) => $ins->laboratorio->wapp_lab)
            ->addColumn('mail_lab', fn($ins) => $ins->laboratorio->mail_lab ?? '-')
            ->addColumn('desempeno', fn($ins) => $ins->certificado->detalles->first()?->calificacion_certificado ?? '')
            ->toJson();
    }
}
