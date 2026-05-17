<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

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
        'id_ciclo',
        'cod_lab',
        'fecha_envio',
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

    public function respuestasCampo()
    {
        return $this->hasMany(RespuestaCampo::class, 'id_formulario_resultado');
    }

    public function ciclo(): BelongsTo
    {
        return $this->belongsTo(Ciclo::class, 'id_ciclo');
    }

    public function ensayoAptitud(): HasOneThrough
    {
        return $this->hasOneThrough(
            EnsayoAptitud::class,
            Ciclo::class,
            'id',          // PK en ciclos
            'id',          // PK en ensayo_aptitud
            'id_ciclo',    // local key en formulario_ensayos_resultados
            'id_ensayo'    // local key en ciclos
        );
    }

    /**
     * Accessor de compatibilidad: reconstruye el shape de RespuestaFormulario
     * (collection de objetos con id_parametro, nombre, visible_nombre, respuestas[])
     * a partir de las filas tipadas en respuestas_campo. Permite que los lectores
     * y Blade existentes funcionen sin cambios durante la transicion.
     */
    protected function respuestasCompat(): Attribute
    {
        return Attribute::make(get: function () {
            if (!$this->relationLoaded('respuestasCampo')) {
                $this->load('respuestasCampo.parametroCampo');
            } elseif ($this->respuestasCampo->isNotEmpty()
                    && !$this->respuestasCampo->first()->relationLoaded('parametroCampo')) {
                $this->respuestasCampo->load('parametroCampo');
            }

            return $this->respuestasCampo
                ->groupBy(fn ($r) => optional($r->parametroCampo)->id_parametro)
                ->map(function ($camposDeParametro, $idParametro) {
                    $primero = $camposDeParametro->first();
                    return (object) [
                        'id_parametro'   => $idParametro,
                        'nombre'         => $primero->nombre_parametro,
                        'visible_nombre' => true,
                        'respuestas'     => $camposDeParametro->map(fn ($r) => [
                            'id'         => $r->id_parametro_campo,
                            'tipo'       => $r->tipo_campo,
                            'valor'      => self::serializarValor($r),
                            'valorTexto' => $r->valor_texto_opcion,
                        ])->values()->all(),
                    ];
                })->values();
        });
    }

    protected static function serializarValor(RespuestaCampo $r): ?string
    {
        return match ($r->tipo_campo) {
            'number'   => $r->valor_numero !== null ? (string) $r->valor_numero : null,
            'date'     => optional($r->valor_fecha)->format('Y-m-d'),
            'checkbox' => $r->valor_booleano ? '1' : '0',
            'select', 'radio' => $r->id_opcion_seleccionada !== null
                ? (string) $r->id_opcion_seleccionada
                : $r->valor_texto,
            default    => $r->valor_texto,
        };
    }
}
