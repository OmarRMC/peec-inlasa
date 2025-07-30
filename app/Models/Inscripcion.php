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


    const STATUS_PAGADO = 1;

    const STATUS_DEUDOR = 2;
    const STATUS_INSCRIPCION = [
        self::STATUS_EN_REVISION => 'En revision',
        self::STATUS_APROBADO => 'Aprobado',
        self::STATUS_VENCIDO => 'Vencido',
    ];

    const STATUS_CUENTA = [
        self::STATUS_PAGADO => 'Pagado',
        self::STATUS_DEUDOR => 'Deudor',
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
                return "<span class='inline-flex items-center px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full shadow-sm'>
                        " . self::STATUS_INSCRIPCION[$this->status_inscripcion] . "
                    </span>";
            case self::STATUS_APROBADO:
                return "<span class='inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full shadow-sm'>
                        " . self::STATUS_INSCRIPCION[$this->status_inscripcion] . "
                    </span>";
            case self::STATUS_VENCIDO:
                return "<span class='inline-flex items-center px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full shadow-sm'>
                        " . self::STATUS_INSCRIPCION[$this->status_inscripcion] . "
                    </span>";
            default:
                return "<span class='inline-flex items-center px-2 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full shadow-sm'>
                        ...
                    </span>";
        }
    }

    public function getStatusCuenta()
    {
        // return $this->status_cuenta
        //     ? "<span class='text-green-600 font-semibold'>" . self::STATUS_CUENTA[$this->status_cuenta] - "</span>"
        //     : '<span class="text-red-600 font-semibold">Inactiva</span>';
        return "<span class='text-green-600 font-semibold'>" . self::STATUS_CUENTA[$this->status_cuenta] . "</span>";
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
}
