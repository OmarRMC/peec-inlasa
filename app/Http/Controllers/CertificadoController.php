<?php

namespace App\Http\Controllers;

use App\Models\Certificado;
use App\Models\Configuracion;
use App\Models\Inscripcion;
use App\Models\Laboratorio;
use App\Models\Permiso;
use App\Models\PlantillaCertificado;
use Illuminate\Http\Request;
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
        return view('certificados.admin.index', compact('gestiones', 'plantillas'));
    }

    public function getDataCertificado(Request $request)
    {
        if (!Gate::any([Permiso::GESTION_CERTIFICADOS, Permiso::ADMIN])) {
            return redirect('/')->with('error', 'No tiene autorización para acceder a esta sección.');
        }

        $query = Inscripcion::with(['laboratorio', 'certificado.detalles', 'certificado.plantilla'])
            ->whereHas('certificado', function ($q2) {
                $q2->Publicado()->conDetalles();
            })
            ->aprobadoOrVencido()
            ->orderByDesc('gestion');

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
                    'laboratorio' => $lab->nombre_lab,
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
        if ($request->filled('estado')) {
            if ($request->estado == Certificado::STATUS_HABILITADO) {
                $rows = $rows->where('inhabilitado', false);
            }
            if ($request->estado == Certificado::STATUS_INHABILITADO) {
                $rows = $rows->where('inhabilitado', true);
            }
        }
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
            ->rawColumns(['desempeño', 'participacion', 'estado'])
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

        // Obtener todos los certificados del laboratorio para esa gestión
        $certificados = Certificado::whereHas('inscripcion', function ($q) use ($idLab, $gestion) {
            $q->where('id_lab', $idLab)
              ->where('gestion', $gestion)
              ->aprobadoOrVencido();
        })->get();

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
                $data = $lab->descargarCertificadoDesemp($gestion);
                break;

            case Certificado::TYPE_PARTICIPACION:
                $data = $lab->descargarCertificadoParticipacion($gestion);
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
