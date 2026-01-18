<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Inscripcion extends Model
{
    protected $table = 'inscripcion';

    const STATUS_EN_REVISION = 1;
    const STATUS_VENCIDO = 2;
    const STATUS_ANULADO = 3;
    const STATUS_APROBADO = 4;
    const STATUS_EN_OBSERVACION = 5;
    // 4 -> Aprobado 
    // 1 -> En revision
    // 3 -> Anulado
    // 2 -> Vencido
    const STATUS_PAGADO = 1;

    const STATUS_DEUDOR = 2;

    const STATUS_INSCRIPCION = [
        self::STATUS_EN_REVISION => 'En revision',
        self::STATUS_APROBADO => 'Aprobado',
        self::STATUS_VENCIDO => 'Vencido',
        self::STATUS_ANULADO => 'Anulado',
        self::STATUS_EN_OBSERVACION => 'En observación'
    ];

    const STATUS_INSCRIPCION_VALIDO = [
        self::STATUS_EN_REVISION => 'En revision',
        self::STATUS_APROBADO => 'Aprobado/Vencido',
        self::STATUS_ANULADO => 'Anulado',
        self::STATUS_EN_OBSERVACION => 'En observación'
    ];

    const STATUS_CUENTA = [
        self::STATUS_PAGADO => 'Pagado',
        self::STATUS_DEUDOR => 'Pendiente',
    ];
    const STATUS_DOCUMENTO_PAGO = [
        DocumentoInscripcion::STATUS_DOCUMENTO_REGISTRADO => 'Registrados',
        DocumentoInscripcion::STATUS_DOCUMENTO_PENDIENTE  => 'Pendientes',
    ];

    protected $fillable = [
        'id_lab',
        'id_contrato',
        'id_formulario',
        'cant_paq',
        'costo_total',
        'obs_inscripcion',
        'fecha_inscripcion',
        'status_cuenta',
        'status_inscripcion',
        'created_by',
        'updated_by',
        'fecha_limite_pago',
        'gestion',
        'ulid'
    ];

    public function estaEnRevision(): bool
    {
        return $this->status_inscripcion === self::STATUS_EN_REVISION;
    }

    // Verifica si el estado es "Aprobado"
    public function estaAprobado(): bool
    {
        return $this->status_inscripcion === self::STATUS_APROBADO;
    }

    // Verifica si el estado es "Vencido"
    public function estaVencido(): bool
    {
        return $this->status_inscripcion === self::STATUS_VENCIDO;
    }


    public function estaAnulado(): bool
    {
        return $this->status_inscripcion === self::STATUS_ANULADO;
    }

    // Verifica si el estado de cuenta es "Pagado"
    public function estaPagado(): bool
    {
        return $this->status_cuenta === self::STATUS_PAGADO;
    }

    // Verifica si el estado de cuenta es "Deudor"
    public function esDeudor(): bool
    {
        return $this->status_cuenta === self::STATUS_DEUDOR;
    }
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

    public function documentosInscripcion()
    {
        return $this->hasMany(DocumentoInscripcion::class, 'id_inscripcion')
            ->where('tipo', DocumentoInscripcion::TYPE_DOCUMENTO_INSCRIPCION);
    }

    public function documentosPago()
    {
        return $this->hasMany(DocumentoInscripcion::class, 'id_inscripcion')
            ->where('tipo', DocumentoInscripcion::TYPE_DOCUMENTO_PAGO);
    }

    public function vigencia()
    {
        return $this->hasOne(VigenciaInscripcion::class, 'id_inscripcion');
    }

    public function ensayos()
    {
        return $this->hasMany(InscripcionEA::class, 'id_inscripcion');
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)
            ->timezone('America/La_Paz')
            ->format('d/m/Y H:i');
    }

    public function getFechaInscripcionAttribute($value)
    {
        return formatDate($value);
    }

    public function getStatusInscripcionLabelAttribute(): string
    {
        if (
            $this->status_inscripcion === self::STATUS_APROBADO &&
            $this->gestion < now()->year
        ) {
            return self::STATUS_INSCRIPCION[self::STATUS_APROBADO]
                . '/'
                . self::STATUS_INSCRIPCION[self::STATUS_VENCIDO];
        }
        return self::STATUS_INSCRIPCION[$this->status_inscripcion] ?? 'Desconocido';
    }
    public function getStatusInscripcion()
    {
        switch ($this->status_inscripcion) {
            case self::STATUS_EN_REVISION:
                return "<span class='inline-flex items-center px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs font-medium rounded shadow-sm'>
                    " . self::STATUS_INSCRIPCION[$this->status_inscripcion] . "
                </span>";
            case self::STATUS_APROBADO:
                return "<span class='inline-flex items-center px-2 py-0.5 bg-green-100 text-green-800 text-xs font-medium rounded shadow-sm'>
                    " . self::STATUS_INSCRIPCION[$this->status_inscripcion] . "
                </span>";
            case self::STATUS_VENCIDO:
                return "<span class='inline-flex items-center px-2 py-0.5 bg-orange-100 text-orange-800 text-xs font-medium rounded shadow-sm'>
                    " . self::STATUS_INSCRIPCION[$this->status_inscripcion] . "
                </span>";
            case self::STATUS_ANULADO:
                return "<span class='inline-flex items-center px-2 py-0.5 bg-gray-300 text-gray-800 text-xs font-medium rounded shadow-sm'>
                    " . self::STATUS_INSCRIPCION[$this->status_inscripcion] . "
                </span>";
            case self::STATUS_EN_OBSERVACION:
                return "<span class='inline-flex items-center px-2 py-0.5 bg-red-100 text-red-800 text-xs font-medium rounded shadow-sm'>
                    " . self::STATUS_INSCRIPCION[$this->status_inscripcion] . "
                </span>";
            default:
                return "<span class='inline-flex items-center px-2 py-0.5 bg-gray-100 text-gray-800 text-xs font-medium rounded shadow-sm'>
                    ...
                </span>";
        }
    }

    public function getStatusCuenta()
    {
        switch ($this->status_cuenta) {
            case self::STATUS_PAGADO:
                return "<span class='inline-flex items-center px-2 py-0.5 bg-green-100 text-green-800 text-xs font-medium rounded shadow-sm'>
                        " . self::STATUS_CUENTA[$this->status_cuenta] . "
                    </span>";
            case self::STATUS_DEUDOR:
                return "<span class='inline-flex items-center px-2 py-0.5 bg-red-100 text-red-800 text-xs font-medium rounded shadow-sm'>
                        " . self::STATUS_CUENTA[$this->status_cuenta] . "
                    </span>";
            default:
                return "<span class='inline-flex items-center px-2 py-0.5 bg-gray-100 text-gray-800 text-xs font-medium rounded shadow-sm'>
                        Desconocido
                    </span>";
        }
    }

    public function getEstadoInscripcionTextoAttribute()
    {
        return self::STATUS_INSCRIPCION[$this->status_inscripcion] ?? 'Desconocido';
    }

    public function getEstadoPagoTextoAttribute()
    {
        return self::STATUS_CUENTA[$this->status_cuenta] ?? 'Desconocid1o';
    }

    public function getSaldoAttribute()
    {
        $pagos = $this->pagos->where('status', true)->sum('monto_pagado');
        return $this->costo_total - $pagos;
    }

    public function scopeAprobado($query)
    {
        return $query->where('status_inscripcion', Inscripcion::STATUS_APROBADO);
    }

    public function scopePendiente($query)
    {
        return $query->where('status_cuenta', Inscripcion::STATUS_DEUDOR);
    }

    public function certificado()
    {
        return $this->hasOne(Certificado::class, 'id_inscripcion');
    }

    public function getPaquetesDescripcionAttribute(): string
    {
        return $this->detalleInscripciones->pluck('descripcion_paquete')->implode(', ');
    }

    public function scopeAprobadoOrVencido($query)
    {
        return $query->whereIn('status_inscripcion', [
            self::STATUS_APROBADO,
            self::STATUS_VENCIDO,
        ]);
    }

    public function getPaquetesAttribute(): array
    {
        $detalles = $this->detalleInscripciones()->select('id_paquete', 'descripcion_paquete')->get();

        return $detalles
            ->map(function ($detalle) {
                return [
                    'id_paquete' => $detalle->id_paquete,
                    'descripcion_paquete' => $detalle->descripcion_paquete,
                ];
            })
            ->filter(function ($p) {
                return !is_null($p['id_paquete']);
            })
            ->values()
            ->all();
    }


    public static function agruparPorLaboratorio(Collection $inscripciones): Collection
    {
        $groups = [];

        foreach ($inscripciones as $inscripcion) {
            $key = $inscripcion->id_lab . '_' . $inscripcion->status_inscripcion;

            $saldoInscripcion = max(0, (float) ($inscripcion->saldo ?? 0));
            $paquetesActuales = $inscripcion->paquetes ?? [];

            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'gestion' => $inscripcion->gestion,
                    'id_lab' => $inscripcion->id_lab,
                    'laboratorio' => $inscripcion->laboratorio ?? null,

                    'status_inscripcion_label' => $inscripcion->getStatusInscripcion(),
                    'status_cuenta_label' => $inscripcion->getStatusCuenta(),
                    'status_inscripcion' =>  $inscripcion->status_inscripcion_label,
                    'inscripciones_count' => 1,
                    'costo_total' => (float) $inscripcion->costo_total,
                    'saldo_total' => (float) $saldoInscripcion,
                    'paquetes' => $paquetesActuales,
                    'ultima_fecha_inscripcion' => $inscripcion->fecha_inscripcion,
                    'inscripciones_ids' => [$inscripcion->id],
                    'deuda_pendiente' => $saldoInscripcion > 0,
                ];
            } else {
                $groups[$key]['inscripciones_count']++;
                $groups[$key]['costo_total'] += (float) $inscripcion->costo_total;
                $groups[$key]['saldo_total'] += (float) $saldoInscripcion;

                $groups[$key]['paquetes'] = collect($groups[$key]['paquetes'])
                    ->merge($paquetesActuales)
                    ->reject(fn($p) => empty($p['id_paquete']))
                    ->unique('id_paquete')
                    ->values()
                    ->toArray();

                $groups[$key]['inscripciones_ids'][] = $inscripcion->id;

                $timestampNueva = Carbon::createFromFormat(getFormat(), $inscripcion->created_at)->getTimestampMs();
                $timestampUltima = Carbon::createFromFormat(getFormat(), $groups[$key]['ultima_fecha_inscripcion'])->getTimestampMs();

                if ($timestampNueva > $timestampUltima) {
                    $groups[$key]['ultima_fecha_inscripcion'] = $inscripcion->fecha_inscripcion;
                }

                $groups[$key]['deuda_pendiente'] =
                    $groups[$key]['deuda_pendiente'] || ($saldoInscripcion > 0);
            }
        }

        return collect($groups)->values();
    }

    public function scopeEnEspera($query)
    {
        return $query->whereIn('status_inscripcion', [
            self::STATUS_APROBADO,
            self::STATUS_VENCIDO,
            self::STATUS_EN_REVISION,
            self::STATUS_EN_OBSERVACION,
        ]);
    }

    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'id_contrato')->with('detallesActivos');
    }

    public static function rangoGestion(array $filters = []): array
    {
        $query = self::query();
        if (!empty($filters['status_inscripcion'])) {
            $query->whereIn('status_inscripcion', (array) $filters['status_inscripcion']);
        }

        if (!empty($filters['status_cuenta'])) {
            $query->whereIn('status_cuenta', (array) $filters['status_cuenta']);
        }

        if (!empty($filters['id_lab'])) {
            $query->where('id_lab', $filters['id_lab']);
        }

        if (!empty($filters['fecha_desde'])) {
            $query->whereDate('fecha_inscripcion', '>=', $filters['fecha_desde']);
        }

        if (!empty($filters['fecha_hasta'])) {
            $query->whereDate('fecha_inscripcion', '<=', $filters['fecha_hasta']);
        }
        $result = $query->selectRaw('MIN(gestion) AS min_g, MAX(gestion) AS max_g')->first();
        if (!$result->min_g || !$result->max_g) {
            return [];
        }
        return array_reverse(range($result->min_g, $result->max_g));
    }
}
