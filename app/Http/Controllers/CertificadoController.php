<?php

namespace App\Http\Controllers;

use App\Models\Certificado;
use App\Models\Configuracion;
use App\Models\Inscripcion;
use App\Models\Laboratorio;
use App\Models\Permiso;
use App\Models\PlantillaCertificado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class CertificadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $gestiones = Inscripcion::rangoGestion([
            'status_inscripcion' => [Inscripcion::STATUS_APROBADO, Inscripcion::STATUS_VENCIDO],
        ]);
        $plantillas = PlantillaCertificado::orderBy('nombre')
            ->get(['id', 'nombre']);
        $puedeEditar = Gate::allows(Permiso::EDITAR_CERTIFICADOS);
        return view('certificados.admin.index', compact('gestiones', 'plantillas', 'puedeEditar'));
    }

    public function getDataCertificado(Request $request)
    {
        if (!Gate::any([Permiso::GESTION_CERTIFICADOS, Permiso::ADMIN])) {
            return redirect('/')->with('error', 'No tiene autorización para acceder a esta sección.');
        }

        // publicado: '1' = publicados (por defecto), '0' = no publicados, 'todos' = ambos
        $filtroPublicado = $request->input('publicado', '1');
        $publicados = fn($q) => $q->Publicado()->conDetalles();
        $noPublicados = fn($q) => $q->noPublicado()->whereHas('detalles', fn($d) => $d->where('temporal', false));
        $query = Inscripcion::with(['laboratorio', 'certificado.detalles', 'certificado.plantilla'])
            ->whereHas('certificado', function ($q2) use ($filtroPublicado, $publicados, $noPublicados) {
                if ($filtroPublicado === 'todos') {
                    $q2->where(fn($q) => $q->where($publicados)->orWhere($noPublicados));
                } elseif ($filtroPublicado === '0') {
                    $noPublicados($q2);
                } else {
                    $publicados($q2);
                }
            })
            ->aprobadoOrVencido()
            ->orderByDesc('gestion')
            ->orderBy('id');

        if ($request->filled('gestion')) {
            $query->where('gestion', $request->gestion);
        }
        $inscripciones = $query->get();
        $data = [];

        foreach ($inscripciones as $inscripcion) {
            $lab = $inscripcion->laboratorio;
            $codLab = $lab->cod_lab;
            $deudor = $inscripcion->esDeudor();

            $tieneDes = $inscripcion->certificado
                ->detalles
                ->contains(fn($detalle) => !is_null($detalle->calificacion_certificado));

            $plantillaId = $inscripcion->certificado->plantilla_certificado_id;
            $plantillaNombre = $inscripcion->certificado->plantilla?->nombre;

            if (isset($data[$inscripcion->gestion][$codLab])) {
                $data[$inscripcion->gestion][$codLab]->tiene_certificado_desempeno =
                    $data[$inscripcion->gestion][$codLab]->tiene_certificado_desempeno || $tieneDes;
                $data[$inscripcion->gestion][$codLab]->inhabilitado =
                    $data[$inscripcion->gestion][$codLab]->inhabilitado || $deudor;
                if (!$data[$inscripcion->gestion][$codLab]->plantilla_id && $plantillaId) {
                    $data[$inscripcion->gestion][$codLab]->plantilla_id = $plantillaId;
                    $data[$inscripcion->gestion][$codLab]->plantilla_nombre = $plantillaNombre;
                }
            } else {
                $data[$inscripcion->gestion][$codLab] = (object) [
                    'gestion' => $inscripcion->gestion,
                    'laboratorio' => $inscripcion->certificado->nombre_laboratorio ?: $lab->nombre_lab,
                    'nombre_lab_actual' => $lab->nombre_lab,
                    'publicado' => (bool) $inscripcion->certificado->publicado,
                    'idLab' => $lab->id,
                    'cod_lab' => $codLab,
                    'tiene_certificado_desempeno' => $tieneDes,
                    'inhabilitado' => $deudor,
                    'plantilla_id' => $plantillaId,
                    'plantilla_nombre' => $plantillaNombre,
                ];
            }
        }

        $rows = collect();
        foreach ($data as $gestion => $labs) {
            foreach ($labs as $codLab => $info) {
                $rows->push($info);
            }
        }
        $rows = $rows->sortBy([
            ['gestion', 'desc'],
            ['laboratorio', 'asc'],
            ['cod_lab', 'asc'],
        ])->values();
        if ($request->filled('estado')) {
            if ($request->estado == Certificado::STATUS_HABILITADO) {
                $rows = $rows->where('inhabilitado', false);
            }
            if ($request->estado == Certificado::STATUS_INHABILITADO) {
                $rows = $rows->where('inhabilitado', true);
            }
        }
        $puedeEditar = Gate::allows(Permiso::EDITAR_CERTIFICADOS);
        return datatables()
            ->of($rows)
            ->addColumn('laboratorio', fn($row) => $row->laboratorio)
            ->addColumn('codigo_lab', fn($row) => $row->cod_lab)
            ->addColumn('gestion', fn($row) => $row->gestion)
            ->addColumn('desempeño', fn($row) => $row->tiene_certificado_desempeno ? Certificado::certificadoDesepRawHabilitado(route('admin.certificado.descargar', ['idLaboratorio' => $row->idLab, 'gestion' => $row->gestion, 'type' => Certificado::TYPE_DESEMP])) : Certificado::certificadoDesepRawDeshabilitado())
            ->addColumn('participacion', fn($row) => Certificado::certificadoParticipacionRaw(route('admin.certificado.descargar', ['idLaboratorio' => $row->idLab, 'gestion' => $row->gestion, 'type' => Certificado::TYPE_PARTICIPACION])))
            ->addColumn('estado', fn($row) => Certificado::estado($row->inhabilitado))
            ->addColumn('plantilla_id', fn($row) => $row->plantilla_id)
            ->addColumn('plantilla_nombre', fn($row) => $row->plantilla_nombre)
            ->addColumn('nombre_lab_actual', fn($row) => $row->nombre_lab_actual)
            ->addColumn('publicado', fn($row) => $row->publicado)
            ->addColumn('acciones', fn($row) => $puedeEditar ? Certificado::botonEditarRaw($row) : '')
            ->rawColumns(['desempeño', 'participacion', 'estado', 'acciones'])
            ->toJson();
    }

    public function updatePlantilla(Request $request)
    {
        if (!Gate::any([Permiso::GESTION_CERTIFICADOS, Permiso::ADMIN])) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $request->validate([
            'id_laboratorio' => 'required|integer|exists:laboratorio,id',
            'gestion' => 'required|string',
            'plantilla_id' => 'nullable|integer|exists:plantillas_certificados,id',
        ]);

        $idLab = $request->id_laboratorio;
        $gestion = $request->gestion;
        $plantillaId = $request->plantilla_id;

        $certificados = $this->certificadosDeLab($idLab, $gestion);

        if ($certificados->isEmpty()) {
            return response()->json(['error' => 'No se encontraron certificados'], 404);
        }

        // Actualizar plantilla en todos los certificados
        foreach ($certificados as $certificado) {
            $certificado->plantilla_certificado_id = $plantillaId;
            $certificado->save();
        }

        $plantillaNombre = null;
        if ($plantillaId) {
            $plantilla = PlantillaCertificado::find($plantillaId);
            $plantillaNombre = $plantilla?->nombre;
        }

        return response()->json([
            'success' => true,
            'message' => 'Plantilla actualizada correctamente',
            'plantilla_nombre' => $plantillaNombre,
            'updated_count' => $certificados->count(),
        ]);
    }

    public function despublicar(Request $request)
    {
        if (!Gate::allows(Permiso::EDITAR_CERTIFICADOS)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        $data = $this->validarLabGestion($request);
        $todos = $this->certificadosDeLab($data['id_laboratorio'], $data['gestion']);
        if ($todos->isEmpty()) {
            return response()->json(['error' => 'No se encontraron certificados'], 404);
        }
        $certificados = $todos->where('publicado', true);
        if ($certificados->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'El certificado ya estaba despublicado',
                'publicado' => false,
                'updated_count' => 0,
            ]);
        }

        Certificado::whereIn('id', $certificados->pluck('id'))->update(['publicado' => 0]);

        return response()->json([
            'success' => true,
            'message' => 'Certificado despublicado correctamente',
            'publicado' => false,
            'updated_count' => $certificados->count(),
        ]);
    }

    public function publicarLaboratorio(Request $request)
    {
        if (!Gate::allows(Permiso::EDITAR_CERTIFICADOS)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        $data = $this->validarLabGestion($request);
        $todos = $this->certificadosDeLab($data['id_laboratorio'], $data['gestion']);
        if ($todos->isEmpty()) {
            return response()->json(['error' => 'No se encontraron certificados'], 404);
        }
        if ($todos->where('publicado', false)->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'El certificado ya estaba publicado',
                'publicado' => true,
                'updated_count' => 0,
            ]);
        }
        $ids = Certificado::whereIn('id', $todos->pluck('id'))
            ->noPublicado()
            ->whereHas('detalles', fn($q) => $q->where('temporal', false))
            ->pluck('id');

        if ($ids->isEmpty()) {
            return response()->json(['error' => 'El certificado tiene desempeños en revisión; confírmelos antes de publicar'], 422);
        }

        Certificado::whereIn('id', $ids)->update(['publicado' => 1]);
        Log::info('certificado.publicar_laboratorio', [
            'user_id' => Auth::id(),
            'id_laboratorio' => $data['id_laboratorio'],
            'gestion' => $data['gestion'],
            'certificados' => $ids->all(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Certificado publicado correctamente',
            'publicado' => true,
            'updated_count' => $ids->count(),
        ]);
    }

    public function updateNombre(Request $request)
    {
        if (!Gate::allows(Permiso::EDITAR_CERTIFICADOS)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        $data = $this->validarLabGestion($request, [
            'nombre_laboratorio' => 'required|string|max:400',
        ]);
        $certificados = $this->certificadosDeLab($data['id_laboratorio'], $data['gestion']);

        if ($certificados->isEmpty()) {
            return response()->json(['error' => 'No se encontraron certificados'], 404);
        }
        if ($certificados->contains('publicado', true)) {
            return response()->json(['error' => 'Despublique el certificado antes de editarlo'], 422);
        }

        $nombre = trim($data['nombre_laboratorio']);
        Log::info('certificado.update_nombre', [
            'user_id' => Auth::id(),
            'id_laboratorio' => $data['id_laboratorio'],
            'gestion' => $data['gestion'],
            'antes' => $certificados->pluck('nombre_laboratorio', 'id')->all(),
            'despues' => $nombre,
        ]);
        Certificado::whereIn('id', $certificados->pluck('id'))->update(['nombre_laboratorio' => $nombre]);

        return response()->json([
            'success' => true,
            'message' => 'Nombre del certificado actualizado correctamente',
            'nombre_laboratorio' => $nombre,
        ]);
    }

    private function validarLabGestion(Request $request, array $extra = []): array
    {
        return $request->validate(array_merge([
            'id_laboratorio' => 'required|integer|exists:laboratorio,id',
            'gestion' => 'required|string',
        ], $extra));
    }

    /**
     * Certificados de un laboratorio en una gestión (puede haber varias inscripciones).
     */
    private function certificadosDeLab($idLab, $gestion)
    {
        return Certificado::whereHas('inscripcion', function ($q) use ($idLab, $gestion) {
            $q->where('id_lab', $idLab)
                ->where('gestion', $gestion)
                ->aprobadoOrVencido();
        })->get();
    }

    public function descargarCertificado($idLaboratorio, $gestion, $type)
    {
        if (!Gate::any([Permiso::GESTION_CERTIFICADOS, Permiso::ADMIN])) {
            return redirect('/')->with('error', '⚠️ No tiene autorización para acceder a esta sección.');
        }
        $lab = Laboratorio::find($idLaboratorio);
        if (!$lab) {
            return redirect()->back()->with('error', '❌ El laboratorio no existe o no se encuentra registrado.');
        }
        switch ($type) {
            case Certificado::TYPE_DESEMP:
                $data = $lab->descargarCertificadoDesemp($gestion, true);
                break;

            case Certificado::TYPE_PARTICIPACION:
                $data = $lab->descargarCertificadoParticipacion($gestion, true);
                break;

            default:
                return redirect()->back()->with('error', '❌ Tipo de certificado no válido.');
        }
        if (!$data) {
            return redirect()->back()->with('error', '⚠️ No se pudo generar el certificado. Verifique los datos.');
        }
        return $data;
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


    public function certificadoDesempenoParticionListLabs()
    {
        if (!Gate::any([Permiso::ADMIN, Permiso::GESTION_CERTIFICADOS])) {
            return redirect('/')->with('error', 'No tienes permisos para realizar esta acción.');
        }
        $gestiones = Inscripcion::rangoGestion([
            'status_inscripcion' => [Inscripcion::STATUS_APROBADO, Inscripcion::STATUS_VENCIDO],
        ]);
        return view('certificados.admin.des_participacion', compact('gestiones'));
    }
    public function publicarCertificado(Request $request, $gestion)
    {
        if (!Gate::any([Permiso::ADMIN, Permiso::GESTION_CERTIFICADOS])) {
            return redirect('/')->with('error', 'No tienes permisos para realizar esta acción.');
        }
        $total = Certificado::where('gestion_certificado', $gestion)
            ->noPublicado()
            ->whereHas('inscripcion', fn($q) => $q->aprobadoOrVencido())
            ->whereHas('detalles', fn($q) => $q->where('temporal', false))
            ->update(['publicado' => 1]);

        return back()->with('success', "Se publicaron {$total} certificados de la gestión {$gestion}.");
    }
}
