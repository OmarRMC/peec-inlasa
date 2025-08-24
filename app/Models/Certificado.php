<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificado extends Model
{
    use HasFactory;

    protected $table = 'certificado';

    const TYPE_DESEMP = 1;
    const TYPE_PARTICIPACION = 2;

    const NAME_TYPE_CERTIFICADO = [
        self::TYPE_DESEMP => 'DESEMPEÑO',
        self::TYPE_PARTICIPACION => 'PARTICIPACIÓN'
    ];

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
        'cod_lab',
        'publicado',
        'cargo_coordinador',
        'cargo_jefe',
        'cargo_director'
    ];

    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class, 'id_inscripcion');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleCertificado::class, 'id_certificado');
    }

    public function scopePublicado($query)
    {
        return $query->where('publicado', true);
    }

    public function scopeNoPublicado($query)
    {
        return $query->where('publicado', false);
    }
    public function scopeConDetalles($query)
    {
        return $query->has('detalles');
    }
}
