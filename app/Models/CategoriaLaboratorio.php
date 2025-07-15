<?php

namespace App\Models;

use App\Traits\General;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaLaboratorio extends Model
{
    use HasFactory, General;

    protected $table = 'categoria';

    protected $fillable = [
        'descripcion',
        'status',
    ];
    protected function nombre(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['descripcion'],
        );
    }
    public function laboratorios()
    {
        return $this->hasMany(Laboratorio::class, 'id_categoria');
    }
}
