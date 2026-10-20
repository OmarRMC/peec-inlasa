<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InscripcionEAFormulario extends Model
{
    protected $table = 'inscripcion_ea_formulario';

    protected $fillable = [
        'inscripcion_ea_id',
        'formulario_id',
        'cantidad',
    ];

    public function inscripcionEA()
    {
        return $this->belongsTo(InscripcionEA::class, 'inscripcion_ea_id');
    }

    public function formulario()
    {
        return $this->belongsTo(Formulario::class, 'formulario_id');
    }
}
