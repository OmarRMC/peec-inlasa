<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespuestaFormulario extends Model
{
    use HasFactory;

    protected $table = 'respuestas_formulario';

    protected $fillable = [
        'id_formulario_resultado',
        'id_parametro',
        'nombre',
        'visible_nombre',
        'respuestas'
    ];

    protected $casts = [
        'respuestas' => 'array',
    ];

    public function formularioResultado()
    {
        return $this->belongsTo(FormularioEnsayoResultado::class, 'id_formulario_resultado');
    }

    public function parametro()
    {
        return $this->belongsTo(Parametro::class, 'id_parametro');
    }
}
