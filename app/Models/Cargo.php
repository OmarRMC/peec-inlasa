<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    protected $table = 'cargo';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre_cargo',
        'descripcion',
        'obs',
        'status',
    ];
}
