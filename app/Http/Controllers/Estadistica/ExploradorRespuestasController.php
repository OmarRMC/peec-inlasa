<?php

namespace App\Http\Controllers\Estadistica;

use App\Exports\ExploradorFormularioExport;
use App\Http\Controllers\Controller;
use App\Models\Ciclo;
use App\Models\EnsayoAptitud;
use App\Models\FormularioEnsayo;
use App\Models\Parametro;
use App\Models\ParametroCampo;
use App\Models\Permiso;
use App\Services\Estadistica\ExploradorRespuestas;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;

class ExploradorRespuestasController extends Controller
{
    public function ensayo(Request $request, int $idEA)
    {
        $ensayo = $this->cargarEnsayoAutorizado($idEA);
        if (!$ensayo) {
            return redirect()->route('reportes.resultados.ensayos')
                ->with('error', 'Ensayo no encontrado o sin permisos.');
        }

        $ciclosTodos = $ensayo->ciclos()->orderByDesc('id')->get();
        if ($ciclosTodos->isEmpty()) {
            return redirect()->route('reportes.resultados.ensayos')
                ->with('error', 'El ensayo no tiene ciclos creados.');
        }

        // Gestiones disponibles (descendente)
        $gestiones = $ciclosTodos->pluck('gestion')->unique()->sortDesc()->values();

        // Determinar gestion: del request, o de la gestion del ciclo activo, o la mas reciente
        $cicloActivo = $ensayo->getCicloActivo();
        $gestion = $request->input('gestion')
            ?? $cicloActivo?->gestion
            ?? $gestiones->first();

        // Ciclos de esa gestion (filtro)
        $ciclos = $ciclosTodos->where('gestion', $gestion)->values();
        if ($ciclos->isEmpty()) {
            // Gestion sin ciclos: fallback a la mas reciente
            $gestion = $gestiones->first();
            $ciclos  = $ciclosTodos->where('gestion', $gestion)->values();
        }

        // Ciclo: del request, sino el activo si pertenece a la gestion, sino el primero
        $idCiclo = (int) $request->input('ciclo', 0);
        $ciclo = $idCiclo > 0
            ? $ciclos->firstWhere('id', $idCiclo)
            : (($cicloActivo && $cicloActivo->gestion == $gestion) ? $cicloActivo : $ciclos->first());

        if (!$ciclo) {
            $ciclo = $ciclos->first();
        }

        $formularios = $ensayo->formularios()->get();

        $backUrl = $this->capturarBackUrl(
            $request,
            "explorador.ensayo.{$idEA}.back",
            "explorador-respuestas/ensayos/{$idEA}",
            route('reportes.resultados.ensayos')
        );

        return view('explorador-respuestas.ensayo', compact(
            'ensayo', 'ciclos', 'ciclo', 'gestiones', 'gestion', 'formularios', 'backUrl'
        ));
    }

    public function formulario(Request $request, int $idEA, int $idCiclo, int $idFormulario)
    {
        $ensayo = $this->cargarEnsayoAutorizado($idEA);
        if (!$ensayo) {
            return redirect()->route('reportes.resultados.ensayos')
                ->with('error', 'Ensayo no encontrado o sin permisos.');
        }

        $ciclo = Ciclo::where('id_ensayo', $idEA)->where('id', $idCiclo)->first();
        if (!$ciclo) {
            return redirect()->route('reportes.resultados.ensayos')
                ->with('error', 'Ciclo no encontrado.');
        }

        $formulario = FormularioEnsayo::with('secciones.parametros.campos')->find($idFormulario);
        if (!$formulario) {
            return redirect()->route('reportes.resultados.ensayos')
                ->with('error', 'Formulario no encontrado.');
        }

        $parametros            = ExploradorRespuestas::resumenParametros($idFormulario, $idCiclo);
        $completitudParametros = ExploradorRespuestas::completitudParametros($idFormulario, $idCiclo);
        $ciclos                = Ciclo::where('id_ensayo', $idEA)
            ->where('gestion', $ciclo->gestion)
            ->orderByDesc('id')
            ->get();
        $formularios           = $ensayo->formularios;

        $nEnvios = (int) \App\Models\FormularioEnsayoResultado::where('id_formulario', $idFormulario)
            ->where('id_ciclo', $idCiclo)
            ->count();
        $nParametros = $parametros->count();
        $nCampos     = $parametros->sum('n_campos');
        $pctPromedio = $completitudParametros->isNotEmpty()
            ? round($completitudParametros->avg('pct_completitud'), 2)
            : 0;

        $backUrl = $this->capturarBackUrl(
            $request,
            "explorador.formulario.{$idEA}.{$idFormulario}.back",
            "/formularios/{$idFormulario}",
            route('explorador-respuestas.ensayo', $idEA)
        );

        return view('explorador-respuestas.formulario', compact(
            'ensayo', 'ciclo', 'ciclos', 'formulario', 'formularios',
            'parametros', 'completitudParametros', 'nEnvios', 'nParametros', 'nCampos', 'pctPromedio',
            'backUrl'
        ));
    }

