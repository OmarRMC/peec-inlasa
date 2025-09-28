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

    public function ensayos()
    {
        return $this->belongsToMany(EnsayoAptitud::class, 'ensayo_formulario', 'formulario_id', 'ensayo_id')->withTimestamps();
    }

    public function scopeActivo($query)
    {
        return $query->where('estado', true);
    }
}
