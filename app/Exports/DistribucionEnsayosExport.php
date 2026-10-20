<?php

namespace App\Exports;

use App\Services\Reportes\DistribucionEnsayosCalculator;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DistribucionEnsayosExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
{
    protected array $filtros;
    protected DistribucionEnsayosCalculator $calculator;
    protected ?Collection $datos = null;
    protected ?array $categorias = null;

    public function __construct(array $filtros, DistribucionEnsayosCalculator $calculator)
    {
        $this->filtros = $filtros;
        $this->calculator = $calculator;
    }

    public function collection(): Collection
    {
        $datos = $this->getDatos();
        $cats = $this->getCategorias();

        return $datos->map(function ($fila) use ($cats) {
            $row = [
                $fila['area'],
                $fila['paquete'],
                $fila['ensayo'],
            ];
            foreach ($cats as $cat) {
                $row[] = (int) ($fila['calificaciones'][$cat] ?? 0);
            }
            $total = (int) $fila['total'];
            $noSat = (int) ($fila['calificaciones'][DistribucionEnsayosCalculator::NO_SATISFACTORIO] ?? 0);
            $pctNoSat = $total > 0 ? round(($noSat / $total) * 100, 2) : 0;
            $row[] = $total;
            $row[] = $pctNoSat;
            return $row;
        })->values();
    }

    public function headings(): array
    {
        $cats = $this->getCategorias();
        return array_merge(
            ['Área', 'Paquete', 'Ensayo'],
            array_map(fn($c) => ucwords(strtolower($c)), $cats),
            ['Total', '% No Satisfactorios'],
        );
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestColumn = $sheet->getHighestColumn();
                $headerRange = "A1:{$highestColumn}1";

                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => '4F81BD'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                $sheet->getRowDimension(1)->setRowHeight(28);
                $sheet->freezePane('A2');

                $highestRow = $sheet->getHighestRow();
                if ($highestRow >= 2) {
                    $sheet->getStyle("A2:{$highestColumn}{$highestRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'CCCCCC'],
                            ],
                        ],
                    ]);
                }
            },
        ];
    }

    protected function getDatos(): Collection
    {
        if ($this->datos === null) {
            $this->datos = $this->calculator->agregar($this->filtros);
        }
        return $this->datos;
    }

    protected function getCategorias(): array
    {
        if ($this->categorias === null) {
            $this->categorias = $this->calculator->categoriasPresentes($this->getDatos());
            if (empty($this->categorias)) {
                $this->categorias = [DistribucionEnsayosCalculator::NO_SATISFACTORIO];
            }
        }
        return $this->categorias;
    }
}
