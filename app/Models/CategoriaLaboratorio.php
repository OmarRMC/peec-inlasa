<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaLaboratorio extends Model
{
    use HasFactory;

    protected $table = 'categoria';

    protected $fillable = [
        'descripcion',
        'status',
    ];
    public function laboratorios()
    {
        return $this->hasMany(Laboratorio::class, 'id_categoria');
    }
}
