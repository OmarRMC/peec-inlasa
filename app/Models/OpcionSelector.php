<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpcionSelector extends Model
{
    use HasFactory;

    protected $table = 'opciones_selector';
    protected $fillable = [
        'id_grupo_selector',
        'valor',
        'etiqueta',
        'posicion',
        'grupo_hijo_id'
    ];

    public function grupo()
    {
        return $this->belongsTo(GrupoSelector::class, 'id_grupo_selector');
    }

    public function grupoHijo()
    {
        return $this->belongsTo(GrupoSelector::class, 'grupo_hijo_id');
    }
}
