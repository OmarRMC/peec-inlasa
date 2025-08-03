<?php

namespace App\Http\Controllers\responsable;

use App\Http\Controllers\Controller;
use App\Models\CategoriaLaboratorio;
use App\Models\InscripcionEA;
use App\Models\Laboratorio;
use App\Models\NivelLaboratorio;
use App\Models\Pais;
use App\Models\Permiso;
use App\Models\TipoLaboratorio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        // Obtener IDs de laboratorios inscritos en el EA
        $query = InscripcionEA::with(['inscripcion', 'inscripcion.laboratorio.departamento', 'inscripcion.laboratorio.usuario'])
            ->where('id_ea', $idEa);

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
}
