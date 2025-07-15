<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoInscripcion extends Model
{
    protected $table = 'documento_inscripcion';

    protected $fillable = [
        'id_inscripcion',
        'nombre_doc',
        'ruta_doc',
        'status'
    ];

    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class, 'id_inscripcion');
    }
}
