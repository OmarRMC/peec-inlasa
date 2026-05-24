<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Pivote entre Notificacion (plantilla) y Laboratorio (destinatario).
 * Mantiene el seguimiento por lab: read_at (cuando el lab cerró el popup)
 * y resuelta_at (cuando se cerró específicamente para este lab).
 *
 * Accessors delegados: cuando una vista usa $recepcion->titulo, $recepcion->html, etc.,
 * la propiedad se obtiene de la plantilla relacionada - así las vistas no necesitan
 * saber si están leyendo de la plantilla o del pivote.
 */
class NotificacionLab extends Model
{
    protected $table = 'notificacion_lab';

    // Re-export de constantes para compatibilidad con código que las usa por NotificacionLab::
    const TIPO_OBSERVACION_INSCRIPCION = Notificacion::TIPO_OBSERVACION_INSCRIPCION;
    const TIPO_CERTIFICADO_DISPONIBLE  = Notificacion::TIPO_CERTIFICADO_DISPONIBLE;
    const TIPO_DEUDA_PENDIENTE         = Notificacion::TIPO_DEUDA_PENDIENTE;
    const TIPO_GENERAL                 = Notificacion::TIPO_GENERAL;
    const MODO_UNA_VEZ                 = Notificacion::MODO_UNA_VEZ;
    const MODO_PERSISTENTE             = Notificacion::MODO_PERSISTENTE;
    const ENTIDAD_INSCRIPCION          = Notificacion::ENTIDAD_INSCRIPCION;
    const ENTIDAD_CERTIFICADO          = Notificacion::ENTIDAD_CERTIFICADO;
    const ENTIDAD_PAGO                 = Notificacion::ENTIDAD_PAGO;
    const TITULOS                      = Notificacion::TITULOS;
    const ICONOS                       = Notificacion::ICONOS;
    const COLORES                      = Notificacion::COLORES;

    protected $fillable = [
        'id_notificacion',
        'id_lab',
        'read_at',
        'resuelta_at',
    ];

    protected $casts = [
        'read_at'     => 'datetime',
        'resuelta_at' => 'datetime',
    ];

    public function notificacion(): BelongsTo
    {
        return $this->belongsTo(Notificacion::class, 'id_notificacion');
    }

    public function laboratorio(): BelongsTo
    {
        return $this->belongsTo(Laboratorio::class, 'id_lab');
    }

    public function scopeParaLaboratorio($query, $idLab)
    {
        return $query->where('id_lab', $idLab);
    }

    /**
     * Recepciones pendientes para mostrar al lab. Combina:
     *   - notificacion no resuelta globalmente
     *   - notificacion vigente por fechas
     *   - recepción no resuelta para este lab
     *   - si modo=una_vez: no leída (read_at IS NULL); si persistente: ignora read_at
     */
    public function scopePendientes($query)
    {
        $hoy = now()->toDateString();

        return $query
            ->whereHas('notificacion', function ($q) use ($hoy) {
                $q->whereNull('resuelta_at')
                  ->where(function ($qq) use ($hoy) {
                      $qq->whereNull('fecha_inicio')->orWhereDate('fecha_inicio', '<=', $hoy);
                  })
                  ->where(function ($qq) use ($hoy) {
                      $qq->whereNull('fecha_fin')->orWhereDate('fecha_fin', '>=', $hoy);
                  });
            })
            ->whereNull('resuelta_at')
            ->where(function ($q) {
                $q->whereNull('read_at')
                  ->orWhereHas('notificacion', fn($n) => $n->where('modo', Notificacion::MODO_PERSISTENTE));
            });
    }

    public function scopeDeTipo($query, $tipo)
    {
        return $query->whereHas('notificacion', fn($q) => $q->where('tipo', $tipo));
    }

    public function scopeDeEntidad($query, $entidadTipo, $entidadId)
    {
        return $query->whereHas('notificacion', fn($q) =>
            $q->where('entidad_tipo', $entidadTipo)->where('entidad_id', $entidadId)
        );
    }

    // ---------- Accessors delegados a la plantilla ----------
    public function getTipoAttribute(): ?string         { return $this->notificacion?->tipo; }
    public function getMensajeAttribute(): array        { return $this->notificacion?->mensaje ?? []; }
    public function getModoAttribute(): ?string         { return $this->notificacion?->modo; }
    public function getEntidadTipoAttribute(): ?string  { return $this->notificacion?->entidad_tipo; }
    public function getEntidadIdAttribute()             { return $this->notificacion?->entidad_id; }
    public function getFechaInicioAttribute()           { return $this->notificacion?->fecha_inicio; }
    public function getFechaFinAttribute()              { return $this->notificacion?->fecha_fin; }
    public function getTituloAttribute(): string        { return $this->notificacion?->titulo ?? 'Notificación'; }
    public function getIconoAttribute(): string         { return $this->notificacion?->icono ?? 'fas fa-bell'; }
    public function getColorAttribute(): string         { return $this->notificacion?->color ?? 'indigo'; }
    public function getUrlAccionAttribute(): ?string    { return $this->notificacion?->url_accion; }
    public function getUrlAdminAccionAttribute(): ?string { return $this->notificacion?->url_admin_accion; }
    public function getEsRecurrenteAttribute(): bool    { return (bool) $this->notificacion?->es_recurrente; }
    public function getEstaVigenteAttribute(): bool     { return (bool) $this->notificacion?->esta_vigente; }
    public function getCreadorAttribute()               { return $this->notificacion?->creador; }
}
