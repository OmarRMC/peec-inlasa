<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetallePagoDocumento extends Model
{
    use HasFactory;

    protected $table = 'detalle_pago_documento';

    protected $fillable = [
        'documento_inscripcion_id',
        'nit',
        'razon_social',
    ];

    /**
     * Un detalle de pago pertenece a un documento de inscripción.
     */
    public function documento()
    {
        return $this->belongsTo(DocumentoInscripcion::class, 'documento_inscripcion_id');
    }
}
