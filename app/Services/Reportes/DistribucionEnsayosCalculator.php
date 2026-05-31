<?php

namespace App\Services\Reportes;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DistribucionEnsayosCalculator
{
    public const NO_SATISFACTORIO = 'NO SATISFACTORIO';

    public function normalizarValor(?string $valor): string
    {
        if ($valor === null) {
            return self::NO_SATISFACTORIO;
        }
        $limpio = strtoupper(trim($valor));
        if ($limpio === '' || $limpio === 'NULL') {
            return self::NO_SATISFACTORIO;
        }
        $limpio = preg_replace('/\s+/', ' ', $limpio);
        return strtr($limpio, [
            'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ñ' => 'N',
        ]);
    }

    public function agregar(array $filtros): Collection
    {
        $rows = $this->baseQuery($filtros)
            ->select([
                'dc.id_ea',
                'ea.descripcion as ensayo',
                'p.descripcion as paquete',
                'p.id as id_paquete',
                'a.descripcion as area',
                'a.id as id_area',
                'dc.calificacion_certificado as calificacion_raw',
                DB::raw('COUNT(*) as n'),
            ])
            ->groupBy('dc.id_ea', 'ea.descripcion', 'p.descripcion', 'p.id', 'a.descripcion', 'a.id', 'dc.calificacion_certificado')
            ->get();

        $porEnsayo = [];
        foreach ($rows as $row) {
            $id = (int) $row->id_ea;
            if (!isset($porEnsayo[$id])) {
                $porEnsayo[$id] = [
                    'id_ensayo' => $id,
                    'ensayo' => $row->ensayo,
                    'paquete' => $row->paquete,
                    'id_paquete' => (int) $row->id_paquete,
                    'area' => $row->area,
                    'id_area' => (int) $row->id_area,
                    'calificaciones' => [],
                    'total' => 0,
                ];
            }
            $categoria = $this->normalizarValor($row->calificacion_raw);
            $porEnsayo[$id]['calificaciones'][$categoria] =
                ($porEnsayo[$id]['calificaciones'][$categoria] ?? 0) + (int) $row->n;
            $porEnsayo[$id]['total'] += (int) $row->n;
        }

        return collect(array_values($porEnsayo))->sortBy('ensayo')->values();
    }

    /**
     * Lista ordenada de categorías presentes en los datos agregados.
     * Pone primero las conocidas (en orden lógico) y luego cualquier otra alfabéticamente.
     */
    public function categoriasPresentes(Collection $datosAgregados): array
    {
        $presentes = [];
        foreach ($datosAgregados as $fila) {
            foreach (array_keys($fila['calificaciones']) as $cat) {
                $presentes[$cat] = true;
            }
        }
        $presentes = array_keys($presentes);

        $ordenConocido = [
            'EXCELENTE', 'SATISFACTORIO', 'BUENO', 'ACEPTABLE', 'SUFICIENTE',
            'REGULAR', 'CUESTIONABLE', self::NO_SATISFACTORIO,
        ];
        $conocidas = array_values(array_intersect($ordenConocido, $presentes));
        $resto = array_values(array_diff($presentes, $ordenConocido));
        sort($resto);

        return array_values(array_unique(array_merge($conocidas, $resto)));
    }

    public function detalleLabs(array $filtros, int $idEa, string $categoria): Builder
    {
        $filtros['id_ea'] = $idEa;
        $categoriaNormalizada = $this->normalizarValor($categoria);

        $query = $this->baseQuery($filtros)
            ->join('laboratorio as lab', 'lab.id', '=', 'i.id_lab')
            ->leftJoin('departamento as dep', 'dep.id', '=', 'lab.id_dep')
            ->select([
                'dc.id as id_detalle',
                'lab.id as id_lab',
                'lab.cod_lab',
                'lab.nombre_lab',
                'dep.nombre_dep as departamento',
                'ea.descripcion as ensayo',
                'dc.calificacion_certificado',
                'c.gestion_certificado',
                'c.publicado',
                'dc.temporal',
                'dc.updated_at',
            ]);

        if ($categoriaNormalizada === self::NO_SATISFACTORIO) {
            $query->where(function ($q) {
                $q->whereNull('dc.calificacion_certificado')
                  ->orWhereRaw("TRIM(dc.calificacion_certificado) = ''")
                  ->orWhereRaw('UPPER(TRIM(dc.calificacion_certificado)) = ?', [self::NO_SATISFACTORIO]);
            });
        } else {
            $query->whereRaw('UPPER(TRIM(dc.calificacion_certificado)) = ?', [$categoriaNormalizada]);
        }

        return $query;
    }

    protected function baseQuery(array $filtros): Builder
    {
        $q = DB::table('detalle_certificado as dc')
            ->join('certificado as c', 'c.id', '=', 'dc.id_certificado')
            ->join('inscripcion as i', 'i.id', '=', 'c.id_inscripcion')
            ->join('ensayo_aptitud as ea', 'ea.id', '=', 'dc.id_ea')
            ->join('paquete as p', 'p.id', '=', 'ea.id_paquete')
            ->join('area as a', 'a.id', '=', 'p.id_area');

        if (!empty($filtros['gestion'])) {
            $q->where('c.gestion_certificado', $filtros['gestion']);
        }
        if (!empty($filtros['id_area'])) {
            $q->where('a.id', $filtros['id_area']);
        }
        if (!empty($filtros['id_paquete'])) {
            $q->where('p.id', $filtros['id_paquete']);
        }
        if (!empty($filtros['id_ea'])) {
            $q->where('dc.id_ea', $filtros['id_ea']);
        }

        $estado = $filtros['estado'] ?? 'publicados';
        if ($estado === 'publicados') {
            $q->where('c.publicado', true);
        } elseif ($estado === 'no_publicados') {
            $q->where('c.publicado', false);
        } elseif ($estado === 'pendientes_responsable') {
            $q->where('dc.temporal', true);
        }

        if (isset($filtros['ensayos_permitidos']) && is_array($filtros['ensayos_permitidos'])) {
            if (empty($filtros['ensayos_permitidos'])) {
                $q->whereRaw('1 = 0');
            } else {
                $q->whereIn('dc.id_ea', $filtros['ensayos_permitidos']);
            }
        }

        return $q;
    }
}
