<?php

namespace App\Http\Controllers\Admin;

use App\Exports\InscripcionesExport;
use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use App\Models\Inscripcion;
use App\Models\Permiso;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    /**
     * Página principal del reporte: filtros + listado con opciones.
     */

    public function inscripciones(Request $request)
    {

        if (
            !Gate::any([Permiso::ADMIN, Permiso::GESTION_INSCRIPCIONES])
        ) {
            return redirect()->back()->with('error', 'No tienes permisos para realizar esta acción.');
        }
        $query = Inscripcion::query()
            ->aprobadoOrVencido()
            ->with(['detalleInscripciones', 'laboratorio', 'pagos', 'laboratorio.departamento']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id_lab', 'like', "%{$search}%")
                    ->orWhereHas('laboratorio', function ($q2) use ($search) {
                        $q2->where('nombre_lab', 'like', "%{$search}%")
                            ->orWhere('mail_lab', 'like', "%{$search}%")
                            ->orWhere('cod_lab', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        if ($request->filled('gestion')) {
            $query->where('gestion', $request->gestion);
        } else {
            $query->where('gestion', now()->year);
        }

        $gestiones = Inscripcion::rangoGestion([
            'status_inscripcion' => [Inscripcion::STATUS_APROBADO, Inscripcion::STATUS_VENCIDO],
        ]) ?? now()->year;

        $paginator = $query
            ->orderBy('created_at', 'desc')
            ->paginate(25)
            ->withQueryString();

        // 🔹 Llamada al nuevo método del modelo
        $agrupadas = Inscripcion::agruparPorLaboratorio(collect($paginator->items()));

        $paginator->setCollection($agrupadas);

        return view('reportes.inscripciones.index', [
            'inscripciones' => $paginator,
            'gestiones' => $gestiones,
        ]);
    }

    /**
     * Exportar a Excel usando Maatwebsite\Excel
     */
    public function exportInscripcionesExcel(Request $request)
    {
        if (
            !Gate::any([Permiso::ADMIN, Permiso::GESTION_INSCRIPCIONES])
        ) {
            return redirect()->back()->with('error', 'No tienes permisos para realizar esta acción.');
        }
        $filters = $request->only(['gestion', 'fecha_desde', 'fecha_hasta', 'search']);
        $format = $request->query('format', 'xlsx');
        $fileName = 'inscripciones_' . now()->format('Y-m-d_H:i:s') . '.' . $format;
        $export = new InscripcionesExport($request->all());
        $data = $export->collection();
        if ($format === 'json') {
            $jsonContent = $data->toJson(JSON_PRETTY_PRINT);

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

    public function reportePagos(Request $request)
    {
        if (!Gate::any([Permiso::ADMIN, Permiso::GESTION_PAGOS])) {
            return redirect()->back()->with('error', 'No tienes permisos para realizar esta acción.');
        }

        $gestion = $request->query('gestion', now()->year); // ejemplo: 2025

        $gestiones = Inscripcion::rangoGestion([
            'status_inscripcion' => [Inscripcion::STATUS_APROBADO, Inscripcion::STATUS_VENCIDO],
        ]) ?? now()->year;
        $inscripciones = Inscripcion::with([
            'laboratorio',
            'formulario',
            'pagos'
        ])
            ->where('gestion', $gestion)
            ->whereHas('pagos')
            ->orderBy('fecha_inscripcion', 'desc')
            ->paginate(25)
            ->through(function ($ins) {
                $totalPagado = $ins->pagos->sum('monto_pagado');

                return [
                    'model' => $ins,
                    'total_pagado' => $totalPagado,
                    'saldo' => $ins->costo_total - $totalPagado,
                    'cantidad_pagos' => $ins->pagos->count(),
                    'ultimo_pago' => $ins->pagos->max('fecha_pago'),
                ];
            });


        return view('reportes.pagos.index', compact('inscripciones', 'gestion', 'gestiones'));
    }
}
