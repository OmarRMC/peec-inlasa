<?php

namespace App\Exports;

use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ResultadosExport implements FromCollection, WithHeadings, WithEvents
{
    protected $estructura;
    protected $idFormulario;
    public function __construct($id)
    {
        $this->idFormulario = $id;
        $this->estructura = \App\Models\FormularioEnsayo::with('secciones.parametros.campos')->where('id', $id)->first();
    }

    public function collection()
    {
        return collect([]);
    }

    public function headings(): array
    {
        $row1 = [];
        $row2 = [];
        $row3 = [];

        for ($i = 0; $i < 6; $i++) {
            $row1[] = ' ';
            $row2[] = 'Laboratorio';
        }
        $row3[] = 'GESTIÓN';
        $row3[] = 'CICLO';
        $row3[] = 'LABORATORIO';
        $row3[] = 'DEPARTAMENTO';
        $row3[] = 'CÓDIGO';
        $row3[] = 'FECHA DE ENVIO';

        foreach ($this->estructura->secciones as $seccion) {
            foreach ($seccion->parametros as $parametro) {
                $countCampos = 0;
                foreach ($parametro->campos as $campo) {
                    $countCampos++;
                    if ($campo->unidad) {
                        $countCampos++;
                    }
                }
                $headersNecesarios = array_slice($seccion->headers, -$countCampos);
                foreach ($headersNecesarios as $key => $head) {
                    $row1[] = $seccion->nombre;
                    $row2[] = $parametro->nombre;
                    $row3[] = $head;
                }
            }
        }

        return [$row1, $row2, $row3];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $delegate = $sheet->getDelegate();

                $lastColumnIndex = count($this->headings()[0]);
                $lastColumn = Coordinate::stringFromColumnIndex($lastColumnIndex);

                $headerRange = "A1:{$lastColumn}3";
                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'wrapText'   => true,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color'    => ['rgb' => '4F81BD'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['rgb' => '000000'],
                        ]
                    ]
                ]);
                foreach ([1, 2] as $rowIndex) {
                    $rowValues = $delegate->rangeToArray("A{$rowIndex}:{$lastColumn}{$rowIndex}")[0];

                    $start = 0;
                    $prev = $rowValues[0];
                    for ($i = 1; $i <= count($rowValues); $i++) {
                        if ($i == count($rowValues) || $rowValues[$i] !== $prev) {
                            if ($i - 1 > $start) {
                                $startCol = Coordinate::stringFromColumnIndex($start + 1);
                                $endCol   = Coordinate::stringFromColumnIndex($i);
                                $sheet->mergeCells("{$startCol}{$rowIndex}:{$endCol}{$rowIndex}");
                            }
                            if ($i < count($rowValues)) {
                                $prev = $rowValues[$i];
                                $start = $i;
                            }
                        }
                    }
                }
                $delegate->getRowDimension(1)->setRowHeight(25);
                $delegate->getRowDimension(2)->setRowHeight(25);
                $delegate->getRowDimension(3)->setRowHeight(25);
            }
        ];
    }
}
