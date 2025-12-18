<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ciclo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'numero',
        'fecha_inicio_envio_muestras',
        'fecha_fin_envio_muestras',
        'fecha_inicio_envio_resultados',
        'fecha_fin_envio_resultados',
        'fecha_inicio_envio_reporte',
        'fecha_fin_envio_reporte',
        'id_ensayo',
        'estado',
    ];
    public function scopeActivo($query)
    {
        return $query->where('estado', 1);
    }
    public function ensayoAptitud()
    {
        return $this->belongsTo(EnsayoAptitud::class, 'id_ensayo');
    }

    public function getFechaInicioEnvioMuestrasShowAttribute()
    {

        return formatDate($this->fecha_inicio_envio_muestras, false);
    }

    public function getFechaFinEnvioMuestrasShowAttribute()
    {
        return formatDate($this->fecha_fin_envio_muestras, false);
    }

    public function getFechaInicioEnvioResultadosShowAttribute()
    {
        return formatDate($this->fecha_inicio_envio_resultados, false);
    }

    public function getFechaFinEnvioResultadosShowAttribute()
    {
        return formatDate($this->fecha_fin_envio_resultados, false);
    }

    public function scopeGestion($query, $gestion)
    {
        return $query->whereYear('fecha_inicio_envio_resultados', $gestion);
    }
    public function getFechaInicioEnvioReporteShowAttribute()
    {
        return formatDate($this->fecha_inicio_envio_reporte, false);
    }

    public function getFechaFinEnvioReporteShowAttribute()
    {
        return formatDate($this->fecha_fin_envio_reporte, false);
    }

    public function enPeriodoEnvioResultados(): bool
    {
        $hoy = Carbon::today();

        return $this->fecha_inicio_envio_resultados &&
            $this->fecha_fin_envio_resultados &&
            $hoy->between($this->fecha_inicio_envio_resultados, $this->fecha_fin_envio_resultados);
    }

    public function scopeGestionParaInforme($query, $gestion)
    {
        return $query->whereYear('fecha_inicio_envio_resultados', $gestion)
            ->whereDate('fecha_inicio_envio_reporte', '<=', Carbon::today());
    }

    public function informes()
    {
        return $this->hasMany(InformeTecnico::class, 'id_ciclo');
    }

    // public function scopePorGestion($query, $gestion)
    // {
    //     return $query->where('gestion', $gestion);
    // }
}
