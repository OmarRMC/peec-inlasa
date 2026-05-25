<?php

namespace App\Services\Estadistica;

use App\Models\FormularioEnsayo;
use App\Models\Parametro;
use App\Models\ParametroCampo;
use App\Models\RespuestaCampo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Explorador descriptivo de respuestas.
 *
 * Distinto del CalculadorClasico: aqui no se evalua ni se clasifica.
 * Solo describimos el comportamiento de los datos enviados por los labs.
 */
class ExploradorRespuestas
{
    /** Umbral para considerar un campo text como numerico (>= 90% parseables). */
    public const UMBRAL_TEXTO_NUMERICO = 0.9;

    /** Si los valores tienen <= este numero de unicos, el histograma se renderiza
     *  como "categorico" (un bin por valor unico). Evita gaps feos con datos discretos. */
    public const UMBRAL_VALORES_DISCRETOS = 25;

    private static array $cacheTipoDescriptivo = [];

    /**
     * Determina el tipo descriptivo efectivo del campo en un ciclo.
     * Para campos text/textarea, ejecuta heuristica sobre los valores reales.
     *
     * @return 'numerico'|'categorico'|'booleano'|'fecha'|'texto'
     */
    public static function tipoDescriptivo(ParametroCampo $campo, int $idCiclo): string
    {
        $cacheKey = "{$campo->id}:{$idCiclo}";
        if (isset(self::$cacheTipoDescriptivo[$cacheKey])) {
            return self::$cacheTipoDescriptivo[$cacheKey];
        }

        $tipo = match ($campo->tipo) {
            'number'                       => 'numerico',
            'checkbox'                     => 'booleano',
            'date'                         => 'fecha',
            'select', 'radio', 'datalist'  => 'categorico',
            'text', 'textarea'             => self::detectarTextoNumerico($campo->id, $idCiclo)
                                                ? 'numerico'
                                                : 'texto',
            default                        => 'texto',
        };

        return self::$cacheTipoDescriptivo[$cacheKey] = $tipo;
    }

    /**
     * Heuristica: si >= 90% de los valor_texto del campo son numericos en este ciclo,
     * el campo se trata como numerico aunque su tipo de catalogo sea text.
     */
    private static function detectarTextoNumerico(int $idCampo, int $idCiclo): bool
    {
        $row = DB::table('respuestas_campo as rc')
            ->join('formulario_ensayos_resultados as r', 'r.id', '=', 'rc.id_formulario_resultado')
            ->where('r.id_ciclo', $idCiclo)
            ->where('rc.id_parametro_campo', $idCampo)
            ->whereNotNull('rc.valor_texto')
            ->selectRaw('COUNT(*) as total, SUM(rc.valor_texto REGEXP "^-?[0-9]+(\\\\.[0-9]+)?$") as numericos')
            ->first();

        if (!$row || (int) $row->total === 0) {
            return false;
        }

        return ((int) $row->numericos / (int) $row->total) >= self::UMBRAL_TEXTO_NUMERICO;
    }

    /**
     * Resumen del formulario completo en un ciclo.
     * Devuelve, por cada campo, su tipo descriptivo, completitud y snippet de stat.
     *
     * @return Collection<int, object>
     */
    public static function resumenFormulario(int $idFormulario, int $idCiclo): Collection
    {
        $formulario = FormularioEnsayo::with('secciones.parametros.campos')->find($idFormulario);
        if (!$formulario) {
            return collect();
        }

        $nEnvios = self::contarEnviosFormulario($idFormulario, $idCiclo);

        $items = collect();
        foreach ($formulario->secciones as $seccion) {
            foreach ($seccion->parametros as $parametro) {
                foreach ($parametro->campos as $campo) {
                    $tipo = self::tipoDescriptivo($campo, $idCiclo);
                    $items->push((object) [
                        'id_campo'         => $campo->id,
                        'label'            => $campo->etiqueta,
                        'tipo_catalogo'    => $campo->tipo,
                        'tipo_descriptivo' => $tipo,
                        'seccion'          => $seccion->nombre,
                        'parametro'        => $parametro->nombre,
                        'snippet'          => self::snippetStat($campo, $idCiclo, $tipo),
                        'n_envios'         => $nEnvios,
                    ]);
                }
            }
        }

        return $items;
    }

    /**
     * Resumen textual del stat principal segun el tipo descriptivo.
     */
    private static function snippetStat(ParametroCampo $campo, int $idCiclo, string $tipo): string
    {
        switch ($tipo) {
            case 'numerico':
                $d = self::descriptivoNumerico($idCiclo, $campo->id);
                if ($d['n'] === 0) return 'sin datos';
                return "n={$d['n']}, media=" . self::fmt($d['media']) . ", σ=" . self::fmt($d['desv']);

            case 'categorico':
                $freq = self::frecuenciasCategorico($idCiclo, $campo->id);
                if ($freq->isEmpty()) return 'sin datos';
                $moda = $freq->first();
                return "moda: '{$moda->etiqueta}' ({$moda->count}/{$freq->sum('count')})";

            case 'booleano':
                $b = self::descriptivoBooleano($idCiclo, $campo->id);
                if ($b['n'] === 0) return 'sin datos';
                return "{$b['pct_marcado']}% marcado ({$b['marcado']}/{$b['n']})";

            case 'fecha':
                $f = self::distribucionFechas($idCiclo, $campo->id);
                if ($f->isEmpty()) return 'sin datos';
                return "{$f->sum('count')} respuestas en " . $f->count() . " periodo(s)";

            case 'texto':
            default:
                $top = self::topValoresTexto($idCiclo, $campo->id, 1);
                if ($top->isEmpty()) return 'sin datos';
                $distintos = self::contarValoresDistintosTexto($idCiclo, $campo->id);
                return "{$distintos} valores distintos";
        }
    }

