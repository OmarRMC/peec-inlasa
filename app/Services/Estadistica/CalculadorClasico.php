<?php

namespace App\Services\Estadistica;

use App\Models\ParametroCampo;
use App\Models\RespuestaCampo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Calculador de estadisticas clasicas para PEEC.
 *
 * Opera directamente sobre respuestas_campo + formulario_ensayos_resultados.
 * No requiere tablas auxiliares. Stats en vivo por query.
 *
 * Umbrales 2/3 hardcoded por ahora (Sprint 1). Se haran configurables
 * cuando el negocio lo requiera.
 */
class CalculadorClasico
{
    public const UMBRAL_SATISFACTORIO = 2.0;
    public const UMBRAL_CUESTIONABLE  = 3.0;

    public const DESEMPENO_SATISFACTORIO   = 'satisfactorio';
    public const DESEMPENO_CUESTIONABLE    = 'cuestionable';
    public const DESEMPENO_NO_SATISFACTORIO = 'no_satisfactorio';

    /**
     * Un campo es "evaluable" si entra en analisis estadistico.
     * Sprint 1: solo numericos.
     * Sprint 3: tambien selects/radio cuando exista opciones_selector.es_correcta.
     */
    public static function esEvaluable(ParametroCampo $campo): bool
    {
        return $campo->tipo === 'number';
    }

    /**
     * Clasifica un z-score absoluto en una de las 3 categorias.
     */
    public static function clasificar(float $absZ): string
    {
        if ($absZ < self::UMBRAL_SATISFACTORIO)  return self::DESEMPENO_SATISFACTORIO;
        if ($absZ < self::UMBRAL_CUESTIONABLE)   return self::DESEMPENO_CUESTIONABLE;
        return self::DESEMPENO_NO_SATISFACTORIO;
    }

    /**
     * Consenso descriptivo de un campo numerico en un ciclo.
     * Mediana se calcula en PHP (MySQL no tiene MEDIAN nativo portable).
     *
     * @return array{n:int, media:?float, mediana:?float, desv:?float, cv_pct:?float, minimo:?float, maximo:?float}
     */
    public static function consenso(int $idCiclo, int $idCampo): array
    {
        $valores = RespuestaCampo::query()
            ->join('formulario_ensayos_resultados as r', 'r.id', '=', 'respuestas_campo.id_formulario_resultado')
            ->where('r.id_ciclo', $idCiclo)
            ->where('respuestas_campo.id_parametro_campo', $idCampo)
            ->whereNotNull('respuestas_campo.valor_numero')
            ->pluck('respuestas_campo.valor_numero')
            ->map(fn ($v) => (float) $v)
            ->sort()
            ->values();

        $n = $valores->count();
        if ($n === 0) {
            return ['n' => 0, 'media' => null, 'mediana' => null, 'desv' => null, 'cv_pct' => null, 'minimo' => null, 'maximo' => null];
        }

        $media = $valores->avg();
        $desv  = $n > 1 ? self::desviacionEstandarMuestral($valores->all(), $media) : 0.0;
        $cv    = ($media !== 0.0 && $media !== null) ? ($desv / abs($media)) * 100 : null;

        return [
            'n'       => $n,
            'media'   => round($media, 6),
            'mediana' => round(self::medianaOrdenada($valores->all()), 6),
            'desv'    => round($desv, 6),
            'cv_pct'  => $cv !== null ? round($cv, 4) : null,
            'minimo'  => round($valores->first(), 6),
            'maximo'  => round($valores->last(), 6),
        ];
    }

    /**
     * Z-score por laboratorio para un campo numerico en un ciclo.
     * Se calcula en SQL para aprovechar el indice idx_campo_numero.
     *
     * @return Collection<int, object{id_laboratorio:int, nombre_lab:string, cod_lab:?string, valor:float, z_score:?float, desempeno:string}>
     */
    public static function zScoreLabs(int $idCiclo, int $idCampo): Collection
    {
        $cons = self::consenso($idCiclo, $idCampo);
        if ($cons['n'] === 0 || $cons['desv'] === null || $cons['desv'] == 0.0) {
            return collect();
        }

        $media = $cons['media'];
        $desv  = $cons['desv'];

        return RespuestaCampo::query()
            ->join('formulario_ensayos_resultados as r', 'r.id', '=', 'respuestas_campo.id_formulario_resultado')
            ->where('r.id_ciclo', $idCiclo)
            ->where('respuestas_campo.id_parametro_campo', $idCampo)
            ->whereNotNull('respuestas_campo.valor_numero')
            ->select([
                'r.id_laboratorio',
                'r.nombre_lab',
                'r.cod_lab',
                'respuestas_campo.valor_numero as valor',
            ])
            ->orderBy('r.id_laboratorio')
            ->get()
            ->map(function ($row) use ($media, $desv) {
                $z = ((float) $row->valor - $media) / $desv;
                return (object) [
                    'id_laboratorio' => (int) $row->id_laboratorio,
                    'nombre_lab'     => $row->nombre_lab,
                    'cod_lab'        => $row->cod_lab,
                    'valor'          => (float) $row->valor,
                    'z_score'        => round($z, 4),
                    'desempeno'      => self::clasificar(abs($z)),
                ];
            })
            ->sortByDesc(fn ($r) => abs($r->z_score))
            ->values();
    }

