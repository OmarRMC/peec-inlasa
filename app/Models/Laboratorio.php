<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Laboratorio extends Model
{
    use HasFactory;

    protected $table = 'laboratorio';

    protected $fillable = [
        'id_usuario',
        'cod_lab',
        'antcod_peec',
        'numsedes_lab',
        'nit_lab',
        'nombre_lab',
        'sigla_lab',
        'id_nivel',
        'id_tipo',
        'id_categoria',
        'respo_lab',
        'ci_respo_lab',
        'repreleg_lab',
        'ci_repreleg_lab',
        'id_pais',
        'id_dep',
        'id_prov',
        'id_municipio',
        'zona_lab',
        'direccion_lab',
        'wapp_lab',
        'wapp2_lab',
        'mail_lab',
        'mail2_lab',
        'status',
        'created_by',
        'updated_by',
        'email_verified_at'
    ];

    protected function nombreLab(): Attribute
    {
        return Attribute::make(
            get: fn($value) => strtoupper($value),
        );
    }

    protected function siglaLab(): Attribute
    {
        return Attribute::make(
            get: fn($value) => strtoupper($value),
        );
    }

    protected function respoLab(): Attribute
    {
        return Attribute::make(
            get: fn($value) => strtoupper($value),
        );
    }

    protected function zonaLab(): Attribute
    {
        return Attribute::make(
            get: fn($value) => strtoupper($value),
        );
    }

    protected function reprelegLab(): Attribute
    {
        return Attribute::make(
            get: fn($value) => strtoupper($value),
        );
    }

    protected function direccionLab(): Attribute
    {
        return Attribute::make(
            get: fn($value) => strtoupper($value),
        );
    }

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function nivel()
    {
        return $this->belongsTo(NivelLaboratorio::class, 'id_nivel');
    }

    public function tipo()
    {
        return $this->belongsTo(TipoLaboratorio::class, 'id_tipo');
    }

    public function categoria()
    {
        return $this->belongsTo(CategoriaLaboratorio::class, 'id_categoria');
    }

    public function pais()
    {
        return $this->belongsTo(Pais::class, 'id_pais');
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_dep');
    }

    public function provincia()
    {
        return $this->belongsTo(Provincia::class, 'id_prov');
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'id_municipio');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function actualizador()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }


    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'id_lab');
    }


    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)
            ->timezone('America/La_Paz')
            ->format('d/m/Y H:i');
    }


    public function tieneNotificacionPendiente()
    {
        $fechaInicio = Configuracion::where('key', Configuracion::NOTIFICACION_FECHA_INICIO)->value('valor');
        $fechaFin = Configuracion::where('key', Configuracion::NOTIFICACION_FECHA_FIN)->value('valor');
        $clave = Configuracion::where('key', Configuracion::NOTIFICACION_KEY)->value('valor');
        $hoy = Carbon::now()->toDateString();

        return $this->notificacion_key !== $clave &&
            $fechaInicio <= $hoy &&
            $fechaFin >= $hoy;
    }
}
