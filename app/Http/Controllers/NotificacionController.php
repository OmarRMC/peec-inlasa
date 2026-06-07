<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use App\Models\NotificacionLab;
use App\Services\NotificacionLabService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacionController extends Controller
{
    /**
     * Devuelve las notificaciones pendientes para mostrar como pop-up.
     *
     * Memoria por sesión Laravel: cada notificación cuyo id ya fue entregado
     * en esta sesión se omite, así no reaparece en cada navegación SSR.
     * Al iniciar una nueva sesión (re-login), la lista vuelve a calcularse
     * desde cero, por lo que las persistentes no resueltas reaparecen.
     */
    public function getNotificacionesPendientes(Request $request)
    {
        $user = Auth::user();
        $lab = $user?->laboratorio;

        if (!$lab) {
            return response()->json([]);
        }

        $mostradasEnSesion = $request->session()->get('notif_lab_mostradas', []);

        $pendientes = NotificacionLab::query()
            ->with('notificacion')
            ->paraLaboratorio($lab->id)
            ->pendientes()
            ->whereNotIn('id', $mostradasEnSesion)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($pendientes->isNotEmpty()) {
            $nuevasMostradas = array_values(array_unique(array_merge(
                $mostradasEnSesion,
                $pendientes->pluck('id')->all()
            )));
            $request->session()->put('notif_lab_mostradas', $nuevasMostradas);
        }

        $payload = $pendientes->map(function (NotificacionLab $n) {
            return [
                'id'          => $n->id,
                'tipo'        => $n->tipo,
                'titulo'      => $n->titulo,
                'icono'       => $n->icono,
                'color'       => $n->color,
                'url_accion'  => $n->url_accion,
                'modo'        => $n->modo,
                'recurrente'  => $n->es_recurrente,
                'leida'       => !is_null($n->leido_en),
                'html'        => NotificacionLabService::renderMensaje($n),
                'created_at'  => $n->created_at?->format('d/m/Y H:i'),
            ];
        })->all();

        if (!in_array('global', $mostradasEnSesion, true)) {
            $global = $this->notificacionGlobalLegacy($lab);
            if ($global) {
                $payload[] = $global;
                $actual = $request->session()->get('notif_lab_mostradas', []);
                $request->session()->put(
                    'notif_lab_mostradas',
                    array_values(array_unique(array_merge($actual, ['global'])))
                );
            }
        }

        return response()->json($payload);
    }

    /**
     * Sistema legacy de notificación global configurable desde Configuracion
     * (NOTIFICACION_TITULO / DESCRIPCION / MENSAJE / KEY / FECHA_*). Aparece a
     * todos los labs mientras la fecha actual esté en rango y su key no
     * coincida con la última que el lab confirmó leer (laboratorio.notificacion_key).
     */
    private function notificacionGlobalLegacy($lab): ?array
    {
        if (!$lab || !$lab->tieneNotificacionPendiente()) {
            return null;
        }

        $titulo      = Configuracion::where('key', Configuracion::NOTIFICACION_TITULO)->value('valor');
        $descripcion = Configuracion::where('key', Configuracion::NOTIFICACION_DESCRIPCION)->value('valor');
        $mensaje     = Configuracion::where('key', Configuracion::NOTIFICACION_MENSAJE)->value('valor');
        $clave       = Configuracion::where('key', Configuracion::NOTIFICACION_KEY)->value('valor');

        $html = '';
        if ($descripcion) {
            $html .= '<p class="text-gray-600 mb-3">' . e($descripcion) . '</p>';
        }
        if ($mensaje) {
            $html .= '<div class="text-gray-800">' . $mensaje . '</div>';
        }

        return [
            'id'            => 'global',
            'tipo'          => NotificacionLab::TIPO_GENERAL,
            'titulo'        => $titulo ?: 'Aviso',
            'descripcion'   => $descripcion,
            'icono'         => 'fas fa-bullhorn',
            'color'         => 'indigo',
            'url_accion'    => null,
            'modo'          => NotificacionLab::MODO_PERSISTENTE,
            'recurrente'    => true,
            'leida'         => false,
            'html'          => $mensaje ?: '',
            'created_at'    => null,
            'clave_global'  => $clave,
            'adaptable'     => true,
            'sin_acciones'  => true,
        ];
    }

    public function marcarLeida(Request $request, $id)
    {
        $user = Auth::user();
        $lab = $user?->laboratorio;

        if (!$lab) {
            return response()->json(['ok' => false, 'error' => 'no_lab'], 403);
        }

        if ($id === 'global') {
            // La global es persistente: el cierre solo la silencia en esta sesión
            // (gestionado por session('notif_lab_mostradas')). Vuelve a aparecer
            // en el próximo login mientras la NOTIFICACION_KEY/fechas no cambien.
            return response()->json(['ok' => true]);
        }

        $notif = NotificacionLab::where('id', $id)
            ->where('id_lab', $lab->id)
            ->first();

        if (!$notif) {
            return response()->json(['ok' => false, 'error' => 'not_found'], 404);
        }

        if (is_null($notif->leido_en)) {
            $notif->leido_en = now();
            $notif->save();
        }

        return response()->json(['ok' => true]);
    }

    public function bandeja(Request $request)
    {
        $user = Auth::user();
        $lab = $user?->laboratorio;

        $query = NotificacionLab::query()
            ->with('notificacion')
            ->paraLaboratorio($lab?->id ?? 0)
            ->orderBy('created_at', 'desc');

        if ($filtroTipo = $request->input('tipo')) {
            $query->deTipo($filtroTipo);
        }

        if ($estado = $request->input('estado')) {
            match ($estado) {
                'no_leidas' => $query->whereNull('leido_en')->whereNull('resuelta_en'),
                'leidas'    => $query->whereNotNull('leido_en')->whereNull('resuelta_en'),
                'resueltas' => $query->whereNotNull('resuelta_en'),
                default     => null,
            };
        }

        $notificaciones = $query->paginate(20)->withQueryString();

        $notificaciones->getCollection()->transform(function (NotificacionLab $n) {
            $n->html_mensaje = NotificacionLabService::renderMensaje($n);
            return $n;
        });

        return view('laboratorio.notificaciones', [
            'notificaciones' => $notificaciones,
            'filtroTipo'     => $filtroTipo ?? null,
            'estado'         => $estado ?? null,
            'tipos'          => NotificacionLab::TITULOS,
        ]);
    }
}