    /**
     * Tasa de respuesta del ciclo: cuantos labs enviaron algun resultado
     * vs cuantos labs estan inscritos al ensayo del ciclo en esa gestion.
     *
     * @return array{labs_enviaron:int, labs_inscritos:int, porcentaje:?float}
     */
    public static function tasaRespuesta(int $idCiclo): array
    {
        $ciclo = DB::table('ciclos')->where('id', $idCiclo)->first();
        if (!$ciclo) {
            return ['labs_enviaron' => 0, 'labs_inscritos' => 0, 'porcentaje' => null];
        }

        $enviaron = DB::table('formulario_ensayos_resultados')
            ->where('id_ciclo', $idCiclo)
            ->distinct()
            ->count('id_laboratorio');

        $inscritos = DB::table('inscripcion_ea as iea')
            ->join('inscripcion as ins', 'ins.id', '=', 'iea.id_inscripcion')
            ->where('iea.id_ea', $ciclo->id_ensayo)
            ->where('ins.gestion', $ciclo->gestion)
            ->distinct()
            ->count('ins.id_lab');

        return [
            'labs_enviaron'  => $enviaron,
            'labs_inscritos' => $inscritos,
            'porcentaje'     => $inscritos > 0 ? round($enviaron * 100 / $inscritos, 2) : null,
        ];
    }

    /**
     * % de satisfactorios / cuestionables / no-satisfactorios agregado
     * a nivel ciclo (todos los campos evaluables).
     *
     * @return array{n:int, satisfactorios:int, cuestionables:int, no_satisfactorios:int, pct_satisfactorios:?float}
     */
    public static function porcentajeSatisfactorios(int $idCiclo): array
    {
        // Obtener todos los campos evaluables que tienen respuestas en este ciclo
        $idsCampos = DB::table('respuestas_campo as rc')
            ->join('formulario_ensayos_resultados as r', 'r.id', '=', 'rc.id_formulario_resultado')
            ->where('r.id_ciclo', $idCiclo)
            ->whereNotNull('rc.valor_numero')
            ->distinct()
            ->pluck('rc.id_parametro_campo');

        $totals = ['n' => 0, 'sat' => 0, 'cuest' => 0, 'no_sat' => 0];

        foreach ($idsCampos as $idCampo) {
            foreach (self::zScoreLabs($idCiclo, (int) $idCampo) as $row) {
                $totals['n']++;
                $totals[match ($row->desempeno) {
                    self::DESEMPENO_SATISFACTORIO   => 'sat',
                    self::DESEMPENO_CUESTIONABLE    => 'cuest',
                    self::DESEMPENO_NO_SATISFACTORIO => 'no_sat',
                }]++;
            }
        }

        return [
            'n'                  => $totals['n'],
            'satisfactorios'     => $totals['sat'],
            'cuestionables'      => $totals['cuest'],
            'no_satisfactorios'  => $totals['no_sat'],
            'pct_satisfactorios' => $totals['n'] > 0 ? round($totals['sat'] * 100 / $totals['n'], 2) : null,
        ];
    }

