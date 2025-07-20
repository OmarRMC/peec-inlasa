<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use App\Models\CategoriaLaboratorio;
use App\Models\Inscripcion;
use App\Models\NivelLaboratorio;
use App\Models\Pais;
use App\Models\Permiso;
use App\Models\Programa;
use App\Models\TipoLaboratorio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class LabController extends Controller
{


    public function profile()
    {
        Gate::authorize(Permiso::LABORATORIO);
        $user = Auth::user();
        $laboratorio = $user->laboratorio;
        $backTo = false;
        return view('laboratorio.show', compact('laboratorio', 'backTo'));
    }


    public function editarProfile()
    {
        Gate::authorize(Permiso::LABORATORIO);
        $user = Auth::user();
        $laboratorio = $user->laboratorio;
        $backTo = false;
        return view('laboratorio.edit', [
            'laboratorio' => $laboratorio,
            'paises' => Pais::active()->get(),
            'niveles' => NivelLaboratorio::all(),
            'tipos' => TipoLaboratorio::all(),
            'departamentos' => $laboratorio->pais->departamentos()->orderBy('nombre_dep')->get(),
            'provincias' => $laboratorio->departamento->provincias()->orderBy('nombre_prov')->get(),
            'municipios' => $laboratorio->provincia->municipios()->orderBy('nombre_municipio')->get(),
            'categorias' => CategoriaLaboratorio::all(),
            'backTo' => $backTo
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize(Permiso::LABORATORIO);
        $user = Auth::user();
        $laboratorio = $user->laboratorio;
        $inscripciones = $laboratorio->inscripciones()->get();
        return view('laboratorio.inscripciones', [
            'paises' => [],
            'niveles' => [],
            'tipos' => [],
            'departamentos' => [],
            'provincias' => [],
            'municipios' => [],
            'categorias' => [],
            'gestiones' => ['2025', '2024', '2023'],
        ]);
    }

    public function getInscripcionData()
    {
        $lab = Auth::user()->laboratorio;
        $query = Inscripcion::with([
            'laboratorio.pais',
            'laboratorio.tipo',
            'laboratorio.categoria',
            'laboratorio.nivel',
            'detalleInscripciones'
        ])->where('id_lab', $lab->id);

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
            ->addColumn('acciones', function ($i) {
                return view('inscripcion_paquete.action-buttons', [
                    'showUrl' => route('lab.inscripcion.show', $i->id),
                ])->render();
            })
            ->rawColumns(['estado', 'acciones'])
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function labInscripcion()
    {

        $user = Auth::user();
        $laboratorio = $user->laboratorio;
        // $programas = $laboratorio->tipo->programas()->get();
        $programas  = Programa::active()->get();
        // $programas = Programa::whereHas('tipos', fn($q) => $q->where('id_tipo', $tipoLab))
        //     ->where('status', true)
        //     ->get();
        $redirectTo =  route('lab.ins.index');
        return view('inscripcion_paquete.create', compact('laboratorio', 'programas', 'redirectTo'));
    }


    public function labShowInscripcion($id)
    {
        $user = Auth::user();
        $laboratorio = $user->laboratorio;

        // Asegurarse que la inscripción pertenezca al laboratorio autenticado
        $inscripcion = $laboratorio->inscripciones()
            ->with(['laboratorio', 'detalleInscripciones', 'pagos', 'documentos', 'vigencia'])
            ->findOrFail($id);

        $programas = Programa::active()->get();
        $backTo = route('lab.ins.index');

        return view('inscripcion_paquete.show', compact('inscripcion', 'backTo'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('lab_tem.create', [
            'paises' => Pais::active()->get(),
            'niveles' => NivelLaboratorio::all(),
            'tipos' => TipoLaboratorio::all(),
            'departamentos' => [],
            'provincias' => [],
            'municipios' => [],
            'categorias' => CategoriaLaboratorio::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function registrar() {}
}
