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
        'orden',
        'grupo_selector_id'
    ];

    protected $casts = [
        'validacion' => 'array',
        'requerido' => 'boolean',
    ];

    public function seccion()
    {
        return $this->belongsTo(Seccion::class, 'seccion_id');
    }

    public function grupoSelector()
    {
        return $this->belongsTo(GrupoSelector::class, 'grupo_selector_id');
    }
}
