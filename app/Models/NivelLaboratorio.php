<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NivelLaboratorio extends Model
{
    use HasFactory;

    protected $table = 'nivel_laboratorio';

    protected $fillable = [
        'descripcion_nivel',
        'status_nivel',
    ];

    protected $casts = [
        'status_nivel' => 'boolean',
    ];

    public function laboratorios()
    {
        return $this->hasMany(Laboratorio::class, 'id_nivel');
    }
    public function scopeActive($query)
    {
        return $query->where('status_nivel', true);
    }
}
