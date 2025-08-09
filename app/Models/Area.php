<?php

namespace App\Models;

use App\Traits\General;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory, General;

    protected $table = 'area';

    protected $fillable = [
        'id_programa',
        'descripcion',
        'status',
        'max_paquetes_inscribir',
    ];

    // Relación N:1 con Programa
    public function programa()
    {
        return $this->belongsTo(Programa::class, 'id_programa');
    }

    // Relación 1:N con Paquete
    public function paquetes()
    {
        return $this->hasMany(Paquete::class, 'id_area');
    }
}
