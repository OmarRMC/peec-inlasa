<?php

namespace App\Http\Controllers;

use App\Exports\ResultadosEvaluacionLabExport;
use App\Models\EnsayoAptitud;
use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;

class ReporteResultadosEvaluacioneController extends Controller
{

    public function exportEnsayoResultados(Request $request, $idEA, $ciclo)
    {

        if (!Gate::any([Permiso::ADMIN, Permiso::RESPONSABLE])) {
            return redirect('/')->with('error', 'Acceso restringido. No dispones de los permisos requeridos para realizar esta operación.');
        }
        $gestion = $request->query('gestion', now()->year);
        $ensayo = EnsayoAptitud::with(['formularios.resultados' => function ($q) use ($ciclo, $gestion) {
            $q->where('id_ciclo', $ciclo)->where('estado', 1)->where('gestion', $gestion);
        }, 'formularios.secciones.parametros.campos'])->findOrFail($idEA);

        $formularios = $ensayo->formularios;
        $format = $request->query('format', 'xlsx');
        $fileName = 'ResultadosEvaluacion_' . now()->format('Y-m-d_H:i:s') . '.' . $format;
        $export = new ResultadosEvaluacionLabExport($formularios);
        if ($format === 'json') {
            $jsonContent = $ensayo->toJson(JSON_PRETTY_PRINT);
            return response()->streamDownload(function () use ($jsonContent) {
                echo $jsonContent;
            }, $fileName, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
            ]);
        }
        switch ($format) {
            case 'csv':
                return Excel::download($export, $fileName, \Maatwebsite\Excel\Excel::CSV);
            case 'xlsx':
            default:
                return Excel::download($export, $fileName);
        }
    }
}