    public function campo(Request $request, int $idEA, int $idCiclo, int $idCampo)
    {
        $ensayo = $this->cargarEnsayoAutorizado($idEA);
        if (!$ensayo) {
            return redirect()->route('reportes.resultados.ensayos')
                ->with('error', 'Ensayo no encontrado o sin permisos.');
        }

        $ciclo = Ciclo::where('id_ensayo', $idEA)->where('id', $idCiclo)->first();
        if (!$ciclo) {
            return redirect()->route('reportes.resultados.ensayos')
                ->with('error', 'Ciclo no encontrado.');
        }

        $campo = ParametroCampo::with('parametro.seccion.formulario', 'grupoSelector')->find($idCampo);
        if (!$campo) {
            return redirect()->route('reportes.resultados.ensayos')
                ->with('error', 'Campo no encontrado.');
        }

        $tipo = ExploradorRespuestas::tipoDescriptivo($campo, $idCiclo);

        $data = ['tipo' => $tipo];
        switch ($tipo) {
            case 'numerico':
                $data['descriptivo'] = ExploradorRespuestas::descriptivoNumerico($idCiclo, $idCampo);
                $data['histograma']  = ExploradorRespuestas::histogramaCampo($idCiclo, $idCampo, 20);
                $data['boxplot']     = ExploradorRespuestas::boxPlotCampo($idCiclo, $idCampo);
                $data['valoresLab']  = ExploradorRespuestas::valoresPorLabNumerico($idCiclo, $idCampo);
                break;
            case 'categorico':
                $data['frecuencias'] = ExploradorRespuestas::frecuenciasCategorico($idCiclo, $idCampo);
                break;
            case 'booleano':
                $data['booleano'] = ExploradorRespuestas::descriptivoBooleano($idCiclo, $idCampo);
                break;
            case 'fecha':
                $data['fechas'] = ExploradorRespuestas::distribucionFechas($idCiclo, $idCampo);
                break;
            case 'texto':
            default:
                $data['topValores'] = ExploradorRespuestas::topValoresTexto($idCiclo, $idCampo, 10);
                $data['distintos']  = ExploradorRespuestas::contarValoresDistintosTexto($idCiclo, $idCampo);
                break;
        }

        $ciclos = Ciclo::where('id_ensayo', $idEA)
            ->where('gestion', $ciclo->gestion)
            ->orderByDesc('id')
            ->get();
        $seccion    = $campo->parametro?->seccion;
        $formulario = $seccion?->formulario;
        $parametro  = $campo->parametro;

        // Fallback de Volver: parametro (si existe) → formulario → ensayo
        $fallback = $parametro
            ? route('explorador-respuestas.parametro', [$idEA, $idCiclo, $parametro->id])
            : ($formulario
                ? route('explorador-respuestas.formulario', [$idEA, $idCiclo, $formulario->id])
                : route('explorador-respuestas.ensayo', $idEA));

        $backUrl = $this->capturarBackUrl(
            $request,
            "explorador.campo.{$idEA}.{$idCampo}.back",
            "/campos/{$idCampo}",
            $fallback
        );

        return view('explorador-respuestas.campo', array_merge($data, [
            'ensayo'      => $ensayo,
            'ciclo'       => $ciclo,
            'ciclos'      => $ciclos,
            'campo'       => $campo,
            'parametro'   => $parametro,
            'seccion'     => $seccion,
            'formulario'  => $formulario,
            'backUrl'     => $backUrl,
        ]));
    }

