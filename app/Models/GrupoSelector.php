<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrupoSelector extends Model
{
    use HasFactory;

    protected $table = 'grupos_selectores';
    protected $fillable = ['nombre', 'descripcion'];

    public function parametros()
    {
        return $this->hasMany(Parametro::class, 'grupo_selector_id');
    }

    public function opciones()
    {
        return $this->hasMany(OpcionSelector::class, 'grupo_selector_id');
    }
}
