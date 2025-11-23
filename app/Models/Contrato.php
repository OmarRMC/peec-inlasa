<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    protected $table = 'contratos';

    protected $fillable = [
        'firmante_nombre',
        'firmante_cargo',
        'firmante_institucion',
        'firmante_detalle',
        'estado',
    ];

    //estado
    public function scopeActivo($query)
    {
        return $query->where('estado', 1);
    }

    // Relación: un contrato tiene muchos detalles/secciones
    public function detalles()
    {
        return $this->hasMany(ContratoDetalle::class, 'id_contrato')->orderBy('posicion', 'asc');
    }

    public function detallesActivos()
    {
        return $this->hasMany(ContratoDetalle::class, 'id_contrato')->where('estado', 1)->orderBy('posicion', 'asc');
    }

    // Relación opcional: una inscripción puede apuntar a este contrato
    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'id_contrato');
    }
}