    /**
     * Stats descriptivos numericos para campos number o text-numerico.
     */
    public static function descriptivoNumerico(int $idCiclo, int $idCampo): array
    {
        $valores = self::valoresNumericosDelCampo($idCiclo, $idCampo);
        $nEnvios = self::contarEnviosPorCampo($idCiclo, $idCampo);
        $n = $valores->count();

        if ($n === 0) {
            return [
                'n' => 0, 'n_total_envios' => $nEnvios, 'pct_completitud' => 0.0,
                'media' => null, 'mediana' => null, 'desv' => null, 'cv_pct' => null,
                'minimo' => null, 'maximo' => null, 'q1' => null, 'q3' => null, 'iqr' => null,
            ];
        }

        $arr   = $valores->sort()->values()->all();
        $media = array_sum($arr) / $n;
        $desv  = $n > 1 ? self::desvMuestral($arr, $media) : 0.0;
        $cv    = ($media != 0.0) ? abs($desv / $media) * 100 : null;
        $q1    = self::percentilOrdenado($arr, 0.25);
        $q3    = self::percentilOrdenado($arr, 0.75);

        return [
            'n'               => $n,
            'n_total_envios'  => $nEnvios,
            'pct_completitud' => $nEnvios > 0 ? round($n * 100 / $nEnvios, 2) : null,
            'media'           => round($media, 6),
            'mediana'         => round(self::medianaOrdenada($arr), 6),
            'desv'            => round($desv, 6),
            'cv_pct'          => $cv !== null ? round($cv, 4) : null,
            'minimo'          => round($arr[0], 6),
            'maximo'          => round($arr[$n - 1], 6),
            'q1'              => round($q1, 6),
            'q3'              => round($q3, 6),
            'iqr'             => round($q3 - $q1, 6),
        ];
    }

    /**
     * Frecuencia de cada opcion en selects/radios/datalist.
     * Para selects/radio agrupa por id_opcion_seleccionada (o valor_texto si no hay id).
     * Para datalist (texto libre) agrupa por valor_texto.
     */
    public static function frecuenciasCategorico(int $idCiclo, int $idCampo): Collection
    {
        $rows = DB::table('respuestas_campo as rc')
            ->join('formulario_ensayos_resultados as r', 'r.id', '=', 'rc.id_formulario_resultado')
            ->where('r.id_ciclo', $idCiclo)
            ->where('rc.id_parametro_campo', $idCampo)
            ->where(function ($q) {
                $q->whereNotNull('rc.id_opcion_seleccionada')
                  ->orWhereNotNull('rc.valor_texto_opcion')
                  ->orWhereNotNull('rc.valor_texto');
            })
            ->select([
                'rc.id_opcion_seleccionada',
                DB::raw('COALESCE(rc.valor_texto_opcion, rc.valor_texto) as etiqueta'),
                DB::raw('COUNT(*) as cantidad'),
            ])
            ->groupBy('rc.id_opcion_seleccionada', 'etiqueta')
            ->orderByDesc('cantidad')
            ->get();

        $total = $rows->sum('cantidad');

        return $rows->map(fn ($r) => (object) [
            'id_opcion' => $r->id_opcion_seleccionada !== null ? (int) $r->id_opcion_seleccionada : null,
            'etiqueta'  => $r->etiqueta ?? '(vacio)',
            'count'     => (int) $r->cantidad,
            'pct'       => $total > 0 ? round($r->cantidad * 100 / $total, 2) : 0,
        ]);
    }

    /**
     * Para checkbox: cuantos marcaron vs no marcaron.
     */
    public static function descriptivoBooleano(int $idCiclo, int $idCampo): array
    {
        $row = DB::table('respuestas_campo as rc')
            ->join('formulario_ensayos_resultados as r', 'r.id', '=', 'rc.id_formulario_resultado')
            ->where('r.id_ciclo', $idCiclo)
            ->where('rc.id_parametro_campo', $idCampo)
            ->whereNotNull('rc.valor_booleano')
            ->selectRaw('COUNT(*) as n, SUM(rc.valor_booleano = 1) as marcado')
            ->first();

        $n = $row ? (int) $row->n : 0;
        $marcado = $row ? (int) $row->marcado : 0;

        return [
            'n'           => $n,
            'marcado'     => $marcado,
            'no_marcado'  => $n - $marcado,
            'pct_marcado' => $n > 0 ? round($marcado * 100 / $n, 2) : null,
        ];
    }

    /**
     * Top-N valores agrupados de un campo de texto libre.
     */
    public static function topValoresTexto(int $idCiclo, int $idCampo, int $topN = 10): Collection
    {
        $rows = DB::table('respuestas_campo as rc')
            ->join('formulario_ensayos_resultados as r', 'r.id', '=', 'rc.id_formulario_resultado')
            ->where('r.id_ciclo', $idCiclo)
            ->where('rc.id_parametro_campo', $idCampo)
            ->whereNotNull('rc.valor_texto')
            ->where('rc.valor_texto', '!=', '')
            ->select([
                'rc.valor_texto',
                DB::raw('COUNT(*) as cantidad'),
                DB::raw('AVG(CHAR_LENGTH(rc.valor_texto)) as longitud_promedio'),
            ])
            ->groupBy('rc.valor_texto')
            ->orderByDesc('cantidad')
            ->limit($topN)
            ->get();

        $total = DB::table('respuestas_campo as rc')
            ->join('formulario_ensayos_resultados as r', 'r.id', '=', 'rc.id_formulario_resultado')
            ->where('r.id_ciclo', $idCiclo)
            ->where('rc.id_parametro_campo', $idCampo)
            ->whereNotNull('rc.valor_texto')
            ->where('rc.valor_texto', '!=', '')
            ->count();

        return $rows->map(fn ($r) => (object) [
            'valor'             => $r->valor_texto,
            'count'             => (int) $r->cantidad,
            'pct'               => $total > 0 ? round($r->cantidad * 100 / $total, 2) : 0,
            'longitud_promedio' => round((float) $r->longitud_promedio, 2),
        ]);
    }

    /**
     * Cantidad de valores distintos en un campo texto.
     */
    public static function contarValoresDistintosTexto(int $idCiclo, int $idCampo): int
    {
        return (int) DB::table('respuestas_campo as rc')
            ->join('formulario_ensayos_resultados as r', 'r.id', '=', 'rc.id_formulario_resultado')
            ->where('r.id_ciclo', $idCiclo)
            ->where('rc.id_parametro_campo', $idCampo)
            ->whereNotNull('rc.valor_texto')
            ->where('rc.valor_texto', '!=', '')
            ->distinct()
            ->count('rc.valor_texto');
    }

