<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    protected $table = 'provincia';
    protected $fillable = ['id_dep', 'nombre_prov', 'cod_prov', 'status_prov'];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_dep');
    }

    public function municipios()
    {
        return $this->hasMany(Municipio::class, 'id_prov');
    }
}
