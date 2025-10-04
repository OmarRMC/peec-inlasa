<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LaboratorioTem extends Model
{
    use SoftDeletes;
    protected $table = 'laboratorio_tem';
    protected $fillable = [
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
        'telefono',
        'password',
    ];

    public function getDireccionLabTemAttribute()
    {
        $valor = $this->attributes['direccion_lab'] ?? '';
        $partes = explode('||', $valor);

        return ($partes[0] ?? '')
            .  (isset($partes[1]) ? ' Nro:' . $partes[1] :  '')
            . ' ' . ($partes[2] ?? '');
    }
}
