<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    protected $table = 'inscripcion';

    const STATUS_EN_REVISION = 1;

    const STATUS_APROBADO = 2;

    const STATUS_VENCIDO = 3;

    const STATUS_ANULADO = 4;
    const STATUS_EN_OBSERVACION = 5;

    const STATUS_PAGADO = 1;

    const STATUS_DEUDOR = 2;

    const STATUS_INSCRIPCION = [
        self::STATUS_EN_REVISION => 'En revision',
        self::STATUS_APROBADO => 'Aprobado',
        self::STATUS_VENCIDO => 'Vencido',
        self::STATUS_ANULADO => 'Anulado',
        self::STATUS_EN_OBSERVACION => 'En observación'
    ];

    const STATUS_CUENTA = [
        self::STATUS_PAGADO => 'Pagado',
        self::STATUS_DEUDOR => 'Pendiente',
    ];

    protected $fillable = [
        'id_lab',
        'id_formulario',
        'cant_paq',
        'costo_total',
        'obs_inscripcion',
        'fecha_inscripcion',
        'status_cuenta',
        'status_inscripcion',
        'created_by',
        'updated_by',
        'fecha_limite_pago',
        'gestion'
    ];

    public function estaEnRevision(): bool
    {
        return $this->status_inscripcion === self::STATUS_EN_REVISION;
    }

    // Verifica si el estado es "Aprobado"
    public function estaAprobado(): bool
    {
        return $this->status_inscripcion === self::STATUS_APROBADO;
    }

    // Verifica si el estado es "Vencido"
    public function estaVencido(): bool
    {
        return $this->status_inscripcion === self::STATUS_VENCIDO;
    }


    public function estaAnulado(): bool
    {
        return $this->status_inscripcion === self::STATUS_ANULADO;
    }

    // Verifica si el estado de cuenta es "Pagado"
    public function estaPagado(): bool
    {
        return $this->status_cuenta === self::STATUS_PAGADO;
    }

    // Verifica si el estado de cuenta es "Deudor"
    public function esDeudor(): bool
    {
        return $this->status_cuenta === self::STATUS_DEUDOR;
    }
    public function laboratorio()
    {
        return $this->belongsTo(Laboratorio::class, 'id_lab');
    }

    public function formulario()
    {
        return $this->belongsTo(Formulario::class, 'id_formulario');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function detalleInscripciones()
    {
        return $this->hasMany(DetalleInscripcion::class, 'id_inscripcion');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'id_inscripcion');
    }

    public function documentos()
    {
        return $this->hasMany(DocumentoInscripcion::class, 'id_inscripcion');
    }

    public function documentosInscripcion()
    {
        return $this->hasMany(DocumentoInscripcion::class, 'id_inscripcion')
            ->where('tipo', DocumentoInscripcion::TYPE_DOCUMENTO_INSCRIPCION);
    }

    public function documentosPago()
    {
        return $this->hasMany(DocumentoInscripcion::class, 'id_inscripcion')
            ->where('tipo', DocumentoInscripcion::TYPE_DOCUMENTO_PAGO);
    }

    public function vigencia()
    {
        return $this->hasOne(VigenciaInscripcion::class, 'id_inscripcion');
    }

    public function ensayos()
    {
        return $this->hasMany(InscripcionEA::class, 'id_inscripcion');
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)
            ->timezone('America/La_Paz')
            ->format('d/m/Y H:i');
    }

    public function getFechaInscripcionAttribute($value)
    {
        return formatDate($value);
    }

    public function getStatusInscripcion()
    {
        switch ($this->status_inscripcion) {
            case self::STATUS_EN_REVISION:
                return "<span class='inline-flex items-center px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs font-medium rounded shadow-sm'>
                    " . self::STATUS_INSCRIPCION[$this->status_inscripcion] . "
                </span>";
            case self::STATUS_APROBADO:
                return "<span class='inline-flex items-center px-2 py-0.5 bg-green-100 text-green-800 text-xs font-medium rounded shadow-sm'>
                    " . self::STATUS_INSCRIPCION[$this->status_inscripcion] . "
                </span>";
            case self::STATUS_VENCIDO:
                return "<span class='inline-flex items-center px-2 py-0.5 bg-orange-100 text-orange-800 text-xs font-medium rounded shadow-sm'>
                    " . self::STATUS_INSCRIPCION[$this->status_inscripcion] . "
                </span>";
            case self::STATUS_ANULADO:
                return "<span class='inline-flex items-center px-2 py-0.5 bg-gray-300 text-gray-800 text-xs font-medium rounded shadow-sm'>
                    " . self::STATUS_INSCRIPCION[$this->status_inscripcion] . "
                </span>";
            case self::STATUS_EN_OBSERVACION:
                return "<span class='inline-flex items-center px-2 py-0.5 bg-red-100 text-red-800 text-xs font-medium rounded shadow-sm'>
                    " . self::STATUS_INSCRIPCION[$this->status_inscripcion] . "
                </span>";
            default:
                return "<span class='inline-flex items-center px-2 py-0.5 bg-gray-100 text-gray-800 text-xs font-medium rounded shadow-sm'>
                    ...
                </span>";
        }
    }

    public function getStatusCuenta()
    {
        switch ($this->status_cuenta) {
            case self::STATUS_PAGADO:
                return "<span class='inline-flex items-center px-2 py-0.5 bg-green-100 text-green-800 text-xs font-medium rounded shadow-sm'>
                        " . self::STATUS_CUENTA[$this->status_cuenta] . "
                    </span>";
            case self::STATUS_DEUDOR:
                return "<span class='inline-flex items-center px-2 py-0.5 bg-red-100 text-red-800 text-xs font-medium rounded shadow-sm'>
                        " . self::STATUS_CUENTA[$this->status_cuenta] . "
                    </span>";
            default:
                return "<span class='inline-flex items-center px-2 py-0.5 bg-gray-100 text-gray-800 text-xs font-medium rounded shadow-sm'>
                        Desconocido
                    </span>";
        }
    }

    public function getEstadoInscripcionTextoAttribute()
    {
        return self::STATUS_INSCRIPCION[$this->status_inscripcion] ?? 'Desconocido';
    }

    public function getEstadoPagoTextoAttribute()
    {
        return self::STATUS_CUENTA[$this->status_cuenta] ?? 'Desconocid1o';
    }

    public function getSaldoAttribute()
    {
        $pagos = $this->pagos->where('status', true)->sum('monto_pagado');
        return $this->costo_total - $pagos;
    }

    public function scopeAprobado($query)
    {
        return $query->where('status_inscripcion', Inscripcion::STATUS_APROBADO);
    }

    public function scopePendiente($query)
    {
        return $query->where('status_cuenta', Inscripcion::STATUS_DEUDOR);
    }

    public function certificado()
    {
        return $this->hasOne(Certificado::class, 'id_inscripcion');
    }

    public function getPaquetesDescripcionAttribute(): string
    {
        return $this->detalleInscripciones->pluck('descripcion_paquete')->implode(', ');
    }
}
