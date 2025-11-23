<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContratoDetalle extends Model
{
    protected $table = 'contrato_detalles';

    protected $fillable = [
        'id_contrato',
        'titulo',
        'descripcion',
        'posicion',
        'estado',
    ];

    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'id_contrato');
    }
}
