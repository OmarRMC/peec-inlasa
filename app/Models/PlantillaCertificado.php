<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlantillaCertificado extends Model
{
    use HasFactory;

    protected $table = 'plantillas_certificados';

    protected $fillable = [
        'nombre',
        'descripcion',
        'imagen_fondo',
        'ancho_mm',
        'alto_mm',
        'fuente_por_defecto',
        'color_texto_por_defecto',
        'tamano_fuente_por_defecto',
        'diseno',
        'firmas',
        'activo',
    ];

    protected $casts = [
        'diseno' => 'array',
        'firmas' => 'array',
        'activo' => 'boolean',
        'ancho_mm' => 'decimal:2',
        'alto_mm' => 'decimal:2',
    ];

    public function certificados()
    {
        return $this->hasMany(Certificado::class, 'plantilla_certificado_id');
    }
}
