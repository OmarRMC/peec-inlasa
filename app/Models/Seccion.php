<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seccion extends Model
{
    use HasFactory;

    protected $table = 'secciones';
    protected $fillable = ['formulario_id', 'nombre', 'descripcion', 'posicion', 'cantidad_parametros'];

    public function formulario()
    {
        return $this->belongsTo(Formulario::class, 'formulario_id');
    }

    public function parametros()
    {
        return $this->hasMany(Parametro::class, 'id_seccion');
    }
}
