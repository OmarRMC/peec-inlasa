<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'configuracion';

    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'valor',
    ];

    // Claves centralizadas como constantes
    public const FECHA_INICIO_INSCRIPCION = 'fecha_inicio_inscripcion';
    public const FECHA_FIN_INSCRIPCION = 'fecha_fin_inscripcion';

    public const FECHA_INICIO_PAGO = 'fecha_inicio_pago';
    public const FECHA_FIN_PAGO = 'fecha_fin_pago';

    public const FECHA_INICIO_VIGENCIA = 'fecha.inicio.vigencia';
    public const FECHA_FIN_VIGENCIA = 'fecha.fin.vigencia';

    public const GESTION_ACTUAL = 'gestion_actual';

    public const NOTIFICACION_KEY = 'notificacion.key';
    public const NOTIFICACION_TITULO = 'notificacion.titulo';
    public const NOTIFICACION_DESCRIPCION = 'notificacion.descripcion';
    public const NOTIFICACION_MENSAJE = 'notificacion.mensaje';
    public const NOTIFICACION_FECHA_INICIO = 'notificacion.fecha_inicio';
    public const NOTIFICACION_FECHA_FIN = 'notificacion.fecha_fin';
    public const EMAIL_INFORMACION = 'email.informacion';

    public static function esPeriodoInscripcion()
    {
        $fechaInicio = Configuracion::where('key', Configuracion::FECHA_INICIO_INSCRIPCION)->value('valor');
        $fechaFin = Configuracion::where('key', Configuracion::FECHA_FIN_INSCRIPCION)->value('valor');
        $hoy = Carbon::now()->toDateString();
        return $fechaInicio <= $hoy && $hoy <= $fechaFin;
    }
}