    public function parametro(Request $request, int $idEA, int $idCiclo, int $idParametro)
    {
        $ensayo = $this->cargarEnsayoAutorizado($idEA);
        if (!$ensayo) {
            return redirect()->route('reportes.resultados.ensayos')
                ->with('error', 'Ensayo no encontrado o sin permisos.');
        }

        $ciclo = Ciclo::where('id_ensayo', $idEA)->where('id', $idCiclo)->first();
        if (!$ciclo) {
            return redirect()->route('reportes.resultados.ensayos')
                ->with('error', 'Ciclo no encontrado.');
        }

        $parametro = Parametro::with('seccion.formulario')->find($idParametro);
        if (!$parametro) {
            return redirect()->route('reportes.resultados.ensayos')
                ->with('error', 'Parámetro no encontrado.');
        }

        $campos      = ExploradorRespuestas::camposDelParametro($idParametro, $idCiclo);
        $valoresLab  = ExploradorRespuestas::valoresParametroPorLab($idCiclo, $idParametro);

        // Selectores (query params)
        $idCampoX        = $request->integer('campo_x') ?: null;
        $idCampoY        = $request->integer('campo_y') ?: null;
        $idCampoFiltro   = $request->integer('filtro_campo') ?: null;
        $idOpcionFiltro  = $request->integer('filtro_opcion') ?: null;

        $combinacion = ExploradorRespuestas::combinacionCampos(
            $idCiclo, $idCampoX, $idCampoY, $idCampoFiltro, $idOpcionFiltro
        );

        // Opciones del campo filtro (si esta seleccionado)
        $opcionesFiltro = $idCampoFiltro
            ? ExploradorRespuestas::opcionesDelCampo($idCampoFiltro)
            : collect();

        $ciclos     = Ciclo::where('id_ensayo', $idEA)
            ->where('gestion', $ciclo->gestion)
            ->orderByDesc('id')
            ->get();
        $seccion    = $parametro->seccion;
        $formulario = $seccion?->formulario;

        $fallback = $formulario
            ? route('explorador-respuestas.formulario', [$idEA, $idCiclo, $formulario->id])
            : route('explorador-respuestas.ensayo', $idEA);

        $backUrl = $this->capturarBackUrl(
            $request,
            "explorador.parametro.{$idEA}.{$idParametro}.back",
            "/parametros/{$idParametro}",
            $fallback
        );

        return view('explorador-respuestas.parametro', compact(
            'ensayo', 'ciclo', 'ciclos', 'parametro', 'seccion', 'formulario',
            'campos', 'valoresLab', 'combinacion',
            'idCampoX', 'idCampoY', 'idCampoFiltro', 'idOpcionFiltro', 'opcionesFiltro',
            'backUrl'
        ));
    }

    public function exportarParametro(Request $request, int $idEA, int $idCiclo, int $idParametro)
    {
        $ensayo = $this->cargarEnsayoAutorizado($idEA);
        if (!$ensayo) {
            return redirect()->route('reportes.resultados.ensayos')
                ->with('error', 'Ensayo no encontrado o sin permisos.');
        }

        $ciclo     = Ciclo::where('id_ensayo', $idEA)->findOrFail($idCiclo);
        $parametro = Parametro::with('seccion.formulario')->findOrFail($idParametro);

        $campos     = ExploradorRespuestas::camposDelParametro($idParametro, $idCiclo);
        $valoresLab = ExploradorRespuestas::valoresParametroPorLab($idCiclo, $idParametro);

        // Stats por cada campo del parametro
        $statsPorCampo = $campos->map(function ($c) use ($idCiclo) {
            $stats = match ($c->tipo_descriptivo) {
                'numerico'   => ExploradorRespuestas::descriptivoNumerico($idCiclo, $c->id_campo),
                'categorico' => ['frecuencias' => ExploradorRespuestas::frecuenciasCategorico($idCiclo, $c->id_campo)],
                'booleano'   => ExploradorRespuestas::descriptivoBooleano($idCiclo, $c->id_campo),
                'fecha'      => ['fechas' => ExploradorRespuestas::distribucionFechas($idCiclo, $c->id_campo)],
                default      => ['top' => ExploradorRespuestas::topValoresTexto($idCiclo, $c->id_campo, 20),
                                 'distintos' => ExploradorRespuestas::contarValoresDistintosTexto($idCiclo, $c->id_campo)],
            };
            return (object) ['campo' => $c, 'stats' => $stats];
        });

        $pdf = Pdf::loadView('explorador-respuestas.exports.parametro-pdf', [
            'ensayo'        => $ensayo,
            'ciclo'         => $ciclo,
            'parametro'     => $parametro,
            'seccion'       => $parametro->seccion,
            'formulario'    => $parametro->seccion?->formulario,
            'campos'        => $campos,
            'valoresLab'    => $valoresLab,
            'statsPorCampo' => $statsPorCampo,
            'generado_en'   => now(),
        ])->setPaper('a4', 'landscape');

        $filename = sprintf('explorador-parametro-%d-ciclo-%d.pdf', $idParametro, $idCiclo);
        return $pdf->download($filename);
    }