    /**
     * Histograma de un campo numerico: bins equidistantes entre min y max,
     * conteos por bin. Util para Chart.js bar chart.
     *
     * @return array{bins:array<int,array{rango_inicio:float, rango_fin:float, etiqueta:string, count:int}>, media:?float, desv:?float}
     */
    public static function histograma(int $idCiclo, int $idCampo, int $bins = 15): array
    {
        $valores = RespuestaCampo::query()
            ->join('formulario_ensayos_resultados as r', 'r.id', '=', 'respuestas_campo.id_formulario_resultado')
            ->where('r.id_ciclo', $idCiclo)
            ->where('respuestas_campo.id_parametro_campo', $idCampo)
            ->whereNotNull('respuestas_campo.valor_numero')
            ->pluck('respuestas_campo.valor_numero')
            ->map(fn ($v) => (float) $v)
            ->all();

        $n = count($valores);
        if ($n === 0) {
            return ['bins' => [], 'media' => null, 'desv' => null];
        }

        $min = min($valores);
        $max = max($valores);

        // Caso degenerado: todos los valores iguales
        if ($min == $max) {
            return [
                'bins' => [[
                    'rango_inicio' => $min,
                    'rango_fin'    => $max,
                    'etiqueta'     => (string) $min,
                    'count'        => $n,
                ]],
                'media' => $min,
                'desv'  => 0.0,
            ];
        }

        $ancho = ($max - $min) / $bins;
        $binsArr = array_fill(0, $bins, 0);

        foreach ($valores as $v) {
            $idx = (int) floor(($v - $min) / $ancho);
            if ($idx >= $bins) $idx = $bins - 1; // edge case del maximo
            $binsArr[$idx]++;
        }

        $resultado = [];
        for ($i = 0; $i < $bins; $i++) {
            $inicio = $min + $i * $ancho;
            $fin    = $inicio + $ancho;
            $resultado[] = [
                'rango_inicio' => round($inicio, 4),
                'rango_fin'    => round($fin, 4),
                'etiqueta'     => round($inicio, 2) . '-' . round($fin, 2),
                'count'        => $binsArr[$i],
            ];
        }

        $media = array_sum($valores) / $n;
        $desv  = $n > 1 ? self::desviacionEstandarMuestral($valores, $media) : 0.0;

        return [
            'bins'  => $resultado,
            'media' => round($media, 6),
            'desv'  => round($desv, 6),
        ];
    }

    /**
     * Datos para box-plot Chart.js: q1, mediana, q3, whiskers IQR-based, outliers.
     *
     * @return array{q1:?float, mediana:?float, q3:?float, min:?float, max:?float, outliers:array<int,float>, whisker_min:?float, whisker_max:?float}
     */
    public static function boxPlot(int $idCiclo, int $idCampo): array
    {
        $valores = RespuestaCampo::query()
            ->join('formulario_ensayos_resultados as r', 'r.id', '=', 'respuestas_campo.id_formulario_resultado')
            ->where('r.id_ciclo', $idCiclo)
            ->where('respuestas_campo.id_parametro_campo', $idCampo)
            ->whereNotNull('respuestas_campo.valor_numero')
            ->pluck('respuestas_campo.valor_numero')
            ->map(fn ($v) => (float) $v)
            ->sort()
            ->values()
            ->all();

        $n = count($valores);
        if ($n === 0) {
            return ['q1' => null, 'mediana' => null, 'q3' => null, 'min' => null, 'max' => null, 'outliers' => [], 'whisker_min' => null, 'whisker_max' => null];
        }

        $q1      = self::percentilOrdenado($valores, 0.25);
        $mediana = self::medianaOrdenada($valores);
        $q3      = self::percentilOrdenado($valores, 0.75);
        $iqr     = $q3 - $q1;

        $whiskerMin = $q1 - 1.5 * $iqr;
        $whiskerMax = $q3 + 1.5 * $iqr;

        // Whiskers reales: los valores extremos dentro del rango IQR×1.5
        $dentro = array_filter($valores, fn ($v) => $v >= $whiskerMin && $v <= $whiskerMax);
        $whiskerMinReal = $dentro ? min($dentro) : $q1;
        $whiskerMaxReal = $dentro ? max($dentro) : $q3;

        $outliers = array_values(array_filter($valores, fn ($v) => $v < $whiskerMin || $v > $whiskerMax));

        return [
            'q1'           => round($q1, 6),
            'mediana'      => round($mediana, 6),
            'q3'           => round($q3, 6),
            'min'          => round($valores[0], 6),
            'max'          => round($valores[$n - 1], 6),
            'outliers'     => array_map(fn ($v) => round($v, 6), $outliers),
            'whisker_min'  => round($whiskerMinReal, 6),
            'whisker_max'  => round($whiskerMaxReal, 6),
        ];
    }

