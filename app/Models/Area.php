<?php

namespace App\Models;

use App\Traits\General;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory, General;

    protected $table = 'area';

    protected $fillable = [
        'id_programa',
        'descripcion',
        'status',
        'max_paquetes_inscribir',
    ];

    // Relación N:1 con Programa
    public function programa()
    {
        return $this->belongsTo(Programa::class, 'id_programa');
    }

    // Relación 1:N con Paquete
    public function paquetes()
    {
        return $this->hasMany(Paquete::class, 'id_area');
    }

    public static function listarConDescripcionUnica($soloActivos = false)
    {
        $query = self::with('programa')->orderBy('descripcion');
        if ($soloActivos) {
            $query->where('status', 1);
        }

        $areas = $query->get();
        $repetidos = $areas->groupBy('descripcion')
            ->filter(fn($g) => $g->count() > 1)
            ->keys();
        return $areas->map(function ($a) use ($repetidos) {
            return (object)[
                'id' => $a->id,
                'descripcion' => $repetidos->contains($a->descripcion)
                    ? "{$a->descripcion} ({$a->programa->descripcion})"
                    : $a->descripcion
            ];
        });
    }
}
