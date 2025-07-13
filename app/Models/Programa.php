<?php

namespace App\Models;

use App\Traits\General;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programa extends Model
{
    use HasFactory, General;

    protected $table = 'programa';

    protected $fillable = [
        'descripcion',
        'status',
    ];

    // Relación 1:N con Área
    public function areas()
    {
        return $this->hasMany(Area::class, 'id_programa');
    }
}
