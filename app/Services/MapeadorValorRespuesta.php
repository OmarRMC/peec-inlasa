<?php

namespace App\Services;

use Carbon\Carbon;

class MapeadorValorRespuesta
{
    /**
     * Convierte el shape del frontend ({id, label, tipo, valor, valorTexto, unidad})
     * a las columnas tipadas de respuestas_campo.
     */
    public static function mapear(array $campoFrontend, ?string $nombreParametro = null): array
    {
        $tipo  = $campoFrontend['tipo'] ?? 'text';
        $valor = $campoFrontend['valor'] ?? null;
        $valorTexto = $campoFrontend['valorTexto'] ?? null;

        $base = [
            'tipo_campo'             => $tipo,
            'nombre_parametro'       => $nombreParametro,
            'valor_texto'            => null,
            'valor_numero'           => null,
            'valor_fecha'            => null,
            'valor_booleano'         => null,
            'id_opcion_seleccionada' => null,
            'valor_texto_opcion'     => null,
        ];

        switch ($tipo) {
            case 'number':
                $base['valor_numero'] = is_numeric($valor) ? $valor : null;
                break;

            case 'date':
                try {
                    $base['valor_fecha'] = !empty($valor)
                        ? Carbon::parse($valor)->format('Y-m-d')
                        : null;
                } catch (\Throwable) {
                    $base['valor_fecha'] = null;
                }
                break;

            case 'checkbox':
                $base['valor_booleano'] = in_array($valor, ['1', 1, true, 'true'], true);
                break;

            case 'select':
            case 'radio':
                if (is_numeric($valor)) {
                    $base['id_opcion_seleccionada'] = (int) $valor;
                } elseif (!empty($valor)) {
                    // selects dependientes pueden persistir texto libre, no id
                    $base['valor_texto'] = (string) $valor;
                }
                $base['valor_texto_opcion'] = $valorTexto;
                break;

            case 'datalist':
                $base['valor_texto'] = $valor !== null ? (string) $valor : null;
                $base['valor_texto_opcion'] = $valorTexto ?: ($valor ?? null);
                break;

            case 'text':
            case 'textarea':
            case 'email':
            case 'file':
            default:
                $base['valor_texto'] = $valor !== null ? (string) $valor : null;
                break;
        }

        return $base;
    }
}
