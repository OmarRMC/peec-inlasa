<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RespuestaCampo extends Model
{
    use HasFactory;

    protected $table = 'respuestas_campo';

    protected $fillable = [
        'id_formulario_resultado',
        'id_parametro_campo',
        'tipo_campo',
        'nombre_parametro',
        'valor_texto',
        'valor_numero',
        'valor_fecha',
        'valor_booleano',
        'id_opcion_seleccionada',
        'valor_texto_opcion',
    ];

    protected $casts = [
        'valor_numero'   => 'decimal:6',
        'valor_fecha'    => 'date',
        'valor_booleano' => 'boolean',
    ];

    public function formularioResultado(): BelongsTo
    {
        return $this->belongsTo(FormularioEnsayoResultado::class, 'id_formulario_resultado');
    }

    public function parametroCampo(): BelongsTo
    {
        return $this->belongsTo(ParametroCampo::class, 'id_parametro_campo');
    }

    public function opcionSeleccionada(): BelongsTo
    {
        return $this->belongsTo(OpcionSelector::class, 'id_opcion_seleccionada');
    }
}
