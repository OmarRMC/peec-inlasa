<?php

namespace App\Models;

use App\Traits\General;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paquete extends Model
{
    use HasFactory, General;

    protected $table = 'paquete';

    protected $fillable = [
        'id_area',
        'descripcion',
        'costo_paquete',
        'status',
        'max_participantes',
        'descuento'
    ];

    // Relación N:1 con Area
    public function area()
    {
        return $this->belongsTo(Area::class, 'id_area');
    }

    // Relación 1:N con EnsayoAptitud
    public function ensayosAptitud()
    {
        return $this->hasMany(EnsayoAptitud::class, 'id_paquete');
    }


    public function detalleInscripciones()
    {
        return $this->hasMany(DetalleInscripcion::class, 'id_paquete');
    }

    public function tiposLaboratorios()
    {
        return $this->belongsToMany(TipoLaboratorio::class, 'paquete_tipo_laboratorio', 'paquete_id', 'tipo_laboratorio_id')
            ->withTimestamps();
    }

    protected function precioFinal(): Attribute
    {
        return Attribute::get(function () {
            if ($this->descuento > 0) {
                return round($this->costo_paquete - ($this->costo_paquete * ($this->descuento / 100)), 2);
            }
            return $this->costo_paquete;
        });
    }

    protected function precioFinalHtml(): Attribute
    {
        return Attribute::get(function () {
            return "
                <div>
                    <span class='font-semibold text-green-700'>{$this->precio_final} Bs</span>
                </div>
            ";
        });
    }

    protected function descuentoHtml(): Attribute
    {
        return Attribute::get(function () {
            if ($this->descuento > 0) {
                return "<span class='inline-flex items-center px-2 py-0.5 bg-blue-100 text-blue-800 text-xs font-medium rounded shadow-sm'>
                         -{$this->descuento}%
                    </span>";
            }

            return "<span class='inline-flex items-center px-2 py-0.5 bg-gray-100 text-gray-800 text-xs font-medium rounded shadow-sm'>
                    0%
                </span>";
        });
    }
    protected function costoSinDescuento(): Attribute
    {
        return Attribute::get(fn() => $this->costo_paquete);
    }
}