    /**
     * Tendencia inter-ciclos para un campo: serie de N ultimos ciclos con
     * media de consenso y desv. Util para Chart.js line chart.
     *
     * @return Collection<int, object{id_ciclo:int, nombre:string, fecha:?string, n:int, media:?float, desv:?float}>
     */
    public static function tendenciaConsenso(int $idCampo, int $nCiclos = 6): Collection
    {
        // Identificar el ensayo del campo via parametro -> seccion -> formulario -> ensayo_formulario -> ensayo_aptitud
        // Mas simple: obtener directamente los ciclos donde el campo tiene respuestas.
        $idsCiclo = DB::table('respuestas_campo as rc')
            ->join('formulario_ensayos_resultados as r', 'r.id', '=', 'rc.id_formulario_resultado')
            ->where('rc.id_parametro_campo', $idCampo)
            ->whereNotNull('rc.valor_numero')
            ->orderByDesc('r.id_ciclo')
            ->distinct()
            ->limit($nCiclos)
            ->pluck('r.id_ciclo');

        if ($idsCiclo->isEmpty()) {
            return collect();
        }

        $ciclos = DB::table('ciclos')->whereIn('id', $idsCiclo)->orderBy('id')->get();

        return $ciclos->map(function ($c) use ($idCampo) {
            $cons = self::consenso((int) $c->id, $idCampo);
            return (object) [
                'id_ciclo' => (int) $c->id,
                'nombre'   => $c->nombre,
                'fecha'    => $c->fecha_inicio_envio_resultados ?? null,
                'n'        => $cons['n'],
                'media'    => $cons['media'],
                'desv'     => $cons['desv'],
            ];
        });
    }

    /**
     * Tasa de respuesta por ensayo en una gestion (usa el ciclo mas reciente
     * de cada ensayo en esa gestion). Util para V6 bar chart del Jefe PEEC.
     *
     * Incluye paquete porque el nombre del ensayo puede repetirse entre
     * paquetes distintos.
     *
     * @return Collection<int, object{id_ensayo:int, descripcion:string, paquete:?string, id_ciclo:?int, labs_enviaron:int, labs_inscritos:int, porcentaje:?float}>
     */
    public static function tasasPorEnsayoGestion(string|int $gestion): Collection
    {
        $ensayos = DB::table('ensayo_aptitud as ea')
            ->leftJoin('paquete as p', 'p.id', '=', 'ea.id_paquete')
            ->leftJoin('ciclos as c', function ($j) use ($gestion) {
                $j->on('c.id_ensayo', '=', 'ea.id')
                  ->where('c.gestion', $gestion);
            })
            ->select(
                'ea.id as id_ensayo',
                'ea.descripcion',
                'p.descripcion as paquete',
                DB::raw('MAX(c.id) as id_ciclo'),
            )
            ->groupBy('ea.id', 'ea.descripcion', 'p.descripcion')
            ->orderBy('p.descripcion')
            ->orderBy('ea.descripcion')
            ->get();

        return $ensayos->map(function ($e) {
            if (!$e->id_ciclo) {
                return (object) [
                    'id_ensayo'     => (int) $e->id_ensayo,
                    'descripcion'   => $e->descripcion,
                    'paquete'       => $e->paquete,
                    'id_ciclo'      => null,
                    'labs_enviaron' => 0,
                    'labs_inscritos'=> 0,
                    'porcentaje'    => null,
                ];
            }
            $tasa = self::tasaRespuesta((int) $e->id_ciclo);
            return (object) [
                'id_ensayo'     => (int) $e->id_ensayo,
                'descripcion'   => $e->descripcion,
                'paquete'       => $e->paquete,
                'id_ciclo'      => (int) $e->id_ciclo,
                'labs_enviaron' => $tasa['labs_enviaron'],
                'labs_inscritos'=> $tasa['labs_inscritos'],
                'porcentaje'    => $tasa['porcentaje'],
            ];
        });
    }

    /**
     * Consenso robusto via Algoritmo A ISO 13528.
     *
     * @return array{n:int, x_star:?float, s_star:?float, iteraciones:int, convergio:bool}
     */
    public static function consensoRobusto(int $idCiclo, int $idCampo): array
    {
        $valores = RespuestaCampo::query()
            ->join('formulario_ensayos_resultados as r', 'r.id', '=', 'respuestas_campo.id_formulario_resultado')
            ->where('r.id_ciclo', $idCiclo)
            ->where('respuestas_campo.id_parametro_campo', $idCampo)
            ->whereNotNull('respuestas_campo.valor_numero')
            ->pluck('respuestas_campo.valor_numero')
            ->map(fn ($v) => (float) $v)
            ->all();

        return AlgoritmoIsoA::calcular($valores);
    }