    /**
     * Distribucion temporal de fechas: agrupa por YYYY-MM.
     */
    public static function distribucionFechas(int $idCiclo, int $idCampo): Collection
    {
        return DB::table('respuestas_campo as rc')
            ->join('formulario_ensayos_resultados as r', 'r.id', '=', 'rc.id_formulario_resultado')
            ->where('r.id_ciclo', $idCiclo)
            ->where('rc.id_parametro_campo', $idCampo)
            ->whereNotNull('rc.valor_fecha')
            ->select([
                DB::raw("DATE_FORMAT(rc.valor_fecha, '%Y-%m') as periodo"),
                DB::raw('COUNT(*) as cantidad'),
            ])
            ->groupBy('periodo')
            ->orderBy('periodo')
            ->get()
            ->map(fn ($r) => (object) [
                'periodo' => $r->periodo,
                'count'   => (int) $r->cantidad,
            ]);
    }

    /**
     * Histograma del campo numerico. Soporta number y text-numerico.
     * Si la cantidad de valores unicos es <= UMBRAL_VALORES_DISCRETOS, se
     * renderiza como "categorico" (un bin por valor unico, sin gaps).
     *
     * @return array{bins:array, media:?float, desv:?float, modo:string}
     */
    public static function histogramaCampo(int $idCiclo, int $idCampo, int $bins = 20): array
    {
        $valores = self::valoresNumericosDelCampo($idCiclo, $idCampo)->all();
        return self::construirHistograma($valores, $bins);
    }

    /**
     * Box-plot del campo numerico. Soporta number y text-numerico.
     */
    public static function boxPlotCampo(int $idCiclo, int $idCampo): array
    {
        $valores = self::valoresNumericosDelCampo($idCiclo, $idCampo)->sort()->values()->all();
        $n = count($valores);

        if ($n === 0) {
            return ['q1' => null, 'mediana' => null, 'q3' => null, 'min' => null, 'max' => null,
                    'outliers' => [], 'whisker_min' => null, 'whisker_max' => null];
        }

        $q1      = self::percentilOrdenado($valores, 0.25);
        $mediana = self::medianaOrdenada($valores);
        $q3      = self::percentilOrdenado($valores, 0.75);
        $iqr     = $q3 - $q1;

        $whiskerMin = $q1 - 1.5 * $iqr;
        $whiskerMax = $q3 + 1.5 * $iqr;

        $dentro = array_filter($valores, fn ($v) => $v >= $whiskerMin && $v <= $whiskerMax);
        $outliers = array_values(array_filter($valores, fn ($v) => $v < $whiskerMin || $v > $whiskerMax));

        return [
            'q1'           => round($q1, 6),
            'mediana'      => round($mediana, 6),
            'q3'           => round($q3, 6),
            'min'          => round($valores[0], 6),
            'max'          => round($valores[$n - 1], 6),
            'outliers'     => array_map(fn ($v) => round($v, 6), $outliers),
            'whisker_min'  => round($dentro ? min($dentro) : $q1, 6),
            'whisker_max'  => round($dentro ? max($dentro) : $q3, 6),
        ];
    }

    /**
     * Completitud agregada a nivel de parametro: % promedio de campos del parametro
     * que fueron respondidos a traves de todos los envios.
     *
     * Formula:
     *   esperado = n_envios x n_campos_del_parametro
     *   reales   = total de respuestas no nulas en campos del parametro
     *   pct      = reales / esperado x 100
     *
     * @return Collection<int, object{id_parametro, nombre, seccion, n_campos, n_envios, n_respuestas_esperadas, n_respuestas, pct_completitud}>
     */
    public static function completitudParametros(int $idFormulario, int $idCiclo): Collection
    {
        $formulario = FormularioEnsayo::with('secciones.parametros.campos')->find($idFormulario);
        if (!$formulario) return collect();

        $nEnvios = self::contarEnviosFormulario($idFormulario, $idCiclo);

        $items = collect();
        foreach ($formulario->secciones as $seccion) {
            foreach ($seccion->parametros as $parametro) {
                $nCampos = $parametro->campos->count();
                if ($nCampos === 0) continue;

                $idsCampos = $parametro->campos->pluck('id')->all();

                $nRespuestas = (int) DB::table('respuestas_campo as rc')
                    ->join('formulario_ensayos_resultados as r', 'r.id', '=', 'rc.id_formulario_resultado')
                    ->where('r.id_ciclo', $idCiclo)
                    ->whereIn('rc.id_parametro_campo', $idsCampos)
                    ->where(function ($q) {
                        $q->whereNotNull('rc.valor_texto')
                          ->orWhereNotNull('rc.valor_numero')
                          ->orWhereNotNull('rc.valor_fecha')
                          ->orWhereNotNull('rc.valor_booleano')
                          ->orWhereNotNull('rc.id_opcion_seleccionada');
                    })
                    ->count();

                $esperadas = $nEnvios * $nCampos;
                $items->push((object) [
                    'id_parametro'           => $parametro->id,
                    'nombre'                 => $parametro->nombre,
                    'seccion'                => $seccion->nombre,
                    'n_campos'               => $nCampos,
                    'n_envios'               => $nEnvios,
                    'n_respuestas_esperadas' => $esperadas,
                    'n_respuestas'           => $nRespuestas,
                    'pct_completitud'        => $esperadas > 0 ? round($nRespuestas * 100 / $esperadas, 2) : 0,
                ]);
            }
        }
        return $items;
    }

    /**
     * Completitud del formulario: cuantos labs respondieron cada campo
     * vs cuantos enviaron el formulario.
     */
    public static function completitudFormulario(int $idFormulario, int $idCiclo): Collection
    {
        $formulario = FormularioEnsayo::with('secciones.parametros.campos')->find($idFormulario);
        if (!$formulario) {
            return collect();
        }

        $nEnvios = self::contarEnviosFormulario($idFormulario, $idCiclo);

        $items = collect();
        foreach ($formulario->secciones as $seccion) {
            foreach ($seccion->parametros as $parametro) {
                foreach ($parametro->campos as $campo) {
                    $n = self::contarRespuestasNoNulasCampo($idCiclo, $campo->id);
                    $items->push((object) [
                        'id_campo'        => $campo->id,
                        'label'           => $campo->etiqueta,
                        'n_respuestas'    => $n,
                        'n_envios'        => $nEnvios,
                        'pct_completitud' => $nEnvios > 0 ? round($n * 100 / $nEnvios, 2) : 0,
                    ]);
                }
            }
        }

        return $items;
    }

