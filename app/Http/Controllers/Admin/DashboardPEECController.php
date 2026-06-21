<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Permiso;
use App\Services\Estadistica\CalculadorClasico;
use App\Services\Estadistica\ComportamientoCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class DashboardPEECController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::any([Permiso::ADMIN, Permiso::JEFE_PEEC])) {
            return redirect('/')->with('error', 'Acceso restringido.');
        }

        $gestion = (int) $request->input('gestion', now()->year);

        // Gestiones disponibles (para el selector)
        $gestiones = DB::table('ciclos')
            ->select('gestion')
            ->distinct()
            ->orderByDesc('gestion')
            ->pluck('gestion');

        // KPIs de la gestion
        $ensayosActivos = DB::table('ciclos')
            ->where('gestion', $gestion)
            ->distinct()
            ->count('id_ensayo');

        $labsActivos = DB::table('formulario_ensayos_resultados')
            ->where('gestion', $gestion)
            ->distinct()
            ->count('id_laboratorio');

        $totalResultados = DB::table('formulario_ensayos_resultados')
            ->where('gestion', $gestion)
            ->count();

        // KPI "% Aptitud global" (z-score). El donut se removio; solo se usa el porcentaje.
        $pctGestion = CalculadorClasico::porcentajeSatisfactoriosGestion($gestion);
        $tasasPorEnsayo = CalculadorClasico::tasasPorEnsayoGestion($gestion);
        $heatmap = CalculadorClasico::heatmapEnsayosCiclos($gestion);
        $labsProblematicos = CalculadorClasico::labsProblematicos($gestion, 6, 2);

        // Comportamiento de ensayos / paquetes (demanda y evolucion, sin z-score)
        $inscritosPorEnsayo  = ComportamientoCalculator::inscritosPorEnsayo($gestion);
        $inscritosPorPaquete = ComportamientoCalculator::inscritosPorPaquete($gestion);
        $evolucion           = ComportamientoCalculator::evolucionInscripciones();

        return view('admin.dashboard.peec', [
            'gestion'             => $gestion,
            'gestiones'           => $gestiones,
            'kpis'                => [
                'ensayos_activos'  => $ensayosActivos,
                'labs_activos'     => $labsActivos,
                'total_resultados' => $totalResultados,
                'pct_aptitud'      => $pctGestion['pct_satisfactorios'],
            ],
            'tasasPorEnsayo'      => $tasasPorEnsayo,
            'heatmap'             => $heatmap,
            'labsProblematicos'   => $labsProblematicos,
            'inscritosPorEnsayo'  => $inscritosPorEnsayo,
            'inscritosPorPaquete' => $inscritosPorPaquete,
            'evolucion'           => $evolucion,
        ]);
    }
}
