<?php

namespace App\Http\Controllers\responsable;

use App\Http\Controllers\Controller;
use App\Models\EnsayoAptitud;
use App\Models\Permiso;
use App\Services\Estadistica\CalculadorClasico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class DashboardEnsayoController extends Controller
{
    public function index(Request $request, int $idEA)
    {
        if (!Gate::any([Permiso::ADMIN, Permiso::RESPONSABLE, Permiso::JEFE_PEEC])) {
            return redirect('/')->with('error', 'Acceso restringido.');
        }

        // Carga del ensayo con autorizacion por rol
        if (Gate::allows(Permiso::JEFE_PEEC) || Gate::allows(Permiso::ADMIN)) {
            $ensayo = EnsayoAptitud::with(['paquete', 'ciclos', 'formularios.secciones.parametros.campos'])->find($idEA);
        } else {
            $ensayo = Auth::user()
                ->responsablesEA()
                ->with(['paquete', 'ciclos', 'formularios.secciones.parametros.campos'])
                ->find($idEA);
        }

        if (!$ensayo) {
            return redirect()->route('reportes.resultados.ensayos')
                ->with('error', 'Ensayo no encontrado o sin permisos.');
        }

        // Modo de calculo de z-scores: clasico (default) o robusto (ISO 13528)
        $modo = $request->input('modo') === 'robusto' ? 'robusto' : 'clasico';

        // Listado de ciclos para el selector (ordenados desc)
        $ciclos = $ensayo->ciclos->sortByDesc('id')->values();

        // Ciclo a mostrar: request->ciclo > ciclo activo > ultimo ciclo del ensayo
        $idCiclo = (int) $request->input('ciclo', 0);
        $cicloSeleccionado = $idCiclo > 0
            ? $ciclos->firstWhere('id', $idCiclo)
            : ($ensayo->getCicloActivo() ?? $ciclos->first());

        if (!$cicloSeleccionado) {
            return view('responsable.dashboard.ensayo', [
                'ensayo'            => $ensayo,
                'ciclos'            => $ciclos,
                'cicloSeleccionado' => null,
                'modo'              => $modo,
                'kpis'              => null,
                'campos'            => collect(),
            ]);
        }

        // KPIs del ciclo
        $tasa = CalculadorClasico::tasaRespuesta($cicloSeleccionado->id);
        $pct  = CalculadorClasico::porcentajeSatisfactorios($cicloSeleccionado->id);

        // Campos evaluables del ensayo, en orden de secciones/parametros
        $camposEvaluables = collect();
        foreach ($ensayo->formularios as $formulario) {
            foreach ($formulario->secciones as $seccion) {
                foreach ($seccion->parametros as $parametro) {
                    foreach ($parametro->campos as $campo) {
                        if (CalculadorClasico::esEvaluable($campo)) {
                            $camposEvaluables->push((object) [
                                'campo'     => $campo,
                                'parametro' => $parametro,
                                'seccion'   => $seccion,
                                'formulario'=> $formulario,
                            ]);
                        }
                    }
                }
            }
        }

        // Por cada campo evaluable: consenso + z-scores + histograma + box-plot + tendencia
        $statsCampos = $camposEvaluables->map(function ($item) use ($cicloSeleccionado, $modo) {
            $idCampo = $item->campo->id;
            $cons      = CalculadorClasico::consenso($cicloSeleccionado->id, $idCampo);
            $consRob   = CalculadorClasico::consensoRobusto($cicloSeleccionado->id, $idCampo);
            $zs        = $modo === 'robusto'
                ? CalculadorClasico::zScoreRobustoLabs($cicloSeleccionado->id, $idCampo)
                : CalculadorClasico::zScoreLabs($cicloSeleccionado->id, $idCampo);
            $hist      = CalculadorClasico::histograma($cicloSeleccionado->id, $idCampo, 12);
            $box       = CalculadorClasico::boxPlot($cicloSeleccionado->id, $idCampo);
            $tend      = CalculadorClasico::tendenciaConsenso($idCampo, 6);

            return (object) [
                'id_campo'        => $idCampo,
                'label'           => $item->campo->etiqueta,
                'parametro'       => $item->parametro->nombre,
                'seccion'         => $item->seccion->nombre,
                'consenso'        => $cons,
                'consenso_robusto'=> $consRob,
                'zscores'         => $zs,
                'outliers'        => $zs->filter(fn ($z) => $z->desempeno === CalculadorClasico::DESEMPENO_NO_SATISFACTORIO)->values(),
                'histograma'      => $hist,
                'boxplot'         => $box,
                'tendencia'       => $tend,
            ];
        });

        return view('responsable.dashboard.ensayo', [
            'ensayo'            => $ensayo,
            'ciclos'            => $ciclos,
            'cicloSeleccionado' => $cicloSeleccionado,
            'modo'              => $modo,
            'kpis'              => [
                'tasa' => $tasa,
                'pct'  => $pct,
            ],
            'campos'            => $statsCampos,
        ]);
    }
}
