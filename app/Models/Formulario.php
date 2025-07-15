<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formulario extends Model
{
    protected $table = 'formulario';

    protected $fillable = [
        'codigo',
        'proceso',
        'fec_formulario',
        'status'
    ];

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'id_formulario');
    }
}
