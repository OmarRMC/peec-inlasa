<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoriaLaboratorio;
use App\Models\Departamento;
use App\Models\Inscripcion;
use App\Models\Laboratorio;
use App\Models\Municipio;
use App\Models\Notificacion;
use App\Models\NotificacionLab;
use App\Models\Pais;
use App\Models\Permiso;
use App\Models\TipoLaboratorio;
use App\Models\User;
use App\Services\NotificacionLabService;
use Illuminate\Http\Request;

class NotificacionAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('canany:' . implode(',', [
            Permiso::ADMIN,
            Permiso::GESTION_NOTIFICACIONES,
            Permiso::JEFE_PEEC,
        ]));
    }

    public function index(Request $request)
    {
        $query = NotificacionLab::query()
            ->with([
                'laboratorio:id,nombre_lab,cod_lab',
                'notificacion',
                'notificacion.creador:id,nombre,ap_paterno,ap_materno,email',
            ])
            ->orderByDesc('created_at');

        if ($tipo = $request->input('tipo')) {
            $query->deTipo($tipo);
        }

        if ($estado = $request->input('estado')) {
            match ($estado) {
                'pendiente' => $query->pendientes(),
                'leida'     => $query->whereNotNull('read_at')->whereNull('resuelta_at')
                                    ->whereHas('notificacion', fn($q) => $q->whereNull('resuelta_at')),
                'resuelta'  => $query->where(function ($q) {
                    $q->whereNotNull('resuelta_at')
                      ->orWhereHas('notificacion', fn($n) => $n->whereNotNull('resuelta_at'));
                }),
                default     => null,
            };
        }

        if ($desde = $request->input('fecha_desde')) {
            $query->whereHas('notificacion', fn($n) => $n->whereDate('created_at', '>=', $desde));
        }
        if ($hasta = $request->input('fecha_hasta')) {
            $query->whereHas('notificacion', fn($n) => $n->whereDate('created_at', '<=', $hasta));
        }

        if ($creadoPor = $request->input('creado_por')) {
            $query->whereHas('notificacion', fn($n) => $n->where('created_by', $creadoPor));
        }

        if ($q = trim($request->input('q', ''))) {
            $query->whereHas('laboratorio', function ($qq) use ($q) {
                $qq->where('nombre_lab', 'like', "%{$q}%")
                   ->orWhere('cod_lab', 'like', "%{$q}%");
            });
        }

        $notificaciones = $query->paginate(25)->withQueryString();

        $notificaciones->getCollection()->transform(function (NotificacionLab $n) {
            $n->html_mensaje = NotificacionLabService::renderMensaje($n);
            return $n;
        });

        $admins = User::whereHas('permisos', function ($q) {
            $q->whereIn('clave', [
                Permiso::ADMIN,
                Permiso::GESTION_INSCRIPCIONES,
                Permiso::GESTION_LABORATORIO,
                Permiso::JEFE_PEEC,
            ]);
        })->orderBy('nombre')->get(['id', 'nombre', 'ap_paterno', 'ap_materno', 'email']);

        return view('admin.notificaciones.index', [
            'notificaciones' => $notificaciones,
            'admins'         => $admins,
            'tipos'          => NotificacionLab::TITULOS,
            'filtros'        => [
                'tipo'        => $tipo ?? null,
                'estado'      => $estado ?? null,
                'fecha_desde' => $desde ?? null,
                'fecha_hasta' => $hasta ?? null,
                'creado_por'  => $creadoPor ?? null,
                'q'           => $q ?? null,
            ],
        ]);
    }

    public function create(Request $request)
    {
        $labs = Laboratorio::where('status', Laboratorio::ACTIVE_STATUS)
            ->orderBy('nombre_lab')
            ->get(['id', 'nombre_lab', 'cod_lab', 'id_pais', 'id_dep', 'id_municipio', 'id_tipo', 'id_categoria']);

        // IDs de labs con al menos una inscripción deudora (status_cuenta = STATUS_DEUDOR).
        // Mismo criterio usado para impedir nuevas inscripciones (Inscripcion::esDeudor).
        $idsDeudores = Inscripcion::where('status_cuenta', Inscripcion::STATUS_DEUDOR)
            ->distinct()
            ->pluck('id_lab')
            ->all();
        $setDeudores = array_flip($idsDeudores);

        $labs->each(function ($lab) use ($setDeudores) {
            $lab->deudor = isset($setDeudores[$lab->id]);
        });

        $paises = Pais::orderBy('nombre_pais')->get(['id', 'nombre_pais']);
        $departamentos = Departamento::orderBy('nombre_dep')->get(['id', 'nombre_dep']);
        $municipios = Municipio::orderBy('nombre_municipio')->get(['id', 'nombre_municipio']);
        $tipos = TipoLaboratorio::orderBy('descripcion')->get(['id', 'descripcion']);
        $categorias = CategoriaLaboratorio::orderBy('descripcion')->get(['id', 'descripcion']);

        $preseleccionados = [];
        if ($idLab = $request->input('id_lab')) {
            $preseleccionados[] = (int) $idLab;
        }

        return view('admin.notificaciones.create', [
            'labs'             => $labs,
            'paises'           => $paises,
            'departamentos'    => $departamentos,
            'municipios'       => $municipios,
            'tipos'            => $tipos,
            'categorias'       => $categorias,
            'preseleccionados' => $preseleccionados,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_labs'      => 'required|array|min:1',
            'id_labs.*'    => 'integer|exists:laboratorio,id',
            'titulo'       => 'required|string|max:255',
            'mensaje'      => 'required|string',
            'modo'         => 'required|in:' . NotificacionLab::MODO_UNA_VEZ . ',' . NotificacionLab::MODO_PERSISTENTE,
            'fecha_inicio' => 'nullable|date|required_if:modo,' . NotificacionLab::MODO_PERSISTENTE,
            'fecha_fin'    => 'nullable|date|required_if:modo,' . NotificacionLab::MODO_PERSISTENTE . '|after_or_equal:fecha_inicio',
        ]);

        $count = NotificacionLabService::notificarGeneralAVarios(
            $data['id_labs'],
            $data['titulo'],
            $data['mensaje'],
            $data['modo'],
            null,
            $data['fecha_inicio'] ?? null,
            $data['fecha_fin'] ?? null
        );

        return redirect()
            ->route('admin.notificaciones.index')
            ->with('success', "Notificación enviada a {$count} laboratorio(s).");
    }

    public function resolver(Request $request, $id)
    {
        $recepcion = NotificacionLab::with('notificacion')->findOrFail($id);

        if ($recepcion->notificacion?->modo !== Notificacion::MODO_PERSISTENTE) {
            return back()->with('warning', 'Solo se pueden resolver notificaciones persistentes.');
        }

        if (!is_null($recepcion->resuelta_at)) {
            return back()->with('info', 'La notificación ya estaba resuelta para este laboratorio.');
        }

        NotificacionLabService::resolverPorId($recepcion->id);

        return back()->with('success', 'Notificación marcada como resuelta para este laboratorio.');
    }

    public function edit($id)
    {
        $notificacion = Notificacion::with([
            'creador:id,nombre,ap_paterno,ap_materno,email',
            'recepciones.laboratorio:id,nombre_lab,cod_lab',
        ])->findOrFail($id);

        return view('admin.notificaciones.edit', [
            'notificacion' => $notificacion,
        ]);
    }

    public function update(Request $request, $id)
    {
        $notificacion = Notificacion::findOrFail($id);

        $data = $request->validate([
            'titulo'       => 'required|string|max:255',
            'mensaje'      => 'required|string',
            'modo'         => 'required|in:' . Notificacion::MODO_UNA_VEZ . ',' . Notificacion::MODO_PERSISTENTE,
            'fecha_inicio' => 'nullable|date|required_if:modo,' . Notificacion::MODO_PERSISTENTE,
            'fecha_fin'    => 'nullable|date|required_if:modo,' . Notificacion::MODO_PERSISTENTE . '|after_or_equal:fecha_inicio',
        ]);

        // Preservar el resto del payload (documentos, url_descarga, etc. en otros tipos)
        $mensaje = $notificacion->mensaje ?? [];
        $mensaje['titulo'] = $data['titulo'];
        $mensaje['html'] = $data['mensaje'];

        $notificacion->update([
            'mensaje'      => $mensaje,
            'modo'         => $data['modo'],
            'fecha_inicio' => $data['fecha_inicio'] ?? null,
            'fecha_fin'    => $data['fecha_fin'] ?? null,
        ]);

        return redirect()
            ->route('admin.notificaciones.edit', $notificacion->id)
            ->with('success', 'Notificación actualizada. Los cambios aplican a todos los destinatarios.');
    }

    public function resolverTodos(Request $request, $id)
    {
        $notificacion = Notificacion::findOrFail($id);

        if (!is_null($notificacion->resuelta_at)) {
            return back()->with('info', 'La notificación ya estaba resuelta para todos.');
        }

        NotificacionLabService::resolverParaTodos($notificacion->id);

        return redirect()
            ->route('admin.notificaciones.index')
            ->with('success', 'Notificación resuelta para todos los destinatarios.');
    }
}
