<?php

namespace App\Services;

use App\Models\Inscripcion;
use App\Models\Laboratorio;
use App\Models\Notificacion;
use App\Models\NotificacionLab;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class NotificacionLabService
{
    /**
     * Crea una plantilla de notificación + sus recepciones por lab destinatario.
     * Acepta:
     *   id_lab (int)  → 1 sola recepción
     *   id_labs (int[]) → N recepciones
     * Devuelve la plantilla creada (Notificacion).
     */
    public static function crear(array $data): Notificacion
    {
        $entidadTipo = $data['entidad']['tipo'] ?? null;
        $entidadId   = $data['entidad']['id'] ?? null;

        $idLabs = $data['id_labs'] ?? (isset($data['id_lab']) ? [$data['id_lab']] : []);
        $idLabs = array_values(array_unique(array_filter(array_map('intval', $idLabs))));

        return DB::transaction(function () use ($data, $entidadTipo, $entidadId, $idLabs) {
            $notificacion = Notificacion::create([
                'tipo'         => $data['tipo'],
                'mensaje'      => $data['mensaje'] ?? [],
                'entidad_tipo' => $entidadTipo,
                'entidad_id'   => $entidadId,
                'modo'         => $data['modo'] ?? Notificacion::MODO_UNA_VEZ,
                'fecha_inicio' => $data['fecha_inicio'] ?? null,
                'fecha_fin'    => $data['fecha_fin'] ?? null,
                'created_by'   => $data['created_by'] ?? (Auth::id() ?? 1),
            ]);

            if (!empty($idLabs)) {
                $ahora = now();
                $filas = array_map(fn($id) => [
                    'id_notificacion' => $notificacion->id,
                    'id_lab'          => $id,
                    'leido_en'        => null,
                    'resuelta_en'     => null,
                    'created_at'      => $ahora,
                    'updated_at'      => $ahora,
                ], $idLabs);
                NotificacionLab::insert($filas);
            }

            return $notificacion;
        });
    }

    /**
     * Notifica una observación a un lab. Aplica idempotencia anti doble-submit:
     * si ya existe una plantilla pendiente del mismo tipo+entidad creada en
     * los últimos 10s, la devuelve sin crear duplicado.
     */
    public static function notificarObservacionInscripcion(
        Inscripcion $ins,
        array $observaciones,
        string $modo = Notificacion::MODO_PERSISTENTE,
        ?string $comentario = null
    ): Notificacion {
        $mensaje = ['documentos' => $observaciones];
        if ($comentario) $mensaje['comentario'] = $comentario;

        $reciente = Notificacion::deTipo(Notificacion::TIPO_OBSERVACION_INSCRIPCION)
            ->deEntidad(Notificacion::ENTIDAD_INSCRIPCION, $ins->id)
            ->whereHas('recepciones', fn($q) => $q->whereNull('resuelta_en'))
            ->where('created_at', '>=', now()->subSeconds(10))
            ->orderByDesc('id')
            ->first();

        if ($reciente) return $reciente;

        return self::crear([
            'id_lab'  => $ins->id_lab,
            'tipo'    => Notificacion::TIPO_OBSERVACION_INSCRIPCION,
            'mensaje' => $mensaje,
            'entidad' => [
                'tipo' => Notificacion::ENTIDAD_INSCRIPCION,
                'id'   => $ins->id,
            ],
            'modo'    => $modo,
        ]);
    }

    public static function notificarCertificadoDisponible(
        Laboratorio $lab,
        $certificado,
        string $urlDescarga,
        ?string $ensayo = null
    ): Notificacion {
        return self::crear([
            'id_lab'  => $lab->id,
            'tipo'    => Notificacion::TIPO_CERTIFICADO_DISPONIBLE,
            'mensaje' => ['url_descarga' => $urlDescarga, 'ensayo' => $ensayo],
            'entidad' => [
                'tipo' => Notificacion::ENTIDAD_CERTIFICADO,
                'id'   => is_object($certificado) ? $certificado->id : $certificado,
            ],
            'modo'    => Notificacion::MODO_UNA_VEZ,
        ]);
    }

    public static function notificarDeudaPendiente(
        Inscripcion $ins,
        float $monto,
        ?string $vencimiento = null
    ): Notificacion {
        return self::crear([
            'id_lab'  => $ins->id_lab,
            'tipo'    => Notificacion::TIPO_DEUDA_PENDIENTE,
            'mensaje' => ['monto' => $monto, 'vencimiento' => $vencimiento],
            'entidad' => [
                'tipo' => Notificacion::ENTIDAD_INSCRIPCION,
                'id'   => $ins->id,
            ],
            'modo'    => Notificacion::MODO_PERSISTENTE,
        ]);
    }

    public static function notificarGeneral(
        int $idLab,
        string $html,
        string $modo = Notificacion::MODO_UNA_VEZ,
        ?string $titulo = null
    ): Notificacion {
        $mensaje = ['html' => $html];
        if ($titulo) $mensaje['titulo'] = $titulo;

        return self::crear([
            'id_lab'  => $idLab,
            'tipo'    => Notificacion::TIPO_GENERAL,
            'mensaje' => $mensaje,
            'modo'    => $modo,
        ]);
    }

    /**
     * Envía una notificación general a varios labs. 1 plantilla + N recepciones.
     * Devuelve la cantidad de recepciones creadas.
     */
    public static function notificarGeneralAVarios(
        array $idLabs,
        string $titulo,
        string $html,
        string $modo = Notificacion::MODO_UNA_VEZ,
        ?int $createdBy = null,
        ?string $fechaInicio = null,
        ?string $fechaFin = null
    ): int {
        $idLabs = array_values(array_unique(array_filter($idLabs)));
        if (empty($idLabs)) return 0;

        self::crear([
            'id_labs'      => $idLabs,
            'tipo'         => Notificacion::TIPO_GENERAL,
            'mensaje'      => ['titulo' => $titulo, 'html' => $html],
            'modo'         => $modo,
            'created_by'   => $createdBy,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin'    => $fechaFin,
        ]);

        return count($idLabs);
    }

    /**
     * Resuelve todas las recepciones pendientes de las notificaciones que matcheen
     * tipo+entidad. Setea notificacion_lab.resuelta_en = now() en cada lab afectado,
     * por lo que dejan de aparecer como pendientes. Devuelve la cantidad de
     * recepciones resueltas.
     */
    public static function resolver(string $tipo, string $entidadTipo, int $entidadId): int
    {
        $ids = Notificacion::deTipo($tipo)
            ->deEntidad($entidadTipo, $entidadId)
            ->pluck('id');

        if ($ids->isEmpty()) return 0;

        return NotificacionLab::whereIn('id_notificacion', $ids)
            ->whereNull('resuelta_en')
            ->update(['resuelta_en' => now()]);
    }

    /**
     * Resuelve las recepciones pendientes de un SOLO laboratorio para las notificaciones
     * que matcheen tipo+entidad. Útil cuando una acción del propio lab debe cerrar su
     * notificación persistente (ej: el lab envía resultados → se resuelve su recordatorio),
     * sin afectar a los demás destinatarios. Devuelve la cantidad de recepciones resueltas.
     */
    public static function resolverParaLab(string $tipo, string $entidadTipo, int $entidadId, int $idLab): int
    {
        $ids = Notificacion::deTipo($tipo)
            ->deEntidad($entidadTipo, $entidadId)
            ->pluck('id');

        if ($ids->isEmpty()) return 0;

        return NotificacionLab::whereIn('id_notificacion', $ids)
            ->where('id_lab', $idLab)
            ->whereNull('resuelta_en')
            ->update(['resuelta_en' => now()]);
    }

    /**
     * Resuelve una notificación para TODOS sus destinatarios.
     * Para el botón "Resolver para todos" en la vista edit.
     */
    public static function resolverParaTodos(int $idNotificacion): bool
    {
        $n = Notificacion::find($idNotificacion);
        if (!$n) return false;

        NotificacionLab::where('id_notificacion', $n->id)
            ->whereNull('resuelta_en')
            ->update(['resuelta_en' => now()]);

        return true;
    }

    /**
     * Resuelve UNA recepción específica (para un lab puntual).
     * Para el botón "Resolver" individual en la bandeja admin.
     */
    public static function resolverPorId(int $idRecepcion): bool
    {
        $r = NotificacionLab::find($idRecepcion);
        if (!$r) return false;
        if (!is_null($r->resuelta_en)) return true;
        $r->resuelta_en = now();
        return (bool) $r->save();
    }

    /**
     * Renderiza el HTML del mensaje. Recibe una Notificacion (plantilla) o
     * una NotificacionLab (pivote - usa su accessor delegado mensaje).
     */
    public static function renderMensaje($notif): string
    {
        $mensaje = $notif instanceof Notificacion
            ? ($notif->mensaje ?? [])
            : ($notif?->mensaje ?? []);

        if (!empty($mensaje['html'])) {
            return self::sanitizarHtml($mensaje['html']);
        }

        $tipo = $notif instanceof Notificacion ? $notif->tipo : $notif?->tipo;
        $vista = 'notificaciones.tipos.' . ($tipo ?: 'general');
        if (!View::exists($vista)) {
            $vista = 'notificaciones.tipos.general';
        }

        return View::make($vista, ['mensaje' => $mensaje, 'notif' => $notif])->render();
    }

    /**
     * Sanitiza HTML con la misma lista blanca de tags usada en las notificaciones in-app.
     * Punto de reutilización para los correos (notificación manual, etc.).
     */
    public static function sanitizar(string $html): string
    {
        return self::sanitizarHtml($html);
    }

    private static function sanitizarHtml(string $html): string
    {
        $permitidos = '<p><br><hr><strong><em><b><i><u><s><strike><sub><sup>'
            . '<a><img><span><div><font>'
            . '<h1><h2><h3><h4><h5><h6>'
            . '<ul><ol><li><dl><dt><dd>'
            . '<table><thead><tbody><tfoot><tr><th><td><caption><colgroup><col>'
            . '<blockquote><pre><code><mark><small>';
        return strip_tags($html, $permitidos);
    }
}
