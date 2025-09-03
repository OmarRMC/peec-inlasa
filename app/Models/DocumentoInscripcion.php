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
        'tipo',
        'status'
    ];

    const TYPE_DOCUMENTO_INSCRIPCION = 1;
    const TYPE_DOCUMENTO_PAGO        = 2;

    const TIPOS = [
        self::TYPE_DOCUMENTO_INSCRIPCION => 'Documentos de Inscripción',
        self::TYPE_DOCUMENTO_PAGO        => 'Documentos de Pago',
    ];

    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class, 'id_inscripcion');
    }

    public function scopeInscripciones($query)
    {
        return $query->where('tipo', self::TYPE_DOCUMENTO_INSCRIPCION);
    }

    public function scopePagos($query)
    {
        return $query->where('tipo', self::TYPE_DOCUMENTO_PAGO);
    }
}