    /**
     * Z-score robusto por laboratorio: usa x* y s* del Algoritmo A
     * en lugar de media/desv clasicas. Reemplaza zScoreLabs() cuando
     * se sospecha contaminacion por outliers.
     *
     * @return Collection<int, object{id_laboratorio:int, nombre_lab:string, cod_lab:?string, valor:float, z_score:?float, desempeno:string}>
     */
    public static function zScoreRobustoLabs(int $idCiclo, int $idCampo): Collection
    {
        $rob = self::consensoRobusto($idCiclo, $idCampo);
        if ($rob['n'] === 0 || $rob['s_star'] === null || $rob['s_star'] == 0.0) {
            return collect();
        }

        $xStar = $rob['x_star'];
        $sStar = $rob['s_star'];

        return RespuestaCampo::query()
            ->join('formulario_ensayos_resultados as r', 'r.id', '=', 'respuestas_campo.id_formulario_resultado')
            ->where('r.id_ciclo', $idCiclo)
            ->where('respuestas_campo.id_parametro_campo', $idCampo)
            ->whereNotNull('respuestas_campo.valor_numero')
            ->select([
                'r.id_laboratorio',
                'r.nombre_lab',
                'r.cod_lab',
                'respuestas_campo.valor_numero as valor',
            ])
            ->orderBy('r.id_laboratorio')
            ->get()
            ->map(function ($row) use ($xStar, $sStar) {
                $z = ((float) $row->valor - $xStar) / $sStar;
                return (object) [
                    'id_laboratorio' => (int) $row->id_laboratorio,
                    'nombre_lab'     => $row->nombre_lab,
                    'cod_lab'        => $row->cod_lab,
                    'valor'          => (float) $row->valor,
                    'z_score'        => round($z, 4),
                    'desempeno'      => self::clasificar(abs($z)),
                ];
            })
            ->sortByDesc(fn ($r) => abs($r->z_score))
            ->values();
    }

    /**
     * Heatmap ensayos × ciclos de una gestion: para cada celda, el % de
     * satisfactorios. Util para V9 del Jefe PEEC.
     *
     * Incluye paquete porque el nombre del ensayo puede repetirse entre
     * paquetes distintos.
     *
     * @return array{ensayos:array<int,array{id:int,descripcion:string,paquete:?string}>, ciclos:array<int,array{id:int,nombre:string,id_ensayo:int}>, celdas:array<int,array<int,?float>>}
     *   `celdas[id_ensayo][id_ciclo] = pct_satisfactorios | null`
     */
    public static function heatmapEnsayosCiclos(string|int $gestion): array
    {
        $ensayos = DB::table('ensayo_aptitud as ea')
            ->join('ciclos as c', 'c.id_ensayo', '=', 'ea.id')
            ->leftJoin('paquete as p', 'p.id', '=', 'ea.id_paquete')
            ->where('c.gestion', $gestion)
            ->select('ea.id', 'ea.descripcion', 'p.descripcion as paquete')
            ->distinct()
            ->orderBy('p.descripcion')
            ->orderBy('ea.descripcion')
            ->get();

        $ciclos = DB::table('ciclos')
            ->where('gestion', $gestion)
            ->orderBy('id')
            ->select('id', 'nombre', 'id_ensayo')
            ->get();

        $celdas = [];
        foreach ($ciclos as $c) {
            $pct = self::porcentajeSatisfactorios((int) $c->id);
            $celdas[(int) $c->id_ensayo][(int) $c->id] = $pct['pct_satisfactorios'];
        }

        return [
            'ensayos' => $ensayos->map(fn ($e) => [
                'id'          => (int) $e->id,
                'descripcion' => $e->descripcion,
                'paquete'     => $e->paquete,
            ])->all(),
            'ciclos'  => $ciclos->map(fn ($c) => ['id' => (int) $c->id, 'nombre' => $c->nombre, 'id_ensayo' => (int) $c->id_ensayo])->all(),
            'celdas'  => $celdas,
        ];
    }

