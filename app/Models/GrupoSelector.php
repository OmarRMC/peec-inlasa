<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrupoSelector extends Model
{
    use HasFactory;

    protected $table = 'grupos_selectores';
    protected $fillable = ['nombre', 'descripcion', 'ensayo_id'];

    // public function parametros()
    // {
    //     return $this->hasMany(Parametro::class, 'id_grupo_selector');
    // }

    public function opciones()
    {
        return $this->hasMany(OpcionSelector::class, 'id_grupo_selector');
    }

    public function campo()
    {
        return $this->hasOne(ParametroCampo::class, 'id_grupo_selector');
    }

    public function ensayo()
    {
        return $this->belongsTo(EnsayoAptitud::class, 'ensayo_id');
    }
}
