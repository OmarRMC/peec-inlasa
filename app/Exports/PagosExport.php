<?php

namespace App\Exports;

use App\Models\Inscripcion;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PagosExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection(): Collection
    {
        $gestion = $this->filters['gestion'] ?? now()->year;

        $inscripciones = Inscripcion::with([
            'laboratorio',
            'pagos',
            'detalleInscripciones'
        ])
            ->where('gestion', $gestion)
            ->whereHas('pagos')
            ->orderBy('fecha_inscripcion', 'desc')
            ->get();

        $nro = 0;

        return $inscripciones->map(function ($ins) use (&$nro) {
            $totalPagado = $ins->pagos->sum('monto_pagado');
            $saldo = $ins->costo_total - $totalPagado;

            // Preparar detalle de pagos completo
            $detallePagos = $ins->pagos->map(function ($pago) {
                $items = [];

                $items[] = 'Fecha: ' . \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y');
                $items[] = 'Monto: Bs ' . number_format($pago->monto_pagado, 2);
                $items[] = 'Tipo: ' . $pago->tipo_transaccion;

                if ($pago->nro_tranferencia) {
                    $items[] = 'N° Transf.: ' . $pago->nro_tranferencia;
                }

                if ($pago->nro_factura) {
                    $items[] = 'Factura: ' . $pago->nro_factura;
                }

                if ($pago->razon_social) {
                    $items[] = 'Razón Social: ' . $pago->razon_social;
                }

                if ($pago->obs_pago) {
                    $items[] = 'Obs: ' . $pago->obs_pago;
                }

                return implode(' | ', $items);
            })->implode("\n");

            return [
                'NRO' => ++$nro,
                'Código Laboratorio' => $ins->laboratorio->cod_lab ?? '',
                'Laboratorio' => $ins->laboratorio->nombre_lab ?? '',
                'Paquetes Inscritos' => $ins->detalleInscripciones
                    ->pluck('descripcion_paquete')
                    ->implode(', '),
                'Gestión' => $ins->gestion,
                'Fecha Inscripción' => $ins->fecha_inscripcion,
                '# de Pagos' => $ins->pagos->count(),
                'Total Pagado' => $totalPagado,
                'Saldo' => $saldo,
                'Estado' => $saldo > 0 ? 'Pendiente' : 'Pagado',
                'Detalle de Pagos' => $detallePagos,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'NRO',
            'Código Laboratorio',
            'Laboratorio',
            'Paquetes Inscritos',
            'Gestión',
            'Fecha Inscripción',
            '# de Pagos',
            'Total Pagado',
            'Saldo',
            'Estado',
            'Detalle de Pagos',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestColumn = $sheet->getHighestColumn();

        // Encabezados
        $sheet->getStyle("A1:{$highestColumn}1")->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => '1F4E78'],
            ],
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 9,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        // Ajuste de alineación para toda la tabla
        $sheet->getStyle("A2:{$highestColumn}{$sheet->getHighestRow()}")
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_TOP)
            ->setWrapText(true);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 18,
            'C' => 30,
            'D' => 35,
            'E' => 10,
            'F' => 15,
            'G' => 12,
            'H' => 15,
            'I' => 12,
            'J' => 12,
            'K' => 80,
        ];
    }
}
