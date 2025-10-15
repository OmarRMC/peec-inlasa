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

    const KEY_GESTION_FILTER = 'key_gestion_filter';
    // Claves centralizadas como constantes
    public const FECHA_INICIO_INSCRIPCION = 'fecha_inicio_inscripcion';
    public const FECHA_FIN_INSCRIPCION = 'fecha_fin_inscripcion';

    public const FECHA_INICIO_REGISTRO_NUEVO_LAB = 'fecha_inicio_registro_lab';
    public const FECHA_FIN_REGISTRO_NUEVO_LAB = 'fecha_fin_registro_lab';

    public const FECHA_INICIO_PAGO = 'fecha_inicio_pago';
    public const FECHA_FIN_PAGO = 'fecha_fin_pago';

    public const FECHA_INICIO_VIGENCIA = 'fecha.inicio.vigencia';
    public const FECHA_FIN_VIGENCIA = 'fecha.fin.vigencia';

    public const GESTION_INSCRIPCION = 'gestion_inscripcion';

    public const NOTIFICACION_KEY = 'notificacion.key';
    public const NOTIFICACION_TITULO = 'notificacion.titulo';
    public const NOTIFICACION_DESCRIPCION = 'notificacion.descripcion';
    public const NOTIFICACION_MENSAJE = 'notificacion.mensaje';
    public const NOTIFICACION_FECHA_INICIO = 'notificacion.fecha_inicio';
    public const NOTIFICACION_FECHA_FIN = 'notificacion.fecha_fin';
    public const EMAIL_INFORMACION = 'email.informacion';
    public const CARGO_EVALUACION_EXTERNA = 'evaluacion_externa';
    public const CARGO_COORDINADORA_RED   = 'coordinadora_red';
    public const CARGO_DIRECTORA_GENERAL  = 'directora_general';
    public const REGISTRO_PONDERACIONES_CERTIFICADOS_GESTION = 'registro_ponderaciones_certificados_gestion';
    public const FECHA_INICIO_REGISTRO_CERTIFICADOS = 'fecha_inicio_registro_certificados';
    public const FECHA_FIN_REGISTRO_CERTIFICADOS    = 'fecha_fin_registro_certificados';
    public const HABILITAR_SUBIDA_DOCUMENTOS_INSCRIPCION = 'habilitar_subida_documentos_inscripcion';
    public const HABILITAR_SUBIDA_DOCUMENTOS_PAGOS = 'habilitar_subida_documentos_pagos';

     public static function esPeriodoRegistro()
    {
        $fechaInicio = Configuracion::where('key', Configuracion::FECHA_INICIO_REGISTRO_NUEVO_LAB)->value('valor');
        $fechaFin = Configuracion::where('key', Configuracion::FECHA_FIN_REGISTRO_NUEVO_LAB)->value('valor');
        $hoy = Carbon::now()->toDateString();
        return $fechaInicio <= $hoy && $hoy <= $fechaFin;
    }

    public static function esPeriodoInscripcion()
    {
        $fechaInicio = Configuracion::where('key', Configuracion::FECHA_INICIO_INSCRIPCION)->value('valor');
        $fechaFin = Configuracion::where('key', Configuracion::FECHA_FIN_INSCRIPCION)->value('valor');
        $hoy = Carbon::now()->toDateString();
        return $fechaInicio <= $hoy && $hoy <= $fechaFin;
    }

    public static function estaHabilitadoCargarCertificado()
    {
        $fechaInicio = Configuracion::where('key', Configuracion::FECHA_INICIO_REGISTRO_CERTIFICADOS)->value('valor');
        $fechaFin = Configuracion::where('key', Configuracion::FECHA_FIN_REGISTRO_CERTIFICADOS)->value('valor');
        $hoy = Carbon::now()->toDateString();
        return $fechaInicio <= $hoy && $hoy <= $fechaFin;
    }
}
