<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    protected $table = 'inscripcion';

    protected $fillable = [
        'id_lab',
        'id_formulario',
        'cant_paq',
        'costo_total',
        'obs_inscripcion',
        'fecha_inscripcion',
        'status_cuenta',
        'status_inscripcion',
        'created_by',
        'updated_by',
        'gestion'
    ];

    public function laboratorio()
    {
        return $this->belongsTo(Laboratorio::class, 'id_lab');
    }

    public function formulario()
    {
        return $this->belongsTo(Formulario::class, 'id_formulario');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function detalleInscripciones()
    {
        return $this->hasMany(DetalleInscripcion::class, 'id_inscripcion');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'id_inscripcion');
    }

    public function documentos()
    {
        return $this->hasMany(DocumentoInscripcion::class, 'id_inscripcion');
    }

    public function vigencias()
    {
        return $this->hasMany(VigenciaInscripcion::class, 'id_inscripcion');
    }

    public function ensayos()
    {
        return $this->hasMany(InscripcionEA::class, 'id_inscripcion');
    }
}
