<?php

namespace App\Models;

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

    public function getFechaInicioEnvioReporteShowAttribute()
    {
        return formatDate($this->fecha_inicio_envio_reporte, false);
    }

    public function getFechaFinEnvioReporteShowAttribute()
    {
        return formatDate($this->fecha_fin_envio_reporte, false);
    }
}
