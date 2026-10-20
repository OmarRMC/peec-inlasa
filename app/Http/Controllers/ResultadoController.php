<?php

namespace App\Http\Controllers;

use App\Exports\ResultadosEvaluacionLabExport;
use App\Exports\ResultadosExport;
use App\Models\Formulario;
use App\Models\FormularioEnsayo;
use Maatwebsite\Excel\Facades\Excel;

class ResultadoController extends Controller
{
    public function export($id)
    {
        $formularios = FormularioEnsayo::find($id);
        if (!$formularios) {
            return redirect()->back()->with('error', '❌ No se encontró el formulario solicitado. Verifique el identificador e intente nuevamente.');
        }
        $formularios = collect([$formularios]);
        try {
            $export = new ResultadosEvaluacionLabExport($formularios, true);
            $fileName = 'Resultados_Evaluacion_' . now()->format('Y_m_d_His') . '.xlsx';
            return Excel::download($export, $fileName);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', '⚠️ Ocurrió un error al generar la exportación: ' . $e->getMessage());
        }
    }
}
