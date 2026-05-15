<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Formulario extends Model
{
    protected $table = 'formulario';

    const INSCRIPCION = 'inscripcion';
    protected $fillable = [
        'codigo',
        'proceso',
        'fec_formulario',
        'version',
        'titulo',
        'status'
    ];
    // protected $dates = ['fec_formulario'];
    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'id_formulario');
    }

    public function getfecFormularioAttribute($value)
    {
        return formatDate($value, false);
    }

    public function ensayos()
    {
        return $this->belongsToMany(EnsayoAptitud::class, 'ensayo_formulario', 'formulario_id', 'ensayo_id');
    }

    public function inscripcionesEA()
    {
        return $this->belongsToMany(InscripcionEA::class, 'inscripcion_ea_formulario','formulario_id','inscripcion_ea_id')
            ->withPivot('cantidad')
            ->withTimestamps();
    }
}
