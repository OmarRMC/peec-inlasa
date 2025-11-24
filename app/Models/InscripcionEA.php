<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InscripcionEA extends Model
{
    protected $table = 'inscripcion_ea';

    protected $fillable = [
        'id_inscripcion',
        'id_ea',
        'descripcion_ea'
    ];

    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class, 'id_inscripcion');
    }

    public function ensayoAptitud()
    {
        return $this->belongsTo(EnsayoAptitud::class, 'id_ea');
    }

    public function detalleCertificado()
    {
        return $this->hasOne(DetalleCertificado::class, 'id_ea', 'id_ea')
            ->whereColumn('id_certificado', 'id_certificado');
    }

    public function formularios()
    {
        return $this->belongsToMany(InscripcionEAFormulario::class, 'inscripcion_ea_formulario', 'inscripcion_ea_id', 'formulario_id')
            ->withPivot('cantidad')
            ->withTimestamps();
    }
}
