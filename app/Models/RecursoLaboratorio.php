<?php

namespace App\Models;

use App\Traits\General;
use Illuminate\Database\Eloquent\Model;

class RecursoLaboratorio extends Model
{
    use General;

    protected $table = 'recurso_laboratorio';

    protected $fillable = [
        'titulo',
        'url',
        'archivo',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Devuelve la URL final del recurso:
     * si tiene archivo subido usa storage, si no usa la URL externa.
     */
    public function getUrlFinalAttribute(): string
    {
        if ($this->archivo) {
            return asset('storage/' . $this->archivo);
        }

        return $this->url ?? '#';
    }
}
