<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleCertificado extends Model
{
    use HasFactory;

    protected $table = 'detalle_certificado';
    // protected $primaryKey = 'id_detalle_certificado';
    // public $incrementing = false;
    // protected $keyType = 'int';

    protected $fillable = [
        'id_certificado',
        'id_paquete',
        'id_ea',
        'detalle_ea',
        'calificacion_certificado',
        'updated_by',
        'temporal',
        'detalle_area'
    ];

    public function certificado()
    {
        return $this->belongsTo(Certificado::class, 'id_certificado');
    }
    public function getCreatedAtAttribute($value)
    {
        return formatDate($value);
    }
}
