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
        $laboratorioIds = InscripcionEA::with('inscripcion')
            ->where('id_ea', $idEa)
            ->get()
            ->pluck('inscripcion.id_lab')
            ->values()
            ->toArray();

        Log::info('$laboratorioIds');
        Log::info($laboratorioIds);
        // Consultar solo los laboratorios filtrados por ID
        $query = Laboratorio::whereIn('id', $laboratorioIds)
            ->with(['pais', 'usuario', 'departamento', 'provincia', 'municipio', 'tipo', 'categoria', 'nivel']);

        // Aplicar filtros adicionales
        foreach (['pais', 'dep', 'prov', 'mun', 'tipo', 'categoria', 'nivel'] as $f) {
            if ($val = $request->get($f)) {
                $query->where("id_{$f}", $val);
            }
        }

        // Retornar respuesta para DataTables
        return datatables()
            ->of($query)
            ->addColumn('pais_nombre', fn($lab) => $lab->pais->nombre_pais)
            ->addColumn('departamento_nombre', fn($lab) => $lab->departamento->nombre_dep ?? '-')
            ->addColumn('provincia_nombre', fn($lab) => $lab->provincia->nombre_prov ?? '-')
            ->addColumn('municipio_nombre', fn($lab) => $lab->municipio->nombre_municipio ?? '-')
            ->addColumn('codigo', fn($lab) => $lab->usuario->username)
            ->addColumn('tipo_nombre', fn($lab) => $lab->tipo->nombre_tipo ?? '-')
            ->addColumn('categoria_nombre', fn($lab) => $lab->categoria->nombre_categoria ?? '-')
            ->addColumn('nivel_nombre', fn($lab) => $lab->nivel->nombre ?? '-')
            ->addColumn('email', fn($lab) => $lab->usuario->email ?? '-')
            ->addColumn('status_label', fn($lab) => $lab->status ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-danger">Inactivo</span>')
            ->addColumn('actions', function ($lab) {
                return view('responsable.lab.action-buttons', [
                    // 'showUrl' => route('laboratorio.show', $lab->id),
                    // 'editUrl' => route('laboratorio.edit', $lab->id),
                    // 'deleteUrl' => route('laboratorio.destroy', $lab->id),
                    // 'inscribirUrl' => route('inscripcion.create', $lab->id),
                    'nombre' => $lab->nombre_lab,
                    'id' => $lab->id,
                    'activo' => $lab->status,
                ])->render();
            })
            ->rawColumns(['status_label', 'actions'])
            ->toJson();
    }
}
