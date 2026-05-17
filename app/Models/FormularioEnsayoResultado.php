<?php

namespace App\Models;

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
}
