<?php

namespace App\Models;

use App\Traits\General;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoLaboratorio extends Model
{
    use HasFactory, General;

    protected $table = 'tipo_laboratorio';

    protected $fillable = [
        'descripcion',
        'status',
    ];
    public function laboratorios()
    {
        return $this->hasMany(Laboratorio::class, 'id_tipo');
    }
    public function programas()
    {
        return $this->belongsToMany(Programa::class, 'tipo_laboratorio_programa', 'id_tipo', 'id_programa');
    }

    public function paquetes()
    {
        return $this->belongsToMany(Paquete::class, 'paquete_tipo_laboratorio', 'tipo_laboratorio_id', 'paquete_id')
            ->withTimestamps();
    }
}