    /**
     * Pares (lab, valor) para drill-down numerico.
     *
     * @return Collection<int, object{id_laboratorio, nombre_lab, cod_lab, valor}>
     */
    public static function valoresPorLabNumerico(int $idCiclo, int $idCampo): Collection
    {
        // Detectar si usamos valor_numero o valor_texto
        $usarTexto = !DB::table('respuestas_campo as rc')
            ->join('formulario_ensayos_resultados as r', 'r.id', '=', 'rc.id_formulario_resultado')
            ->where('r.id_ciclo', $idCiclo)
            ->where('rc.id_parametro_campo', $idCampo)
            ->whereNotNull('rc.valor_numero')
            ->exists();

        $q = DB::table('respuestas_campo as rc')
            ->join('formulario_ensayos_resultados as r', 'r.id', '=', 'rc.id_formulario_resultado')
            ->where('r.id_ciclo', $idCiclo)
            ->where('rc.id_parametro_campo', $idCampo);

        if ($usarTexto) {
            $q->whereRaw('rc.valor_texto REGEXP "^-?[0-9]+(\\\\.[0-9]+)?$"')
              ->select(['r.id_laboratorio', 'r.nombre_lab', 'r.cod_lab', 'rc.valor_texto as valor_raw']);
        } else {
            $q->whereNotNull('rc.valor_numero')
              ->select(['r.id_laboratorio', 'r.nombre_lab', 'r.cod_lab', 'rc.valor_numero as valor_raw']);
        }

        return $q->orderBy('rc.valor_numero')
            ->get()
            ->map(fn ($r) => (object) [
                'id_laboratorio' => (int) $r->id_laboratorio,
                'nombre_lab'     => $r->nombre_lab,
                'cod_lab'        => $r->cod_lab,
                'valor'          => (float) $r->valor_raw,
            ]);
    }

    // --- Helpers privados ---

    /**
     * Devuelve la lista de valores numericos del campo en el ciclo, sea desde
     * valor_numero (tipo=number) o desde valor_texto (text-numerico detectado).
     */
    private static function valoresNumericosDelCampo(int $idCiclo, int $idCampo): Collection
    {
        // Primero intenta valor_numero
        $numericos = DB::table('respuestas_campo as rc')
            ->join('formulario_ensayos_resultados as r', 'r.id', '=', 'rc.id_formulario_resultado')
            ->where('r.id_ciclo', $idCiclo)
            ->where('rc.id_parametro_campo', $idCampo)
            ->whereNotNull('rc.valor_numero')
            ->pluck('rc.valor_numero')
            ->map(fn ($v) => (float) $v);

        if ($numericos->isNotEmpty()) {
            return $numericos;
        }

        // Fallback: valor_texto que sean numericos
        return DB::table('respuestas_campo as rc')
            ->join('formulario_ensayos_resultados as r', 'r.id', '=', 'rc.id_formulario_resultado')
            ->where('r.id_ciclo', $idCiclo)
            ->where('rc.id_parametro_campo', $idCampo)
            ->whereRaw('rc.valor_texto REGEXP "^-?[0-9]+(\\\\.[0-9]+)?$"')
            ->pluck('rc.valor_texto')
            ->map(fn ($v) => (float) $v);
    }

    /**
     * Cantidad de envios totales del formulario en el ciclo.
     */
    private static function contarEnviosFormulario(int $idFormulario, int $idCiclo): int
    {
        return (int) DB::table('formulario_ensayos_resultados')
            ->where('id_formulario', $idFormulario)
            ->where('id_ciclo', $idCiclo)
            ->count();
    }

    /**
     * Cantidad de envios del formulario al que pertenece el campo en el ciclo.
     * Usa el formulario del campo via parametro -> seccion -> formulario.
     */
    private static function contarEnviosPorCampo(int $idCiclo, int $idCampo): int
    {
        $idFormulario = DB::table('parametro_campos as pc')
            ->join('parametros as p', 'p.id', '=', 'pc.id_parametro')
            ->join('secciones as s', 's.id', '=', 'p.id_seccion')
            ->where('pc.id', $idCampo)
            ->value('s.id_formulario');

        if (!$idFormulario) return 0;
        return self::contarEnviosFormulario((int) $idFormulario, $idCiclo);
    }

    /**
     * Resumen agrupado por parametro del formulario.
     * Cada parametro lista sus campos con tipo descriptivo y snippet.
     *
     * @return Collection<int, object{id_parametro, nombre, seccion, campos: Collection}>
     */
    public static function resumenParametros(int $idFormulario, int $idCiclo): Collection
    {
        $formulario = FormularioEnsayo::with('secciones.parametros.campos')->find($idFormulario);
        if (!$formulario) return collect();

        $items = collect();
        foreach ($formulario->secciones as $seccion) {
            foreach ($seccion->parametros as $parametro) {
                $campos = collect();
                foreach ($parametro->campos as $campo) {
                    $tipo = self::tipoDescriptivo($campo, $idCiclo);
                    $campos->push((object) [
                        'id_campo'         => $campo->id,
                        'label'            => $campo->etiqueta,
                        'tipo_catalogo'    => $campo->tipo,
                        'tipo_descriptivo' => $tipo,
                        'snippet'          => self::snippetStat($campo, $idCiclo, $tipo),
                    ]);
                }
                $items->push((object) [
                    'id_parametro' => $parametro->id,
                    'nombre'       => $parametro->nombre,
                    'seccion'      => $seccion->nombre,
                    'n_campos'     => $campos->count(),
                    'campos'       => $campos,
                ]);
            }
        }
        return $items;
    }

    /**
     * Listado de campos de un parametro con su tipo descriptivo en el ciclo.
     */
    public static function camposDelParametro(int $idParametro, int $idCiclo): Collection
    {
        $campos = ParametroCampo::where('id_parametro', $idParametro)
            ->orderBy('posicion')
            ->get();

        return $campos->map(function ($c) use ($idCiclo) {
            $tipo = self::tipoDescriptivo($c, $idCiclo);
            return (object) [
                'id_campo'         => $c->id,
                'label'            => $c->etiqueta,
                'tipo_catalogo'    => $c->tipo,
                'tipo_descriptivo' => $tipo,
                'snippet'          => self::snippetStat($c, $idCiclo, $tipo),
                'modelo'           => $c,
            ];
        });
    }

