<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormularioEnsayoResultado extends Model
{
    use HasFactory;

    protected $table = 'formulario_ensayos_resultados';

    protected $fillable = [
        'id_formulario',
        'id_laboratorio',
        'observaciones',
        'estado',
        'estructura',
        'gestion',
        'nombre_lab',
        'departamento',
        'id_ciclo'
    ];
    protected $casts = [
        'estructura' => 'array'
    ];

    public function formulario()
    {
        return $this->belongsTo(FormularioEnsayo::class, 'id_formulario');
    }

    public function laboratorio()
    {
        return $this->belongsTo(Laboratorio::class, 'id_laboratorio');
    }

    public function respuestas()
    {
        return $this->hasMany(RespuestaFormulario::class, 'id_formulario_resultado');
    }
}
