<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    protected $table = 'permiso';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre_permiso',
        'descripcion',
        'status',
    ];
}
