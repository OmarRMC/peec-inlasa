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
    public const EMAIL_APROBACION = 'email.aprobacion';
    public const EMAIL_OBSERVACION = 'email.observacion';
    public const CARGO_EVALUACION_EXTERNA = 'evaluacion_externa';
    public const CARGO_COORDINADORA_RED   = 'coordinadora_red';
    public const CARGO_DIRECTORA_GENERAL  = 'directora_general';
    public const REGISTRO_PONDERACIONES_CERTIFICADOS_GESTION = 'registro_ponderaciones_certificados_gestion';
    public const FECHA_INICIO_REGISTRO_CERTIFICADOS = 'fecha_inicio_registro_certificados';
    public const FECHA_FIN_REGISTRO_CERTIFICADOS    = 'fecha_fin_registro_certificados';
    public const HABILITAR_SUBIDA_DOCUMENTOS_INSCRIPCION = 'habilitar_subida_documentos_inscripcion';
    public const HABILITAR_SUBIDA_DOCUMENTOS_PAGOS = 'habilitar_subida_documentos_pagos';

    public const CARGO_DIRECTORA_GENERAL_CONTRATO  = 'directora_general_contrato';
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

    public const EMAIL_COMODINES = [
        '{codigo}'      => 'Código del laboratorio',
        '{laboratorio}' => 'Nombre del laboratorio',
        '{gestion}'     => 'Gestión de la inscripción',
        '{fecha}'       => 'Fecha de envío',
    ];

    public const EMAIL_APROBACION_INFORMACION = 'email.aprobacion.informacion';
    public const EMAIL_OBSERVACION_INFORMACION = 'email.observacion.informacion';

    public const EMAIL_TIPOS = [
        'aprobacion'  => self::EMAIL_APROBACION_INFORMACION,
        'observacion' => self::EMAIL_OBSERVACION_INFORMACION,
    ];

    // Claves anteriores: solo un bloque libre al final de la sección (ya no se editan)
    private const EMAIL_BLOQUE_LIBRE = [
        'aprobacion'  => self::EMAIL_APROBACION,
        'observacion' => self::EMAIL_OBSERVACION,
    ];

    /** HTML original de la sección "INFORMACIÓN" según la plantilla. */
    public static function informacionEmailPorDefecto(string $tipo): string
    {
        return view('emails.partials.informacion_' . $tipo)->render();
    }

    /**
     * Contenido de la sección cuando aún no se editó: el original con el bloque libre anterior
     * (email.aprobacion / email.observacion) en el mismo lugar donde aparecía.
     */
    public static function informacionEmailInicial(string $tipo): string
    {
        $html = self::informacionEmailPorDefecto($tipo);
        $bloque = trim((string) self::find(self::EMAIL_BLOQUE_LIBRE[$tipo])?->valor);

        if ($bloque === '' || str_contains($bloque, 'Modificable desde la configuración')) {
            return $html;
        }
        if (str_contains($bloque, 'pégalo en un navegador')) {
            return $bloque;
        }

        if (preg_match('/<body[^>]*>(.*)<\/body>/is', $bloque, $m)) {
            preg_match_all('/<style[^>]*>.*?<\/style>/is', $bloque, $estilos);
            $bloque = implode("\n", $estilos[0]) . "\n" . trim($m[1]);
        }

        $pos = strpos($html, '</table>', strpos($html, 'pégalo en un navegador.'));
        $fila = "<tr>\n<td align=\"left\" style=\"padding:0;Margin:0\">\n" . $bloque . "\n</td>\n</tr>\n";
        return substr_replace($html, $fila, $pos, 0);
    }

    /**
     * HTML de la sección "INFORMACIÓN" del correo con los comodines reemplazados.
     * El HTML guardado NO se compila con Blade; solo se reemplazan comodines.
     */
    public static function informacionEmail(string $tipo, $usuario, $laboratorio, $gestion, ?string $html = null): string
    {
        $html ??= self::find(self::EMAIL_TIPOS[$tipo])?->valor;
        if (!is_string($html) || trim($html) === '') {
            $html = self::informacionEmailInicial($tipo);
        }

        return strtr($html, array_map(fn($v) => e((string) $v), [
            '{codigo}'      => $usuario?->username,
            '{laboratorio}' => $laboratorio?->nombre_lab,
            '{gestion}'     => $gestion,
            '{fecha}'       => now()->format('d/m/Y'),
        ]));
    }

    public static function estaHabilitadoCargarCertificado()
    {
        $fechaInicio = Configuracion::where('key', Configuracion::FECHA_INICIO_REGISTRO_CERTIFICADOS)->value('valor');
        $fechaFin = Configuracion::where('key', Configuracion::FECHA_FIN_REGISTRO_CERTIFICADOS)->value('valor');
        $hoy = Carbon::now()->toDateString();
        return $fechaInicio <= $hoy && $hoy <= $fechaFin;
    }
}
