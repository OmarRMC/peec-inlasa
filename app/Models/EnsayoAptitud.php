<?php

namespace App\Models;

use App\Traits\General;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
