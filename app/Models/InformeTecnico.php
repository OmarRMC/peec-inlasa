<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class InformeTecnico extends Model
{
    use HasFactory;

    protected $table = 'informe_tecnico';

    const ESTADO_SUBIDO = 1;
    protected $fillable = [
        'ulid',
        'id_ciclo',
        'id_laboratorio',
        'gestion',
        'reporte',
        'estado',
        'created_by',
        'updated_by',
    ];
    // public $timestamps = true;

    public function getFechaRegistroAttribute()
    {
        return $this->created_at
            ? formatDate($this->created_at)
            : null;
    }

    public function getFechaActualizacionAttribute()
    {
        return $this->updated_at
            ? formatDate($this->updated_at)
            : null;
    }

    public function ciclo()
    {
        return $this->belongsTo(Ciclo::class, 'id_ciclo');
    }

    public function laboratorio()
    {
        return $this->belongsTo(Laboratorio::class, 'id_laboratorio');
    }

    public function getLinkReporteHtmlAttribute()
    {
        if (!$this->reporte || !Storage::disk('public')->exists($this->reporte)) {
            return ' <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-red-500 bg-red-50 text-red-700 cursor-default select-none text-xs font-medium">No disponible</span>';
        }
        $url = asset('storage/' . $this->reporte);
        return sprintf(
            '<a href="%s" target="_blank"
                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-green-600 text-green-700 hover:bg-green-50 transition text-xs font-medium"
                title="Ver informe técnico">
                <i class="fas fa-file-zipper"></i> Ver informe
            </a>',
            $url
        );
    }

    public function getBtnEliminarHtmlAttribute()
    {
        return '<button
            data-id="' . $this->id . '"
            class="btn-eliminar-informe bg-red-100 hover:bg-red-200 text-red-700 px-2 py-1 rounded shadow-sm">
            <i class="fas fa-trash-alt"></i>
        </button>';
    }

    public function cicloPorGestion($gestion)
    {
        return $this->belongsTo(Ciclo::class, 'id_ciclo')
            ->whereYear('fecha_inicio_envio_reporte', $gestion);
    }
}
