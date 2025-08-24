<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleInscripcion extends Model
{
    protected $table = 'detalle_inscripcion';

    protected $fillable = [
        'id_inscripcion',
        'id_paquete',
        'descripcion_paquete',
        'costo_paquete',
        'observaciones'
    ];

    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class, 'id_inscripcion');
    }

    public function paquete()
    {
        return $this->belongsTo(Paquete::class, 'id_paquete');
    }

    
}
