<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
    protected $table = 'pais';
    protected $fillable = ['nombre_pais', 'sigla_pais', 'cod_pais', 'status_pais'];

    public function departamentos()
    {
        return $this->hasMany(Departamento::class, 'id_pais');
    }
}
