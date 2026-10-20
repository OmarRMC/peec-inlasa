<?php

namespace App\Http\Controllers\Admin;

use App\Exports\DistribucionEnsayosExport;
use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Paquete;
use App\Models\Permiso;
use App\Services\Reportes\DistribucionEnsayosCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;

class ReporteCertificadosController extends Controller
{
    protected const COLORES_CONOCIDOS = [
        'EXCELENTE' => '#0ea5e9',
        'SATISFACTORIO' => '#10b981',
        'BUENO' => '#14b8a6',
        'ACEPTABLE' => '#84cc16',
        'SUFICIENTE' => '#eab308',
        'REGULAR' => '#f59e0b',
        'CUESTIONABLE' => '#f97316',
        'NO SATISFACTORIO' => '#ef4444',
    ];

    protected const PALETA_FALLBACK = [
        '#6366f1', '#a855f7', '#ec4899', '#06b6d4', '#8b5cf6',
        '#22c55e', '#f43f5e', '#0891b2', '#d946ef', '#facc15',
    ];

    public function distribucionEnsayos(Request $request)
    {
        $this->autorizar();
        $ensayosPermitidos = $this->ensayosPermitidos();

        $gestiones = DB::table('certificado')
            ->select('gestion_certificado')
            ->distinct()
            ->orderByDesc('gestion_certificado')
            ->pluck('gestion_certificado');

        $areas = $this->areasVisibles($ensayosPermitidos);
        $paquetes = $this->paquetesVisibles($ensayosPermitidos);

        return view('admin.reportes.distribucion-ensayos', [
            'gestiones' => $gestiones,
            'areas' => $areas,
            'paquetes' => $paquetes,
            'gestionDefault' => $gestiones->first() ?? now()->year,
            'esResponsablePuro' => $ensayosPermitidos !== null,
        ]);
    }

    public function distribucionEnsayosData(Request $request, DistribucionEnsayosCalculator $calculator)
    {
        $this->autorizar();
        $filtros = $this->filtrosDesdeRequest($request);

        $datos = $calculator->agregar($filtros);
        $categorias = $calculator->categoriasPresentes($datos);

        $datasets = $this->construirDatasets($datos, $categorias);

        return response()->json([
            'labels' => $datos->pluck('ensayo')->all(),
            'idEnsayos' => $datos->pluck('id_ensayo')->all(),
            'datasets' => $datasets,
            'totalRegistros' => $datos->sum('total'),
        ]);
    }

    public function distribucionEnsayosDrill(Request $request, DistribucionEnsayosCalculator $calculator)
    {
        $this->autorizar();

        $request->validate([
            'id_ea' => 'required|integer',
            'categoria' => 'required|string|max:100',
        ]);

        $filtros = $this->filtrosDesdeRequest($request);

        $idEa = (int) $request->id_ea;
        if (isset($filtros['ensayos_permitidos'])
            && is_array($filtros['ensayos_permitidos'])
            && !in_array($idEa, $filtros['ensayos_permitidos'], true)) {
            abort(403, 'No tiene acceso a este ensayo.');
        }

        $query = $calculator->detalleLabs($filtros, $idEa, $request->categoria)
            ->orderBy('lab.nombre_lab');

        return datatables()
            ->of($query)
            ->editColumn('updated_at', fn($row) => $row->updated_at ? formatDate($row->updated_at) : '-')
            ->editColumn('publicado', fn($row) => $row->publicado ? 'Sí' : 'No')
            ->editColumn('temporal', fn($row) => $row->temporal ? 'En revisión' : 'Confirmado')
            ->editColumn('calificacion_certificado', fn($row) => $row->calificacion_certificado ?? DistribucionEnsayosCalculator::NO_SATISFACTORIO)
            ->toJson();
    }

    public function distribucionEnsayosExport(Request $request, DistribucionEnsayosCalculator $calculator)
    {
        $this->autorizar();
        $filtros = $this->filtrosDesdeRequest($request);

        $gestion = $filtros['gestion'] ?: 'todas';
        $nombre = "distribucion-ensayos-{$gestion}.xlsx";

        return Excel::download(new DistribucionEnsayosExport($filtros, $calculator), $nombre);
    }