    /**
     * Tabla cruzada: una fila por lab, una columna por campo del parametro.
     * Cada celda es el valor "humano" del campo (numero, texto, fecha o etiqueta de opcion).
     *
     * @return Collection<int, object{id_laboratorio, nombre_lab, cod_lab, valores: array<int, ?string>}>
     */
    public static function valoresParametroPorLab(int $idCiclo, int $idParametro): Collection
    {
        $idsCampos = ParametroCampo::where('id_parametro', $idParametro)->pluck('id');
        if ($idsCampos->isEmpty()) return collect();

        $rows = DB::table('respuestas_campo as rc')
            ->join('formulario_ensayos_resultados as r', 'r.id', '=', 'rc.id_formulario_resultado')
            ->where('r.id_ciclo', $idCiclo)
            ->whereIn('rc.id_parametro_campo', $idsCampos)
            ->select([
                'r.id_laboratorio',
                'r.nombre_lab',
                'r.cod_lab',
                'rc.id_parametro_campo',
                'rc.tipo_campo',
                'rc.valor_texto',
                'rc.valor_numero',
                'rc.valor_fecha',
                'rc.valor_booleano',
                'rc.valor_texto_opcion',
            ])
            ->orderBy('r.id_laboratorio')
            ->get();

        $grouped = $rows->groupBy('id_laboratorio');

        return $grouped->map(function ($filas) use ($idsCampos) {
            $primera = $filas->first();
            $valoresPorCampo = [];
            foreach ($idsCampos as $idCampo) {
                $fila = $filas->firstWhere('id_parametro_campo', $idCampo);
                $valoresPorCampo[$idCampo] = $fila ? self::valorHumano($fila) : null;
            }
            return (object) [
                'id_laboratorio' => (int) $primera->id_laboratorio,
                'nombre_lab'     => $primera->nombre_lab,
                'cod_lab'        => $primera->cod_lab,
                'valores'        => $valoresPorCampo,
            ];
        })->values();
    }

    /**
     * Convierte una fila de respuestas_campo al valor humano del tipo correspondiente.
     */
    private static function valorHumano(object $fila): ?string
    {
        return match ($fila->tipo_campo) {
            'number'                       => $fila->valor_numero !== null ? (string) (float) $fila->valor_numero : null,
            'date'                         => $fila->valor_fecha,
            'checkbox'                     => $fila->valor_booleano !== null ? ((int) $fila->valor_booleano ? 'Sí' : 'No') : null,
            'select', 'radio', 'datalist'  => $fila->valor_texto_opcion ?? $fila->valor_texto,
            default                        => $fila->valor_texto,
        };
    }

    /**
     * Combinacion de campos: dispatcher segun los selectores.
     *
     * Modos:
     * - solo $idCampoX: univariado del campo X
     * - X + Y ambos numericos: scatter
     * - X num + Y categorico (o viceversa): box-plot agrupado
     * - X cat + Y cat: tabla cruzada
     * - $idCampoFiltro + $idOpcionFiltro: aplica filtro a X
     *
     * @return array{modo:string, data:mixed, x_label:?string, y_label:?string}
     */
    public static function combinacionCampos(
        int $idCiclo,
        ?int $idCampoX,
        ?int $idCampoY = null,
        ?int $idCampoFiltro = null,
        ?int $idOpcionFiltro = null,
    ): array {
        if (!$idCampoX && !$idCampoY) {
            return ['modo' => 'vacio', 'data' => null, 'x_label' => null, 'y_label' => null];
        }

        // Normalizar: si solo viene Y, tratarlo como X
        if (!$idCampoX && $idCampoY) {
            $idCampoX = $idCampoY;
            $idCampoY = null;
        }

        $campoX = ParametroCampo::find($idCampoX);
        $campoY = $idCampoY ? ParametroCampo::find($idCampoY) : null;
        if (!$campoX) {
            return ['modo' => 'vacio', 'data' => null, 'x_label' => null, 'y_label' => null];
        }

        $tipoX = self::tipoDescriptivo($campoX, $idCiclo);
        $tipoY = $campoY ? self::tipoDescriptivo($campoY, $idCiclo) : null;

        // Caso univariado (solo X)
        if (!$campoY) {
            return [
                'modo'    => 'univariado',
                'data'    => self::univariadoConFiltro($idCiclo, $campoX, $tipoX, $idCampoFiltro, $idOpcionFiltro),
                'x_label' => $campoX->etiqueta,
                'y_label' => null,
                'tipo_x'  => $tipoX,
            ];
        }

        // Bivariado
        $esNumX = $tipoX === 'numerico';
        $esNumY = $tipoY === 'numerico';
        $esCatX = in_array($tipoX, ['categorico', 'booleano']);
        $esCatY = in_array($tipoY, ['categorico', 'booleano']);

        if ($esNumX && $esNumY) {
            return [
                'modo'    => 'scatter',
                'data'    => self::scatterDosCampos($idCiclo, $campoX->id, $campoY->id),
                'x_label' => $campoX->etiqueta,
                'y_label' => $campoY->etiqueta,
                'tipo_x'  => $tipoX,
                'tipo_y'  => $tipoY,
            ];
        }

        if ($esNumX && $esCatY) {
            return [
                'modo'    => 'box_agrupado',
                'data'    => self::boxPlotAgrupado($idCiclo, $campoX->id, $campoY->id),
                'x_label' => $campoX->etiqueta,
                'y_label' => $campoY->etiqueta,
                'tipo_x'  => $tipoX,
                'tipo_y'  => $tipoY,
            ];
        }

        if ($esCatX && $esNumY) {
            return [
                'modo'    => 'box_agrupado',
                'data'    => self::boxPlotAgrupado($idCiclo, $campoY->id, $campoX->id),
                'x_label' => $campoY->etiqueta,
                'y_label' => $campoX->etiqueta,
                'tipo_x'  => $tipoY,
                'tipo_y'  => $tipoX,
            ];
        }

        if ($esCatX && $esCatY) {
            return [
                'modo'    => 'tabla_cruzada',
                'data'    => self::tablaCruzada($idCiclo, $campoX->id, $campoY->id),
                'x_label' => $campoX->etiqueta,
                'y_label' => $campoY->etiqueta,
                'tipo_x'  => $tipoX,
                'tipo_y'  => $tipoY,
            ];
        }

        return [
            'modo'    => 'no_soportado',
            'data'    => null,
            'x_label' => $campoX->etiqueta,
            'y_label' => $campoY->etiqueta,
        ];
    }

