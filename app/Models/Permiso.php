<?php

namespace App\Models;

use App\Traits\General;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permiso extends Model
{
    use General;
    protected $table = 'permiso';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre_permiso',
        'descripcion',
        'status',
    ];

    /**
     * Relación con usuarios
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'usuario_permiso', 'id_permiso', 'id_usuario')->withTimestamps();
    }
}
