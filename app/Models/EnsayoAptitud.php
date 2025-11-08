<?php

namespace App\Models;
// use App\Models\EnsayoAptitud;
use App\Traits\General;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EnsayoAptitud extends Model
{
    use HasFactory, General;

    protected $table = 'ensayo_aptitud';

    protected $fillable = [
        'id_paquete',
        'descripcion',
        'status',
    ];

    // Relación N:1 con Paquete
    public function paquete()
    {
        return $this->belongsTo(Paquete::class, 'id_paquete');
    }

    public function formularios()
    {
        return $this->belongsToMany(FormularioEnsayo::class, 'ensayo_formulario', 'ensayo_id', 'formulario_id')->withTimestamps();
    }


    public function ciclos()
    {
        return $this->hasMany(Ciclo::class, 'id_ensayo');
    }

    public function gruposSelectores()
    {
        return $this->hasMany(GrupoSelector::class, 'ensayo_id');
    }
    public function resultados(): HasMany
    {
        return $this->hasMany(FormularioEnsayoResultado::class, 'id_ensayo');
    }

    public function getCicloEnPeriodoEnvioResultados()
    {
        $hoy = Carbon::today();
        return $this->ciclos()->activo()
            ->whereDate('fecha_inicio_envio_resultados', '<=', $hoy)
            ->whereDate('fecha_fin_envio_resultados', '>=', $hoy)
            ->first();
    }

    public function getEstadoCiclosYResultados()
    {
        $hoy = Carbon::today();
        $cicloActivo = $this->ciclos()->activo()
            ->where(function ($q) use ($hoy) {
                $q->where(function ($sub) use ($hoy) {
                    $sub->whereDate('fecha_inicio_envio_muestras', '<=', $hoy)
                        ->whereDate('fecha_fin_envio_muestras', '>=', $hoy);
                })
                    ->orWhere(function ($sub) use ($hoy) {
                        $sub->whereDate('fecha_inicio_envio_reporte', '<=', $hoy)
                            ->whereDate('fecha_fin_envio_reporte', '>=', $hoy);
                    })
                    ->orWhere(function ($sub) use ($hoy) {
                        $sub->whereDate('fecha_inicio_envio_resultados', '<=', $hoy)
                            ->whereDate('fecha_fin_envio_resultados', '>=', $hoy);
                    });
            })
            ->first();

        $siguienteCiclo = $this->ciclos()->activo()
            ->whereDate('fecha_inicio_envio_muestras', '>', $hoy)
            ->orWhereDate('fecha_inicio_envio_resultados', '>', $hoy)
            ->orderBy('fecha_inicio_envio_muestras', 'asc')
            ->first();

        $estado = $cicloActivo
            ? 'activo'
            : ($siguienteCiclo ? 'pendiente' : 'finalizado');

        return [
            'cicloActivo' => $cicloActivo,
            'siguienteCiclo' => $siguienteCiclo,
            'estado' => $estado,
        ];
    }

    public function getCicloActivo($gestion = null)
    {
        $hoy = Carbon::today();

        // Si nos pasan la gestión, devolvemos el primer ciclo de esa gestión (sin validar periodos)
        if ($gestion) {
            return $this->ciclos()
                ->activo()
                ->whereYear('fecha_fin_envio_resultados', (int) $gestion)
                ->orderBy('fecha_fin_envio_resultados', 'asc')
                ->first();
        }

        // Closure que aplica la condición de estar dentro de alguno de los periodos
        $periodo = function ($q) use ($hoy) {
            $q->where(function ($sub) use ($hoy) {
                $sub->whereDate('fecha_inicio_envio_muestras', '<=', $hoy)
                    ->whereDate('fecha_fin_envio_muestras', '>=', $hoy);
            })
                ->orWhere(function ($sub) use ($hoy) {
                    $sub->whereDate('fecha_inicio_envio_reporte', '<=', $hoy)
                        ->whereDate('fecha_fin_envio_reporte', '>=', $hoy);
                })
                ->orWhere(function ($sub) use ($hoy) {
                    $sub->whereDate('fecha_inicio_envio_resultados', '<=', $hoy)
                        ->whereDate('fecha_fin_envio_resultados', '>=', $hoy);
                });
        };

    
        $cicloPorPeriodo = $this->ciclos()
            ->activo()
            ->where($periodo)
            ->first();
        return $cicloPorPeriodo;
    }
}
