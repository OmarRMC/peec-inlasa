<?php

namespace App\Models;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Laboratorio extends Model
{
    use HasFactory;

    const STATUS = [
        1 => 'Activo',
        0 => 'Inactivo'
    ];
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

    public function tieneIscripcionGestionActual()
    {
        return $this->inscripciones()->where('gestion', configuracion(Configuracion::GESTION_INSCRIPCION))->exists();
    }

    public function getDataCertificadoDesemp(string $gestion)
    {
        $inscripciones = $this->inscripciones()
            ->Aprobado()
            ->whereHas('certificado', fn($query) => $query->Publicado())
            ->where('gestion', $gestion)
            ->whereHas('certificado.detalles', fn($query) => $query->whereNotNull('calificacion_certificado'))
            ->with(['certificado.detalles'])
            ->get();

        $dataPorArea = [];
        foreach ($inscripciones as $inscripcion) {
            $certificado = $inscripcion->certificado;
            $detalles = $certificado->detalles;

            if ($detalles->isEmpty()) continue;

            foreach ($detalles as $detalle) {
                if (is_null($detalle->calificacion_certificado)) continue;

                if (!isset($dataPorArea["$detalle->detalle_area"])) {
                    $dataPorArea["$detalle->detalle_area"] = [
                        'certificado' => $certificado,
                        'detalles' => []
                    ];
                }

                $dataPorArea["$detalle->detalle_area"]['detalles'][] = [
                    'ensayo' => $detalle->detalle_ea,
                    'ponderacion' => $detalle->calificacion_certificado,
                ];
            }
        }
        return $dataPorArea;
    }

    public function seTieneDeudaPendiente()
    {
        $gestion = configuracion(Configuracion::GESTION_INSCRIPCION);
        $inscripciones = $this->inscripciones()
            ->Aprobado()
            // ->whereHas('certificado', fn($query) => $query->Publicado())
            ->where('gestion', '<', $gestion)
            // ->whereHas('certificado.detalles', fn($query) => $query->whereNotNull('calificacion_certificado'))
            // ->with(['certificado.detalles'])
            ->get();

        $dataPorArea = [];
        // foreach ($inscripciones as $inscripcion) {
        //     $certificado = $inscripcion->certificado;
        //     $detalles = $certificado->detalles;

        //     if ($detalles->isEmpty()) continue;

        //     foreach ($detalles as $detalle) {
        //         if (is_null($detalle->calificacion_certificado)) continue;

        //         if (!isset($dataPorArea["$detalle->detalle_area"])) {
        //             $dataPorArea["$detalle->detalle_area"] = [
        //                 'certificado' => $certificado,
        //                 'detalles' => []
        //             ];
        //         }

        //         $dataPorArea["$detalle->detalle_area"]['detalles'][] = [
        //             'ensayo' => $detalle->detalle_ea,
        //             'ponderacion' => $detalle->calificacion_certificado,
        //         ];
        //     }
        // }
        return $dataPorArea;
    }

    public function getDataCertificadoParticipacion(string $gestion)
    {
        $query = $this->inscripciones()
            ->Aprobado()
            ->whereHas('certificado', fn($query) => $query->Publicado())
            ->where('gestion', $gestion);
        $query = $this->inscripciones()
            ->Aprobado()
            ->whereHas('certificado', fn($query) => $query->Publicado())
            ->where('gestion', $gestion);
        $ensayosA = $query
            ->whereHas('certificado.detalles')
            ->with(['certificado.detalles'])
            ->get()
            ->pluck('certificado.detalles')
            ->flatten()
            ->pluck('detalle_ea')
            ->implode(', ');
        return $ensayosA;
    }

    public function getStatusRaw()
    {
        if ($this->status) {
            return "<span class='inline-flex items-center px-2 py-0.5 bg-green-100 text-green-800 text-xs font-medium rounded shadow-sm'>
                        " . self::STATUS[$this->status] . "
                    </span>";
        } else {
            return "<span class='inline-flex items-center px-2 py-0.5 bg-red-100 text-red-800 text-xs font-medium rounded shadow-sm'>
                        " . self::STATUS[$this->status] . "
                    </span>";
        }
    }

