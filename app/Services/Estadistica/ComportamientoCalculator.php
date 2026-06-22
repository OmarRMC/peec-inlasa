<?php

namespace App\Services\Estadistica;

use App\Models\Inscripcion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Metricas de comportamiento (demanda / participacion) de ensayos y paquetes.
 *
 * A diferencia de CalculadorClasico (que opera sobre respuestas_campo con
 * z-score), este servicio agrega sobre inscripciones para entender que tan
 * demandados son los ensayos/paquetes y como evoluciona la participacion
 * entre gestiones. No usa estadistica z-score.
 *
 * Solo cuenta inscripciones con estado valido (Aprobado / Vencido), igual que
 * el scope Inscripcion::aprobadoOrVencido().
 */
class ComportamientoCalculator
{
    /** Estados de inscripcion que cuentan como participacion real. */
    private const ESTADOS_VALIDOS = [
        Inscripcion::STATUS_APROBADO,
        Inscripcion::STATUS_VENCIDO,
    ];

    /**
     * Nº de laboratorios distintos inscritos por ensayo en una gestion.
     * Incluye paquete porque el nombre del ensayo puede repetirse entre
     * paquetes distintos.
     *
     * @return Collection<int, object{id_ensayo:int, descripcion:string, paquete:?string, labs_inscritos:int}>
     */
    public static function inscritosPorEnsayo(string|int $gestion): Collection
    {
        return DB::table('inscripcion_ea as iea')
            ->join('inscripcion as ins', 'ins.id', '=', 'iea.id_inscripcion')
            ->join('ensayo_aptitud as ea', 'ea.id', '=', 'iea.id_ea')
            ->leftJoin('paquete as p', 'p.id', '=', 'ea.id_paquete')
            ->where('ins.gestion', $gestion)
            ->whereIn('ins.status_inscripcion', self::ESTADOS_VALIDOS)
            ->groupBy('ea.id', 'ea.descripcion', 'p.descripcion')
            ->select(
                'ea.id as id_ensayo',
                'ea.descripcion',
                'p.descripcion as paquete',
                DB::raw('COUNT(DISTINCT ins.id_lab) as labs_inscritos'),
            )
            ->orderByDesc('labs_inscritos')
            ->orderBy('p.descripcion')
            ->get()
            ->map(fn ($e) => (object) [
                'id_ensayo'      => (int) $e->id_ensayo,
                'descripcion'    => $e->descripcion,
                'paquete'        => $e->paquete,
                'labs_inscritos' => (int) $e->labs_inscritos,
            ]);
    }

    /**
     * Nº de laboratorios distintos inscritos por paquete en una gestion,
     * con su cupo maximo para contexto de ocupacion.
     *
     * @return Collection<int, object{id_paquete:int, descripcion:string, max_participantes:?int, labs_inscritos:int}>
     */
    public static function inscritosPorPaquete(string|int $gestion): Collection
    {
        return DB::table('detalle_inscripcion as di')
            ->join('inscripcion as ins', 'ins.id', '=', 'di.id_inscripcion')
            ->join('paquete as p', 'p.id', '=', 'di.id_paquete')
            ->where('ins.gestion', $gestion)
            ->whereIn('ins.status_inscripcion', self::ESTADOS_VALIDOS)
            ->groupBy('p.id', 'p.descripcion', 'p.max_participantes')
            ->select(
                'p.id as id_paquete',
                'p.descripcion',
                'p.max_participantes',
                DB::raw('COUNT(DISTINCT ins.id_lab) as labs_inscritos'),
            )
            ->orderByDesc('labs_inscritos')
            ->orderBy('p.descripcion')
            ->get()
            ->map(fn ($p) => (object) [
                'id_paquete'        => (int) $p->id_paquete,
                'descripcion'       => $p->descripcion,
                'max_participantes' => $p->max_participantes !== null ? (int) $p->max_participantes : null,
                'labs_inscritos'    => (int) $p->labs_inscritos,
            ]);
    }

    /**
     * Evolucion de inscripciones por gestion (todas las gestiones existentes).
     * Util para line chart de tendencia.
     *
     * @return Collection<int, object{gestion:string, total_inscripciones:int, total_labs:int}>
     */
    public static function evolucionInscripciones(): Collection
    {
        return DB::table('inscripcion')
            ->whereIn('status_inscripcion', self::ESTADOS_VALIDOS)
            ->groupBy('gestion')
            ->select(
                'gestion',
                DB::raw('COUNT(*) as total_inscripciones'),
                DB::raw('COUNT(DISTINCT id_lab) as total_labs'),
            )
            ->orderBy('gestion')
            ->get()
            ->map(fn ($g) => (object) [
                'gestion'             => (string) $g->gestion,
                'total_inscripciones' => (int) $g->total_inscripciones,
                'total_labs'          => (int) $g->total_labs,
            ]);
    }
}
