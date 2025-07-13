<?php

namespace App\Models;

use App\Traits\General;
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
}
