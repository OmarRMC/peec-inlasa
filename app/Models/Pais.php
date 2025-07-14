<?php

namespace App\Models;

use App\Traits\General;
use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
    protected $table = 'pais';
    protected $fillable = ['nombre_pais', 'sigla_pais', 'cod_pais', 'status_pais'];

    public function departamentos()
    {
        return $this->hasMany(Departamento::class, 'id_pais');
    }
    public function laboratorios()
    {
        return $this->hasMany(Laboratorio::class, 'id_pais');
    }

    public function scopeActive($query)
    {
        return $query->where('status_pais', true);
    }
}
