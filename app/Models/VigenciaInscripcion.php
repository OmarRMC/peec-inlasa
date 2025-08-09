<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VigenciaInscripcion extends Model
{
    protected $table = 'vigencia_inscripcion';

    protected $fillable = [
        'id_inscripcion',
        'fecha_inicio',
        'fecha_fin',
        'status',
        'created_by',
        'updated_by'
    ];

    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class, 'id_inscripcion');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
