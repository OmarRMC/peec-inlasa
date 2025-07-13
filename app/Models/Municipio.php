<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    protected $table = 'municipio';
    protected $fillable = ['id_prov', 'nombre_municipio', 'cod_municipio', 'status_municipio'];

    public function provincia()
    {
        return $this->belongsTo(Provincia::class, 'id_prov');
    }
}
