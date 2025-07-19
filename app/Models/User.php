<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Notifications\VerificarCorreoPersonalizado;
use App\Traits\General;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use General;
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'nombre',
        'ap_paterno',
        'ap_materno',
        'ci',
        'talefono',
        'status',
        'id_cargo',
        'email',
        'password'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relación con el cargo (1:N)
     */
    public function cargo(): BelongsTo
    {
        return $this->belongsTo(Cargo::class, 'id_cargo');
    }

    /**
     * Relación con permisos (N:M)
     */
    public function permisos(): BelongsToMany
    {
        return $this->belongsToMany(Permiso::class, 'usuario_permiso', 'id_usuario', 'id_permiso')->withTimestamps();
    }

    public function laboratorio()
    {
        return $this->hasOne(Laboratorio::class, 'id_usuario');
    }

    public function laboratoriosCreados()
    {
        return $this->hasMany(Laboratorio::class, 'created_by');
    }

    public function laboratoriosActualizados()
    {
        return $this->hasMany(Laboratorio::class, 'updated_by');
    }

    public function getCreatedAtAttribute($value)
    {
        return formatDate($value);
    }

    // public function sendEmailVerificationNotification()
    // {
    //     $this->notify(new VerificarCorreoPersonalizado($this, null));
    // }

    public function isLaboratorio()
    {
        return $this->laboratorio !== null;
    }

    public function tienePermiso($nombre)
    {
        return $this->permisos->contains('nombre_permiso', $nombre);
    }
}
