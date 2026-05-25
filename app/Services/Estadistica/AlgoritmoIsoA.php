<?php

namespace App\Services\Estadistica;

/**
 * Algoritmo A robusto de ISO 13528 (Annex C).
 *
 * Estimadores robustos iterativos para consenso de Proficiency Testing.
 * Insensible a outliers. Reemplaza media/desv clasicas cuando hay
 * sospecha de contaminacion en los datos.
 *
 * Inicializacion:
 *   x* = mediana(xi)
 *   s* = 1.4826 * MAD
 *
 * Iteracion:
 *   delta = 1.5 * s*
 *   xi*  = clip(xi, x* - delta, x* + delta)   # Winsorizacion
 *   x*   = media(xi*)
 *   s*   = 1.134 * sqrt(sum((xi* - x*)^2) / (n-1))
 *
 * Converge cuando |x_nuevo - x_viejo| < tol * |x_nuevo| y similar para s.
 * Constantes 1.4826 y 1.134 son factores de correccion para distribucion
 * normal teorica (ISO 13528).
 */
class AlgoritmoIsoA
{
    public const MAX_ITERACIONES_DEFAULT = 50;
    public const TOLERANCIA_DEFAULT = 1e-4;

    /**
     * Calcula media robusta (x*) y desviacion robusta (s*) por Algoritmo A.
     *
     * @param array<int,float> $valores
     * @return array{x_star:?float, s_star:?float, iteraciones:int, convergio:bool, n:int}
     */
    public static function calcular(
        array $valores,
        int $maxIter = self::MAX_ITERACIONES_DEFAULT,
        float $tolerancia = self::TOLERANCIA_DEFAULT,
    ): array {
        $n = count($valores);
        if ($n < 2) {
            return ['x_star' => $n === 1 ? (float) $valores[0] : null, 's_star' => null, 'iteraciones' => 0, 'convergio' => false, 'n' => $n];
        }

        $ordenado = $valores;
        sort($ordenado);

        $xStar = self::medianaOrdenada($ordenado);
        $mad   = self::madOrdenada($ordenado, $xStar);
        $sStar = 1.4826 * $mad;

        // Caso degenerado: todos los valores iguales (o MAD = 0)
        if ($sStar == 0.0) {
            return ['x_star' => $xStar, 's_star' => 0.0, 'iteraciones' => 0, 'convergio' => true, 'n' => $n];
        }

        for ($iter = 1; $iter <= $maxIter; $iter++) {
            $delta = 1.5 * $sStar;
            $xPrime = [];
            foreach ($ordenado as $xi) {
                if ($xi < $xStar - $delta) {
                    $xPrime[] = $xStar - $delta;
                } elseif ($xi > $xStar + $delta) {
                    $xPrime[] = $xStar + $delta;
                } else {
                    $xPrime[] = $xi;
                }
            }
            $xStarNew = array_sum($xPrime) / $n;
            $sumSq = 0.0;
            foreach ($xPrime as $xpi) {
                $sumSq += ($xpi - $xStarNew) ** 2;
            }
            $sStarNew = 1.134 * sqrt($sumSq / ($n - 1));

            $diffX = abs($xStarNew - $xStar);
            $diffS = abs($sStarNew - $sStar);
            $xStar = $xStarNew;
            $sStar = $sStarNew;

            $tolX = max($tolerancia * abs($xStar), 1e-12);
            $tolS = max($tolerancia * abs($sStar), 1e-12);

            if ($diffX < $tolX && $diffS < $tolS) {
                return [
                    'x_star'      => round($xStar, 6),
                    's_star'      => round($sStar, 6),
                    'iteraciones' => $iter,
                    'convergio'   => true,
                    'n'           => $n,
                ];
            }
        }

        return [
            'x_star'      => round($xStar, 6),
            's_star'      => round($sStar, 6),
            'iteraciones' => $maxIter,
            'convergio'   => false,
            'n'           => $n,
        ];
    }

    // --- Helpers ---

    private static function medianaOrdenada(array $ordenado): float
    {
        $n = count($ordenado);
        $mid = intdiv($n, 2);
        return $n % 2 === 1
            ? (float) $ordenado[$mid]
            : ((float) $ordenado[$mid - 1] + (float) $ordenado[$mid]) / 2;
    }

    /** Median Absolute Deviation calculado sobre un array ordenado. */
    private static function madOrdenada(array $ordenado, float $mediana): float
    {
        $desvAbs = array_map(fn ($v) => abs((float) $v - $mediana), $ordenado);
        sort($desvAbs);
        return self::medianaOrdenada($desvAbs);
    }
}
