<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notificacion extends Model
{
    protected $table = 'notificacion';

    const TIPO_OBSERVACION_INSCRIPCION = 'observacion_inscripcion';
    const TIPO_CERTIFICADO_DISPONIBLE = 'certificado_disponible';
    const TIPO_DEUDA_PENDIENTE = 'deuda_pendiente';
    const TIPO_GENERAL = 'general';

    const MODO_UNA_VEZ = 'una_vez';
    const MODO_PERSISTENTE = 'persistente';

    const ENTIDAD_INSCRIPCION = 'inscripcion';
    const ENTIDAD_CERTIFICADO = 'certificado';
    const ENTIDAD_PAGO = 'pago';

    const TITULOS = [
        self::TIPO_OBSERVACION_INSCRIPCION => 'Su inscripción tiene observaciones',
        // self::TIPO_CERTIFICADO_DISPONIBLE  => 'Certificado disponible',  // no integrado aún — ocultar de filtros
        // self::TIPO_DEUDA_PENDIENTE         => 'Tiene una deuda pendiente', // no integrado aún — ocultar de filtros
        self::TIPO_GENERAL                 => 'Notificación',
    ];

    const ICONOS = [
        self::TIPO_OBSERVACION_INSCRIPCION => 'fas fa-exclamation-triangle',
        // self::TIPO_CERTIFICADO_DISPONIBLE  => 'fas fa-award',
        // self::TIPO_DEUDA_PENDIENTE         => 'fas fa-money-bill-wave',
        self::TIPO_GENERAL                 => 'fas fa-bell',
    ];

    const COLORES = [
        self::TIPO_OBSERVACION_INSCRIPCION => 'yellow',
        // self::TIPO_CERTIFICADO_DISPONIBLE  => 'green',
        // self::TIPO_DEUDA_PENDIENTE         => 'red',
        self::TIPO_GENERAL                 => 'indigo',
    ];

    protected $fillable = [
        'tipo',
        'mensaje',
        'entidad_tipo',
        'entidad_id',
        'modo',
        'fecha_inicio',
        'fecha_fin',
        'resuelta_at',
        'created_by',
    ];

    protected $casts = [
        'mensaje'      => 'array',
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
        'resuelta_at'  => 'datetime',
    ];

    public function recepciones(): HasMany
    {
        return $this->hasMany(NotificacionLab::class, 'id_notificacion');
    }

    public function laboratorios(): BelongsToMany
    {
        return $this->belongsToMany(Laboratorio::class, 'notificacion_lab', 'id_notificacion', 'id_lab')
            ->withPivot(['read_at', 'resuelta_at'])
            ->withTimestamps();
    }

    public function entidad(): MorphTo
    {
        return $this->morphTo('entidad', 'entidad_tipo', 'entidad_id');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeDeTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeDeEntidad($query, $entidadTipo, $entidadId)
    {
        return $query->where('entidad_tipo', $entidadTipo)
                     ->where('entidad_id', $entidadId);
    }

    public function scopeNoResueltas($query)
    {
        return $query->whereNull('resuelta_at');
    }

    public function scopeVigentes($query)
    {
        $hoy = now()->toDateString();
        return $query->where(function ($q) use ($hoy) {
            $q->whereNull('fecha_inicio')->orWhereDate('fecha_inicio', '<=', $hoy);
        })->where(function ($q) use ($hoy) {
            $q->whereNull('fecha_fin')->orWhereDate('fecha_fin', '>=', $hoy);
        });
    }

    public function getTituloAttribute(): string
    {
        return $this->mensaje['titulo'] ?? self::TITULOS[$this->tipo] ?? 'Notificación';
    }

    public function getIconoAttribute(): string
    {
        return self::ICONOS[$this->tipo] ?? 'fas fa-bell';
    }

    public function getColorAttribute(): string
    {
        return self::COLORES[$this->tipo] ?? 'indigo';
    }

    public function getUrlAccionAttribute(): ?string
    {
        if (!$this->entidad_tipo || !$this->entidad_id) return null;
        return match ($this->entidad_tipo) {
            self::ENTIDAD_INSCRIPCION => route('lab.inscripcion.show', $this->entidad_id),
            default => null,
        };
    }

    public function getUrlAdminAccionAttribute(): ?string
    {
        if (!$this->entidad_tipo || !$this->entidad_id) return null;
        return match ($this->entidad_tipo) {
            self::ENTIDAD_INSCRIPCION => route('inscripcion_paquete.show', $this->entidad_id),
            default => null,
        };
    }

    public function getEsRecurrenteAttribute(): bool
    {
        return $this->modo === self::MODO_PERSISTENTE;
    }

    public function getEstaVigenteAttribute(): bool
    {
        $hoy = now()->toDateString();
        if ($this->fecha_inicio && $this->fecha_inicio->toDateString() > $hoy) return false;
        if ($this->fecha_fin && $this->fecha_fin->toDateString() < $hoy) return false;
        return true;
    }
}
