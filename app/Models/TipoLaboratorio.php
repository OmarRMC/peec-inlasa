<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoLaboratorio extends Model
{
    use HasFactory;

    protected $table = 'tipo_laboratorio';

    protected $fillable = [
        'descripcion',
        'status',
    ];
    public function laboratorios()
    {
        return $this->hasMany(Laboratorio::class, 'id_tipo');
    }
}