    protected function telefono(): Attribute
    {
        return Attribute::make(
            get: fn() => "{$this->usuario->telefono}"
        );
    }
    protected function username(): Attribute
    {
        return Attribute::make(
            get: fn() => "{$this->usuario->username}"
        );
    }

    // scope para determinar las si se tiene deuda pendiente
    public function deudasPendientes(): mixed
    {
        $gestion = configuracion(Configuracion::GESTION_INSCRIPCION);

        return $this->inscripciones()
            ->Aprobado()
            ->Pendiente()
            ->where('gestion', '<', $gestion)
            ->with(['detalleInscripciones', 'pagos'])
            ->get();
    }

    public function descargarCertificadoParticipacion($gestion)
    {
        $query = $this->inscripciones()
            ->Aprobado()
            ->whereHas('certificado', fn($query) => $query->Publicado())
            ->where('gestion', $gestion);

        $ins = $query->with('certificado')
            ->first();
        $certificado = $ins->certificado;
        $codigoCertificado = $ins->id;
        $query = $this->inscripciones()
            ->Aprobado()
            ->whereHas('certificado', fn($query) => $query->Publicado())
            ->where('gestion', $gestion);
        $ensayosA = $query
            ->whereHas('certificado.detalles')
            ->with(['certificado.detalles'])
            ->get()
            ->pluck('certificado.detalles')
            ->flatten()
            ->pluck('detalle_ea')
            ->implode(', ');
        $url = route('verificar.certificado', ['code' => $codigoCertificado, 'type' => Certificado::TYPE_PARTICIPACION]);
        $qr = base64_encode(
            QrCode::format('png')->size(220)->margin(1)->generate($url)
        );
        $pdf = Pdf::loadView('certificados.pdf.participacion', ['ensayosA' => $ensayosA, 'certificado' => $certificado, 'qr' => $qr])
            ->setPaper('A4', 'portrait');
        $pdf->getDomPDF()->getOptions()->set('isHtml5ParserEnabled', true);
        return $pdf->stream('certificados-particiapcion.pdf');
    }

    public function descargarCertificadoDesemp($gestion)
    {
        $inscripciones = $this->inscripciones()
            ->Aprobado()
            ->whereHas('certificado', fn($query) => $query->Publicado())
            ->where('gestion', $gestion)
            ->whereHas('certificado.detalles', fn($query) => $query->whereNotNull('calificacion_certificado'))
            ->with(['certificado.detalles'])
            ->get();
        if ($inscripciones->isEmpty()) {
            return redirect('/')
                ->with('info', '⚠️ No se encontraron certificados registrados para la gestión seleccionada.');
        }
        $dataPorArea = [];
        $codigoCertificado = '';
        foreach ($inscripciones as $inscripcion) {
            $certificado = $inscripcion->certificado;
            $detalles = $certificado->detalles;

            if ($detalles->isEmpty()) continue;

            foreach ($detalles as $detalle) {
                if (is_null($detalle->calificacion_certificado)) continue;

                if (!isset($dataPorArea["$detalle->detalle_area"])) {
                    $dataPorArea["$detalle->detalle_area"] = [
                        'certificado' => $certificado,
                        'detalles' => []
                    ];
                }

                $dataPorArea["$detalle->detalle_area"]['detalles'][] = [
                    'ensayo' => $detalle->detalle_ea,
                    'ponderacion' => $detalle->calificacion_certificado,
                ];
                $codigoCertificado = $inscripcion->id;
            }
        }
        $url = route('verificar.certificado', ['code' => $codigoCertificado, 'type' => Certificado::TYPE_DESEMP]);
        $qr = base64_encode(
            QrCode::format('png')->size(400)->margin(1)->generate($url)
        );
        $pdf = Pdf::loadView('certificados.pdf.desemp', ['data' => $dataPorArea, 'qr' => $qr])
            ->setPaper('A4', 'portrait');
        $pdf->getDomPDF()->getOptions()->set('isHtml5ParserEnabled', true);

        $response = $pdf->stream('certificados-desempeno.pdf');
        return $response;
    }
}
