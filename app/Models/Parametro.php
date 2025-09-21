<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parametro extends Model
{
    use HasFactory;

    protected $table = 'parametros';
    protected $fillable = [
        'seccion_id',
        'nombre',
        'tipo',
        'unidad',
        'validacion',
        'requerido',
        'posicion',
        'id_grupo_selector'
    ];

    protected $casts = [
        'validacion' => 'array',
        'requerido' => 'boolean',
    ];

    public function seccion()
    {
        return $this->belongsTo(Seccion::class, 'seccion_id');
    }

    // public function grupoSelector()
    // {
    //     return $this->belongsTo(GrupoSelector::class, 'id_grupo_selector');
    // }

    // public function resultados()
    // {
    //     return $this->hasMany(ParametroCampo::class, 'id_parametro');
    // }

    public function campos()
    {
        return $this->hasMany(ParametroCampo::class, 'id_parametro');
    }
}