    /**
     * Univariado con filtro opcional por una opcion categorica de otro campo.
     */
    private static function univariadoConFiltro(
        int $idCiclo,
        ParametroCampo $campoX,
        string $tipoX,
        ?int $idCampoFiltro,
        ?int $idOpcionFiltro,
    ): array {
        // Si hay filtro: obtener los id_formulario_resultado donde el campo filtro tiene esa opcion
        $resultadosFiltrados = null;
        if ($idCampoFiltro && $idOpcionFiltro) {
            $resultadosFiltrados = DB::table('respuestas_campo')
                ->where('id_parametro_campo', $idCampoFiltro)
                ->where('id_opcion_seleccionada', $idOpcionFiltro)
                ->pluck('id_formulario_resultado')
                ->toArray();

            if (empty($resultadosFiltrados)) {
                return ['vacio_por_filtro' => true, 'tipo' => $tipoX];
            }
        }

        $base = [
            'tipo'             => $tipoX,
            'vacio_por_filtro' => false,
        ];

        switch ($tipoX) {
            case 'numerico':
                $valores = self::valoresNumericosFiltrados($idCiclo, $campoX->id, $resultadosFiltrados);
                $base['n']          = $valores->count();
                $base['histograma'] = $valores->isNotEmpty()
                    ? self::histogramaDeValores($valores->all())
                    : ['bins' => []];
                $base['stats']      = $valores->isNotEmpty()
                    ? self::statsBasicos($valores->sort()->values()->all())
                    : null;
                break;

            case 'categorico':
                $q = DB::table('respuestas_campo as rc')
                    ->join('formulario_ensayos_resultados as r', 'r.id', '=', 'rc.id_formulario_resultado')
                    ->where('r.id_ciclo', $idCiclo)
                    ->where('rc.id_parametro_campo', $campoX->id);
                if ($resultadosFiltrados !== null) {
                    $q->whereIn('rc.id_formulario_resultado', $resultadosFiltrados);
                }
                $rows = $q->select([
                        'rc.id_opcion_seleccionada',
                        DB::raw('COALESCE(rc.valor_texto_opcion, rc.valor_texto) as etiqueta'),
                        DB::raw('COUNT(*) as cantidad'),
                    ])
                    ->groupBy('rc.id_opcion_seleccionada', 'etiqueta')
                    ->orderByDesc('cantidad')
                    ->get();
                $total = $rows->sum('cantidad');
                $base['frecuencias'] = $rows->map(fn ($r) => (object) [
                    'etiqueta' => $r->etiqueta ?? '(vacio)',
                    'count'    => (int) $r->cantidad,
                    'pct'      => $total > 0 ? round($r->cantidad * 100 / $total, 2) : 0,
                ]);
                break;

            case 'booleano':
                $base['booleano'] = self::descriptivoBooleano($idCiclo, $campoX->id);
                break;

            case 'fecha':
                $base['fechas'] = self::distribucionFechas($idCiclo, $campoX->id);
                break;

            default:
                $base['top'] = self::topValoresTexto($idCiclo, $campoX->id, 10);
        }

        return $base;
    }

    /**
     * Scatter plot: pares (x, y) por lab donde ambos campos tienen valor.
     */
    private static function scatterDosCampos(int $idCiclo, int $idCampoX, int $idCampoY): Collection
    {
        $valoresX = self::valoresNumericosPorResultado($idCiclo, $idCampoX);
        $valoresY = self::valoresNumericosPorResultado($idCiclo, $idCampoY);

        // Intersect by id_formulario_resultado
        $puntos = collect();
        foreach ($valoresX as $idResultado => $infoX) {
            if (!isset($valoresY[$idResultado])) continue;
            $infoY = $valoresY[$idResultado];
            $puntos->push((object) [
                'id_laboratorio' => $infoX->id_laboratorio,
                'nombre_lab'     => $infoX->nombre_lab,
                'cod_lab'        => $infoX->cod_lab,
                'x'              => $infoX->valor,
                'y'              => $infoY->valor,
            ]);
        }
        return $puntos->values();
    }

    /**
     * Box-plot del campo numerico agrupado por categorias del campo categorico.
     *
     * @return Collection<int, object{categoria, n, q1, mediana, q3, min, max, whisker_min, whisker_max, outliers}>
     */
    private static function boxPlotAgrupado(int $idCiclo, int $idCampoNum, int $idCampoCat): Collection
    {
        // Para cada lab/resultado obtenemos (numero, categoria)
        $rows = DB::table('respuestas_campo as rc_num')
            ->join('respuestas_campo as rc_cat', 'rc_cat.id_formulario_resultado', '=', 'rc_num.id_formulario_resultado')
            ->join('formulario_ensayos_resultados as r', 'r.id', '=', 'rc_num.id_formulario_resultado')
            ->where('r.id_ciclo', $idCiclo)
            ->where('rc_num.id_parametro_campo', $idCampoNum)
            ->where('rc_cat.id_parametro_campo', $idCampoCat)
            ->whereNotNull('rc_num.valor_numero')
            ->select([
                'rc_num.valor_numero as valor',
                DB::raw('COALESCE(rc_cat.valor_texto_opcion, rc_cat.valor_texto, IF(rc_cat.valor_booleano = 1, "Sí", IF(rc_cat.valor_booleano = 0, "No", NULL))) as categoria'),
            ])
            ->get();

        $grupos = $rows->groupBy('categoria');

        return $grupos->map(function ($filas, $categoria) {
            $valores = $filas->pluck('valor')->map(fn ($v) => (float) $v)->sort()->values()->all();
            $n = count($valores);
            if ($n === 0) return null;

            $q1      = self::percentilOrdenado($valores, 0.25);
            $mediana = self::medianaOrdenada($valores);
            $q3      = self::percentilOrdenado($valores, 0.75);
            $iqr     = $q3 - $q1;
            $whMin   = $q1 - 1.5 * $iqr;
            $whMax   = $q3 + 1.5 * $iqr;
            $dentro  = array_filter($valores, fn ($v) => $v >= $whMin && $v <= $whMax);
            $outliers = array_values(array_filter($valores, fn ($v) => $v < $whMin || $v > $whMax));

            return (object) [
                'categoria'   => $categoria ?? '(sin categoria)',
                'n'           => $n,
                'q1'          => round($q1, 6),
                'mediana'     => round($mediana, 6),
                'q3'          => round($q3, 6),
                'min'         => round($valores[0], 6),
                'max'         => round($valores[$n - 1], 6),
                'whisker_min' => round($dentro ? min($dentro) : $q1, 6),
                'whisker_max' => round($dentro ? max($dentro) : $q3, 6),
                'outliers'    => array_map(fn ($v) => round($v, 6), $outliers),
            ];
        })->filter()->values();
    }

