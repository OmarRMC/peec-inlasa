<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class VigenciaInscripcion extends Model implements AuditableContract
{
    use Auditable;

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