    protected function construirDatasets(Collection $datos, array $categorias): array
    {
        $paletaIdx = 0;
        $datasets = [];
        foreach ($categorias as $cat) {
            $color = self::COLORES_CONOCIDOS[$cat] ?? self::PALETA_FALLBACK[$paletaIdx++ % count(self::PALETA_FALLBACK)];
            $datasets[] = [
                'label' => $cat,
                'categoria' => $cat,
                'backgroundColor' => $color,
                'data' => $datos->map(fn($fila) => (int) ($fila['calificaciones'][$cat] ?? 0))->all(),
            ];
        }
        return $datasets;
    }

    protected function autorizar(): void
    {
        if (!Gate::any([
            Permiso::GESTION_CERTIFICADOS,
            Permiso::ADMIN,
            Permiso::JEFE_PEEC,
            Permiso::RESPONSABLE,
        ])) {
            abort(403, 'No tiene autorización para acceder a este reporte.');
        }
    }

    protected function ensayosPermitidos(): ?array
    {
        $user = Auth::user();
        $tienePrivilegios = Gate::any([
            Permiso::ADMIN,
            Permiso::JEFE_PEEC,
            Permiso::GESTION_CERTIFICADOS,
        ]);
        if ($tienePrivilegios) {
            return null;
        }
        if (Gate::allows(Permiso::RESPONSABLE)) {
            return $user->responsablesEA->pluck('id')->map(fn($v) => (int) $v)->all();
        }
        return [];
    }

    protected function areasVisibles(?array $ensayosPermitidos)
    {
        $query = Area::with('programa')->orderBy('descripcion');
        if ($ensayosPermitidos === null) {
            $areas = $query->get();
        } elseif (empty($ensayosPermitidos)) {
            return collect();
        } else {
            $areas = $query->whereHas('paquetes.ensayosAptitud', function ($q) use ($ensayosPermitidos) {
                $q->whereIn('id', $ensayosPermitidos);
            })->get();
        }

        $repetidas = $areas->groupBy('descripcion')->filter(fn($g) => $g->count() > 1)->keys();
        return $areas->map(fn($a) => (object) [
            'id' => $a->id,
            'descripcion' => $repetidas->contains($a->descripcion) && $a->programa
                ? "{$a->descripcion} ({$a->programa->descripcion})"
                : $a->descripcion,
        ]);
    }

    protected function paquetesVisibles(?array $ensayosPermitidos)
    {
        $query = Paquete::with('area.programa')->orderBy('descripcion');
        if ($ensayosPermitidos === null) {
            $paquetes = $query->get();
        } elseif (empty($ensayosPermitidos)) {
            return collect();
        } else {
            $paquetes = $query->whereHas('ensayosAptitud', function ($q) use ($ensayosPermitidos) {
                $q->whereIn('id', $ensayosPermitidos);
            })->get();
        }

        $repetidos = $paquetes->groupBy('descripcion')->filter(fn($g) => $g->count() > 1)->keys();
        return $paquetes->map(fn($p) => (object) [
            'id' => $p->id,
            'id_area' => $p->id_area,
            'descripcion' => $repetidos->contains($p->descripcion) && $p->area
                ? "{$p->descripcion} ({$p->area->descripcion})"
                : $p->descripcion,
        ]);
    }

    protected function filtrosDesdeRequest(Request $request): array
    {
        $gestion = $request->input('gestion');
        if (!$gestion) {
            $gestion = DB::table('certificado')->max('gestion_certificado') ?: now()->year;
        }

        $filtros = [
            'gestion' => $gestion,
            'id_area' => $request->input('id_area') ?: null,
            'id_paquete' => $request->input('id_paquete') ?: null,
            'estado' => $request->input('estado', 'publicados'),
        ];

        $ensayosPermitidos = $this->ensayosPermitidos();
        if ($ensayosPermitidos !== null) {
            $filtros['ensayos_permitidos'] = $ensayosPermitidos;
        }
        return $filtros;
    }
}
