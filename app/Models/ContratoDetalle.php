<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class ContratoDetalle extends Model implements AuditableContract
{
    use Auditable;

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
