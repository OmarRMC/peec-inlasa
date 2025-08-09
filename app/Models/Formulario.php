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
}
