<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificado extends Model
{
    use HasFactory;

    protected $table = 'certificado';
    // protected $primaryKey = 'id_certificado';
    // public $incrementing = false; // porque definiste manualmente el PK como integer
    // protected $keyType = 'int';

    protected $fillable = [
        'id_inscripcion',
        'gestion_certificado',
        'nombre_coordinador',
        'nombre_jefe',
        'nombre_director',
        'firma_coordinador',
        'firma_jefe',
        'firma_director',
        'nombre_laboratorio',
        'codigo_certificado',
        'tipo_certificado',
        'id_redaccion',
        'status_certificado',
        'created_by',
        'updated_by',
        'publicado'
    ];

    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class, 'id_inscripcion');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleCertificado::class, 'id_certificado');
    }
}
