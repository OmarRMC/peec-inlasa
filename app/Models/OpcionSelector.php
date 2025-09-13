<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpcionSelector extends Model
{
    use HasFactory;

    protected $table = 'opciones_selector';
    protected $fillable = [
        'grupo_selector_id',
        'valor',
        'etiqueta',
        'orden',
        'grupo_hijo_id'
    ];

    public function grupo()
    {
        return $this->belongsTo(GrupoSelector::class, 'grupo_selector_id');
    }

    public function grupoHijo()
    {
        return $this->belongsTo(GrupoSelector::class, 'grupo_hijo_id');
    }
}
