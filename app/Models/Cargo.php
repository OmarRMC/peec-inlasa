<?php

namespace App\Models;

use App\Traits\General;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cargo extends BaseModel
{
    use General;
    protected $table = 'cargo';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre_cargo',
        'descripcion',
        'obs',
        'status',
    ];

    const RESPOSABLE_EA="Resposable del Ensayo A.";
    const SOPORTE="Soporte";


    /**
     * Relación con usuarios
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'id_cargo');
    }
}