    /**
     * Tabla cruzada de dos campos categoricos: filas = categorias de X, columnas = categorias de Y.
     *
     * @return array{filas: array<int, string>, columnas: array<int, string>, celdas: array<string, array<string, int>>}
     */
    private static function tablaCruzada(int $idCiclo, int $idCampoX, int $idCampoY): array
    {
        $rows = DB::table('respuestas_campo as rc_x')
            ->join('respuestas_campo as rc_y', 'rc_y.id_formulario_resultado', '=', 'rc_x.id_formulario_resultado')
            ->join('formulario_ensayos_resultados as r', 'r.id', '=', 'rc_x.id_formulario_resultado')
            ->where('r.id_ciclo', $idCiclo)
            ->where('rc_x.id_parametro_campo', $idCampoX)
            ->where('rc_y.id_parametro_campo', $idCampoY)
            ->select([
                DB::raw('COALESCE(rc_x.valor_texto_opcion, rc_x.valor_texto, IF(rc_x.valor_booleano = 1, "Sí", IF(rc_x.valor_booleano = 0, "No", NULL))) as cat_x'),
                DB::raw('COALESCE(rc_y.valor_texto_opcion, rc_y.valor_texto, IF(rc_y.valor_booleano = 1, "Sí", IF(rc_y.valor_booleano = 0, "No", NULL))) as cat_y'),
                DB::raw('COUNT(*) as cantidad'),
            ])
            ->groupBy('cat_x', 'cat_y')
            ->get();

        $filasSet = [];
        $colsSet  = [];
        $celdas   = [];

        foreach ($rows as $r) {
            $x = $r->cat_x ?? '(vacio)';
            $y = $r->cat_y ?? '(vacio)';
            $filasSet[$x] = true;
            $colsSet[$y]  = true;
            $celdas[$x][$y] = (int) $r->cantidad;
        }

        return [
            'filas'    => array_keys($filasSet),
            'columnas' => array_keys($colsSet),
            'celdas'   => $celdas,
        ];
    }

    /**
     * Lista de opciones para un campo categorico (para selector de filtro).
     */
    public static function opcionesDelCampo(int $idCampo): Collection
    {
        return DB::table('respuestas_campo')
            ->where('id_parametro_campo', $idCampo)
            ->whereNotNull('id_opcion_seleccionada')
            ->select([
                'id_opcion_seleccionada as id',
                DB::raw('MIN(COALESCE(valor_texto_opcion, valor_texto)) as etiqueta'),
            ])
            ->groupBy('id_opcion_seleccionada')
            ->orderBy('etiqueta')
            ->get();
    }

    // --- Helpers privados nuevos ---

    /**
     * Devuelve los valores numericos del campo indexados por id_formulario_resultado
     * (cada uno con la metadata del lab).
     */
    private static function valoresNumericosPorResultado(int $idCiclo, int $idCampo): array
    {
        $rows = DB::table('respuestas_campo as rc')
            ->join('formulario_ensayos_resultados as r', 'r.id', '=', 'rc.id_formulario_resultado')
            ->where('r.id_ciclo', $idCiclo)
            ->where('rc.id_parametro_campo', $idCampo)
            ->whereNotNull('rc.valor_numero')
            ->select(['rc.id_formulario_resultado', 'r.id_laboratorio', 'r.nombre_lab', 'r.cod_lab', 'rc.valor_numero'])
            ->get();

        if ($rows->isEmpty()) {
            // Fallback: text numerico
            $rows = DB::table('respuestas_campo as rc')
                ->join('formulario_ensayos_resultados as r', 'r.id', '=', 'rc.id_formulario_resultado')
                ->where('r.id_ciclo', $idCiclo)
                ->where('rc.id_parametro_campo', $idCampo)
                ->whereRaw('rc.valor_texto REGEXP "^-?[0-9]+(\\\\.[0-9]+)?$"')
                ->select(['rc.id_formulario_resultado', 'r.id_laboratorio', 'r.nombre_lab', 'r.cod_lab', 'rc.valor_texto as valor_numero'])
                ->get();
        }

        $out = [];
        foreach ($rows as $r) {
            $out[(int) $r->id_formulario_resultado] = (object) [
                'id_laboratorio' => (int) $r->id_laboratorio,
                'nombre_lab'     => $r->nombre_lab,
                'cod_lab'        => $r->cod_lab,
                'valor'          => (float) $r->valor_numero,
            ];
        }
        return $out;
    }

    private static function valoresNumericosFiltrados(int $idCiclo, int $idCampo, ?array $resultadosFiltrados): Collection
    {
        $vals = self::valoresNumericosDelCampo($idCiclo, $idCampo);
        if ($resultadosFiltrados === null) return $vals;

        // Necesitamos filtrar por id_formulario_resultado: refacemos la query con el filtro
        $rows = DB::table('respuestas_campo as rc')
            ->join('formulario_ensayos_resultados as r', 'r.id', '=', 'rc.id_formulario_resultado')
            ->where('r.id_ciclo', $idCiclo)
            ->where('rc.id_parametro_campo', $idCampo)
            ->whereIn('rc.id_formulario_resultado', $resultadosFiltrados)
            ->whereNotNull('rc.valor_numero')
            ->pluck('rc.valor_numero')
            ->map(fn ($v) => (float) $v);

        if ($rows->isNotEmpty()) return $rows;

        return DB::table('respuestas_campo as rc')
            ->join('formulario_ensayos_resultados as r', 'r.id', '=', 'rc.id_formulario_resultado')
            ->where('r.id_ciclo', $idCiclo)
            ->where('rc.id_parametro_campo', $idCampo)
            ->whereIn('rc.id_formulario_resultado', $resultadosFiltrados)
            ->whereRaw('rc.valor_texto REGEXP "^-?[0-9]+(\\\\.[0-9]+)?$"')
            ->pluck('rc.valor_texto')
            ->map(fn ($v) => (float) $v);
    }

