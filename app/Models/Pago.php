<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pago';

    protected $fillable = [
        'id_inscripcion',
        'fecha_pago',
        'tipo_transaccion',
        'nro_tranferencia',
        'monto_pagado',
        'obs_pago',
        'status',
        'created_by',
        'updated_by',
        'nro_factura',
        'razon_social',
    ];

    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class, 'id_inscripcion');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