    public function exportarFormulario(Request $request, int $idEA, int $idCiclo, int $idFormulario)
    {
        $ensayo = $this->cargarEnsayoAutorizado($idEA);
        if (!$ensayo) {
            return redirect()->route('reportes.resultados.ensayos')
                ->with('error', 'Ensayo no encontrado o sin permisos.');
        }

        $ciclo      = Ciclo::where('id_ensayo', $idEA)->findOrFail($idCiclo);
        $formulario = FormularioEnsayo::findOrFail($idFormulario);

        $filename = sprintf(
            'explorador-form-%d-ciclo-%d-%s.xlsx',
            $idFormulario, $idCiclo, now()->format('Ymd-His')
        );

        return Excel::download(
            new ExploradorFormularioExport($ensayo, $ciclo, $formulario),
            $filename
        );
    }

    public function exportarCampo(Request $request, int $idEA, int $idCiclo, int $idCampo)
    {
        $ensayo = $this->cargarEnsayoAutorizado($idEA);
        if (!$ensayo) {
            return redirect()->route('reportes.resultados.ensayos')
                ->with('error', 'Ensayo no encontrado o sin permisos.');
        }

        $ciclo = Ciclo::where('id_ensayo', $idEA)->findOrFail($idCiclo);
        $campo = ParametroCampo::with('parametro.seccion.formulario', 'grupoSelector')->findOrFail($idCampo);

        $tipo = ExploradorRespuestas::tipoDescriptivo($campo, $idCiclo);

        $data = ['tipo' => $tipo, 'ensayo' => $ensayo, 'ciclo' => $ciclo, 'campo' => $campo,
                 'parametro' => $campo->parametro, 'seccion' => $campo->parametro?->seccion,
                 'formulario' => $campo->parametro?->seccion?->formulario,
                 'generado_en' => now()];

        switch ($tipo) {
            case 'numerico':
                $data['descriptivo'] = ExploradorRespuestas::descriptivoNumerico($idCiclo, $idCampo);
                $data['histograma']  = ExploradorRespuestas::histogramaCampo($idCiclo, $idCampo, 20);
                $data['boxplot']     = ExploradorRespuestas::boxPlotCampo($idCiclo, $idCampo);
                $data['valoresLab']  = ExploradorRespuestas::valoresPorLabNumerico($idCiclo, $idCampo);
                break;
            case 'categorico':
                $data['frecuencias'] = ExploradorRespuestas::frecuenciasCategorico($idCiclo, $idCampo);
                break;
            case 'booleano':
                $data['booleano'] = ExploradorRespuestas::descriptivoBooleano($idCiclo, $idCampo);
                break;
            case 'fecha':
                $data['fechas'] = ExploradorRespuestas::distribucionFechas($idCiclo, $idCampo);
                break;
            case 'texto':
            default:
                $data['topValores'] = ExploradorRespuestas::topValoresTexto($idCiclo, $idCampo, 20);
                $data['distintos']  = ExploradorRespuestas::contarValoresDistintosTexto($idCiclo, $idCampo);
                break;
        }

        $pdf = Pdf::loadView('explorador-respuestas.exports.campo-pdf', $data)
            ->setPaper('a4', 'portrait');

        $filename = sprintf('explorador-campo-%d-ciclo-%d.pdf', $idCampo, $idCiclo);
        return $pdf->download($filename);
    }

    /**
     * Guarda el referer en sesion si proviene de una pagina distinta a la actual
     * (el segmento $segmentoActual es el fragmento de URL que identifica a la
     * vista propia, como "/parametros/{id}", para no sobrescribir cuando se
     * cambia un selector dentro de la misma vista).
     */
    private function capturarBackUrl(Request $request, string $sessionKey, string $segmentoActual, string $fallback): string
    {
        $referer = $request->headers->get('referer');
        if ($referer && !str_contains($referer, $segmentoActual)) {
            session()->put($sessionKey, $referer);
        }
        return session($sessionKey, $fallback);
    }

    /**
     * Carga el ensayo verificando autorizacion por rol:
     * - JEFE_PEEC y ADMIN: acceso a cualquier ensayo
     * - RESPONSABLE: solo ensayos a su cargo
     */
    private function cargarEnsayoAutorizado(int $idEA): ?EnsayoAptitud
    {
        if (Gate::allows(Permiso::JEFE_PEEC) || Gate::allows(Permiso::ADMIN)) {
            return EnsayoAptitud::with('paquete')->find($idEA);
        }

        if (Gate::allows(Permiso::RESPONSABLE)) {
            return Auth::user()->responsablesEA()->with('paquete')->find($idEA);
        }

        return null;
    }

}