    /**
     * Histograma a partir de una lista de valores ya en memoria.
     */
    private static function histogramaDeValores(array $valores, int $bins = 20): array
    {
        $r = self::construirHistograma($valores, $bins);
        return ['bins' => $r['bins'], 'modo' => $r['modo']];
    }

    /**
     * Logica central de histograma con deteccion de valores discretos.
     *
     * Modos:
     * - 'vacio': sin datos
     * - 'unico': un solo valor distinto
     * - 'discreto': pocos valores unicos (<=25), un bin por valor sin gaps
     * - 'continuo': bins equidistantes clasicos
     */
    private static function construirHistograma(array $valores, int $bins = 20): array
    {
        $n = count($valores);
        if ($n === 0) {
            return ['bins' => [], 'modo' => 'vacio', 'media' => null, 'desv' => null];
        }

        $unicos = array_unique($valores);
        sort($unicos);

        $min = $unicos[0];
        $max = end($unicos);
        $media = array_sum($valores) / $n;
        $desv  = $n > 1 ? self::desvMuestral($valores, $media) : 0.0;

        if (count($unicos) === 1) {
            return [
                'bins'  => [[
                    'rango_inicio' => $min,
                    'rango_fin'    => $max,
                    'etiqueta'     => (string) $min,
                    'count'        => $n,
                ]],
                'modo'  => 'unico',
                'media' => round($media, 6),
                'desv'  => round($desv, 6),
            ];
        }

        // Modo discreto: pocos valores unicos -> un bin por valor
        if (count($unicos) <= self::UMBRAL_VALORES_DISCRETOS) {
            $counts = array_count_values(array_map(fn ($v) => (string) $v, $valores));
            $out = [];
            foreach ($unicos as $v) {
                $key = (string) $v;
                $out[] = [
                    'rango_inicio' => $v,
                    'rango_fin'    => $v,
                    'etiqueta'     => (string) $v,
                    'count'        => $counts[$key] ?? 0,
                ];
            }
            return [
                'bins'  => $out,
                'modo'  => 'discreto',
                'media' => round($media, 6),
                'desv'  => round($desv, 6),
            ];
        }

        // Modo continuo: bins equidistantes
        $ancho = ($max - $min) / $bins;
        $binsArr = array_fill(0, $bins, 0);
        foreach ($valores as $v) {
            $idx = (int) floor(($v - $min) / $ancho);
            if ($idx >= $bins) $idx = $bins - 1;
            $binsArr[$idx]++;
        }

        $out = [];
        for ($i = 0; $i < $bins; $i++) {
            $inicio = $min + $i * $ancho;
            $fin    = $inicio + $ancho;
            $out[] = [
                'rango_inicio' => round($inicio, 4),
                'rango_fin'    => round($fin, 4),
                'etiqueta'     => round($inicio, 2) . '-' . round($fin, 2),
                'count'        => $binsArr[$i],
            ];
        }

        return [
            'bins'  => $out,
            'modo'  => 'continuo',
            'media' => round($media, 6),
            'desv'  => round($desv, 6),
        ];
    }

    private static function statsBasicos(array $ordenado): array
    {
        $n = count($ordenado);
        $media = array_sum($ordenado) / $n;
        $desv  = $n > 1 ? self::desvMuestral($ordenado, $media) : 0.0;
        return [
            'n'      => $n,
            'media'  => round($media, 6),
            'mediana'=> round(self::medianaOrdenada($ordenado), 6),
            'desv'   => round($desv, 6),
            'min'    => round($ordenado[0], 6),
            'max'    => round($ordenado[$n - 1], 6),
        ];
    }

    /**
     * Cuenta respuestas no nulas (en cualquier columna de valor) para un campo.
     */
    private static function contarRespuestasNoNulasCampo(int $idCiclo, int $idCampo): int
    {
        return (int) DB::table('respuestas_campo as rc')
            ->join('formulario_ensayos_resultados as r', 'r.id', '=', 'rc.id_formulario_resultado')
            ->where('r.id_ciclo', $idCiclo)
            ->where('rc.id_parametro_campo', $idCampo)
            ->where(function ($q) {
                $q->whereNotNull('rc.valor_texto')
                  ->orWhereNotNull('rc.valor_numero')
                  ->orWhereNotNull('rc.valor_fecha')
                  ->orWhereNotNull('rc.valor_booleano')
                  ->orWhereNotNull('rc.id_opcion_seleccionada');
            })
            ->count();
    }

    private static function desvMuestral(array $valores, float $media): float
    {
        $n = count($valores);
        if ($n < 2) return 0.0;
        $sumSq = 0.0;
        foreach ($valores as $v) {
            $sumSq += ($v - $media) ** 2;
        }
        return sqrt($sumSq / ($n - 1));
    }

    private static function medianaOrdenada(array $ordenado): float
    {
        $n = count($ordenado);
        $mid = intdiv($n, 2);
        return $n % 2 === 1
            ? (float) $ordenado[$mid]
            : ((float) $ordenado[$mid - 1] + (float) $ordenado[$mid]) / 2;
    }

    private static function percentilOrdenado(array $ordenado, float $p): float
    {
        $n = count($ordenado);
        if ($n === 1) return (float) $ordenado[0];
        $pos = $p * ($n - 1);
        $lo  = (int) floor($pos);
        $hi  = (int) ceil($pos);
        if ($lo === $hi) return (float) $ordenado[$lo];
        $frac = $pos - $lo;
        return (float) $ordenado[$lo] + $frac * ((float) $ordenado[$hi] - (float) $ordenado[$lo]);
    }

    private static function fmt(?float $v): string
    {
        if ($v === null) return '—';
        return (string) round($v, 3);
    }
}
