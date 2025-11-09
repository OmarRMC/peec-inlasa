<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParametroCampo extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla (opcional, porque Laravel
     * infiere el plural automáticamente).
     */
    protected $table = 'parametro_campos';

    /**
     * Campos asignables en masa.
     */
    protected $fillable = [
        'id_parametro',
        'id_grupo_selector',
        'nombre',
        'label',
        'tipo',
        'placeholder',
        'unidad',
        'posicion',
        'mensaje',
        'requerido',
        'min',
        'max',
        'minlength',
        'maxlength',
        'step',
        'pattern',
        'reglas',
        'modificable',
        'dependencias',
        'rangeNumber',
        'rangeLength',
        'range',
        'valor',
        'modificable',
        'id_campo_padre'
    ];

    /**
     * Cast automáticos para JSON y booleanos.
     */
    protected $casts = [
        'requerido'     => 'boolean',
        'modificable'   => 'boolean',
        'reglas'        => 'array',
        'dependencias'  => 'array',
        'rangeNumber'   => 'array',
        'rangeLength'   => 'array',
    ];

    /**
     * Relación con Parametro.
     */
    public function parametro()
    {
        return $this->belongsTo(Parametro::class, 'id_parametro');
    }

    /**
     * Relación con GrupoSelector.
     */
    public function grupoSelector()
    {
        return $this->belongsTo(GrupoSelector::class, 'id_grupo_selector');
    }

    protected function notaValidacion(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['mensaje'],
        );
    }

    public function campoPadre()
    {
        return $this->belongsTo(ParametroCampo::class, 'id_campo_padre');
    }

    public function camposHijos()
    {
        return $this->hasMany(ParametroCampo::class, 'id_campo_padre');
    }

    //range => 1-2
    protected function min(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if (!empty($attributes['range'])) {
                    [$min, $max] = explode('-', $attributes['range']);
                    return trim($min);
                }
                return $value;
            }
        );
    }

    protected function max(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if (!empty($attributes['range'])) {
                    [$min, $max] = explode('-', $attributes['range']);
                    return trim($max);
                }
                return $value;
            }
        );
    }
}
