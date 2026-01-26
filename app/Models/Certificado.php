<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificado extends Model
{
    use HasFactory;

    protected $table = 'certificado';

    const TYPE_DESEMP = 1;
    const TYPE_PARTICIPACION = 2;

    const NAME_TYPE_CERTIFICADO = [
        self::TYPE_DESEMP => 'DESEMPEÑO',
        self::TYPE_PARTICIPACION => 'PARTICIPACIÓN'
    ];

    const STATUS_HABILITADO = 1;
    const STATUS_INHABILITADO = 0;

    const STATUS_CERTIFICADO   = [
        self::STATUS_HABILITADO => 'Habilitado',
        self::STATUS_INHABILITADO => 'Inhabilitado',
    ];
    protected $fillable = [
        'id_inscripcion',
        'gestion_certificado',
        'nombre_laboratorio',
        'status_certificado',
        'created_by',
        'updated_by',
        'cod_lab',
        'publicado',
        'plantilla_certificado_id'
    ];

    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class, 'id_inscripcion');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleCertificado::class, 'id_certificado');
    }

    public function scopePublicado($query)
    {
        return $query->where('publicado', true);
    }

    public function scopeNoPublicado($query)
    {
        return $query->where('publicado', false);
    }
    public function scopeConDetalles($query)
    {
        return $query->has('detalles');
    }


    public static function certificadoParticipacionRaw(string $url)
    {
        return '<a href="' . $url . '"
                    target="_blank"
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-blue-500 text-blue-600 hover:bg-blue-50 transition text-xs font-medium"
                    data-tippy-content="Descargar certificado de participación">
                    <i class="fas fa-file-pdf"></i> PDF
                    </a>
                ';
    }
    public static function certificadoDesepRawHabilitado(string $url)
    {
        return '<a href="' . $url . '"
                    target="_blank"
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-green-600 text-green-700 hover:bg-green-50 transition text-xs font-medium"
                    data-tippy-content="Descargar certificado de desempeño">
                    <i class="fas fa-file-signature"></i> PDF
                </a>';
    }

    public static function certificadoDesepRawDeshabilitado()
    {
        return '<span
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-gray-300 text-gray-400 text-xs font-medium cursor-not-allowed">
                    <i class="fas fa-ban"></i> N/A
                </span>';
    }

    public static function estado($inhabilitado)
    {

        if ($inhabilitado) {
            return "<span class='inline-flex items-center px-2 py-0.5 bg-gray-300 text-gray-800 text-xs font-medium rounded shadow-sm'>
                    " . self::STATUS_CERTIFICADO[self::STATUS_INHABILITADO] . "
                </span>";
        }
        return "<span class='inline-flex items-center px-2 py-0.5 bg-green-100 text-green-800 text-xs font-medium rounded shadow-sm'>
                    " . self::STATUS_CERTIFICADO[self::STATUS_HABILITADO] . "
                </span>";
    }

    public function plantilla()
    {
        return $this->belongsTo(PlantillaCertificado::class, 'plantilla_certificado_id');
    }
}
