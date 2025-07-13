<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'departamento';
    protected $fillable = ['nombre_dep', 'sigla_dep', 'status_dep', 'id_pais'];

    public function pais()
    {
        return $this->belongsTo(Pais::class, 'id_pais');
    }

    public function provincias()
    {
        return $this->hasMany(Provincia::class, 'id_dep');
    }
}