    /**
     * Detecta laboratorios con desempeño "persistentemente problemático":
     * labs con al menos $minNoSat respuestas no satisfactorias (|z|>=3)
     * en los últimos $nCiclos ciclos de la gestión. Útil para V11.
     *
     * @return Collection<int, object{id_laboratorio:int, nombre_lab:string, cod_lab:?string, total_no_sat:int, total_evaluaciones:int, pct_no_sat:float}>
     */
    public static function labsProblematicos(string|int $gestion, int $nCiclos = 6, int $minNoSat = 2): Collection
    {
        // Ultimos N ciclos de la gestion
        $idsCiclo = DB::table('ciclos')
            ->where('gestion', $gestion)
            ->orderByDesc('id')
            ->limit($nCiclos)
            ->pluck('id');

        if ($idsCiclo->isEmpty()) {
            return collect();
        }

        // Acumulador por lab: total evaluaciones y total no satisfactorias
        $acum = []; // [id_lab => ['nombre' => ..., 'cod' => ..., 'total' => N, 'nosat' => N]]

        foreach ($idsCiclo as $idCiclo) {
            $idsCampos = DB::table('respuestas_campo as rc')
                ->join('formulario_ensayos_resultados as r', 'r.id', '=', 'rc.id_formulario_resultado')
                ->where('r.id_ciclo', $idCiclo)
                ->whereNotNull('rc.valor_numero')
                ->distinct()
                ->pluck('rc.id_parametro_campo');

            foreach ($idsCampos as $idCampo) {
                foreach (self::zScoreLabs((int) $idCiclo, (int) $idCampo) as $z) {
                    $id = $z->id_laboratorio;
                    if (!isset($acum[$id])) {
                        $acum[$id] = [
                            'nombre' => $z->nombre_lab,
                            'cod'    => $z->cod_lab,
                            'total'  => 0,
                            'nosat'  => 0,
                        ];
                    }
                    $acum[$id]['total']++;
                    if ($z->desempeno === self::DESEMPENO_NO_SATISFACTORIO) {
                        $acum[$id]['nosat']++;
                    }
                }
            }
        }

        return collect($acum)
            ->filter(fn ($a) => $a['nosat'] >= $minNoSat)
            ->map(fn ($a, $id) => (object) [
                'id_laboratorio'      => (int) $id,
                'nombre_lab'          => $a['nombre'],
                'cod_lab'             => $a['cod'],
                'total_no_sat'        => $a['nosat'],
                'total_evaluaciones'  => $a['total'],
                'pct_no_sat'          => round($a['nosat'] * 100 / $a['total'], 2),
            ])
            ->sortByDesc('pct_no_sat')
            ->values();
    }

    /**
     * % satisfactorios agregado a nivel gestion completa (todos los ciclos,
     * todos los campos evaluables). Util para V5 donut del Jefe PEEC.
     *
     * @return array{n:int, satisfactorios:int, cuestionables:int, no_satisfactorios:int, pct_satisfactorios:?float}
     */
    public static function porcentajeSatisfactoriosGestion(string|int $gestion): array
    {
        $idsCiclo = DB::table('ciclos')->where('gestion', $gestion)->pluck('id');

        $totals = ['n' => 0, 'sat' => 0, 'cuest' => 0, 'no_sat' => 0];

        foreach ($idsCiclo as $idCiclo) {
            $pct = self::porcentajeSatisfactorios((int) $idCiclo);
            $totals['n']      += $pct['n'];
            $totals['sat']    += $pct['satisfactorios'];
            $totals['cuest']  += $pct['cuestionables'];
            $totals['no_sat'] += $pct['no_satisfactorios'];
        }

        return [
            'n'                  => $totals['n'],
            'satisfactorios'     => $totals['sat'],
            'cuestionables'      => $totals['cuest'],
            'no_satisfactorios'  => $totals['no_sat'],
            'pct_satisfactorios' => $totals['n'] > 0 ? round($totals['sat'] * 100 / $totals['n'], 2) : null,
        ];
    }

    // --- Helpers privados ---

    /** Desviacion estandar muestral (Bessel) - divide por n-1. */
    private static function desviacionEstandarMuestral(array $valores, float $media): float
    {
        $n = count($valores);
        if ($n < 2) return 0.0;
        $sumSq = 0.0;
        foreach ($valores as $v) {
            $sumSq += ($v - $media) ** 2;
        }
        return sqrt($sumSq / ($n - 1));
    }

    /** Mediana de un array ya ordenado ascendentemente. */
    private static function medianaOrdenada(array $ordenado): float
    {
        $n = count($ordenado);
        $mid = intdiv($n, 2);
        return $n % 2 === 1
            ? (float) $ordenado[$mid]
            : ((float) $ordenado[$mid - 1] + (float) $ordenado[$mid]) / 2;
    }

    /** Percentil por interpolacion lineal (metodo R-7, el de numpy/excel). */
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
}
