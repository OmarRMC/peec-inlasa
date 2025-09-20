<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormularioEnsayo extends Model
{
    use HasFactory;

    protected $table = 'formularios';
    protected $fillable = ['nombre', 'codigo', 'nota'];

    public function secciones()
    {
        return $this->hasMany(Seccion::class, 'id_formulario');
    }

    public function ensayo()
    {
        return $this->belongsTo(EnsayoAptitud::class, 'id_ensayo');
    }
    public function scopeActivo($query)
    {
        return $query->where('estado', true);
    }
}
