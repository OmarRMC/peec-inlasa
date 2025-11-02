<?php

namespace App\Exports;

use App\Models\Inscripcion;
use App\Models\Paquete;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class InscripcionesExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $filters;
    protected $paquetes;
    protected $resumen;

    protected $headersLaboratorio;
    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->paquetes = Paquete::active()->orderBy('descripcion')->get();
        $this->resumen = [
            'Cantidad de inscripciones',
            'Saldo',
            'Costo Total',
            'Estado de cuenta',
        ];

        $this->headersLaboratorio = [
            'NRO',
            'Código',
            'Nombre Laboratorio',
            'Correo 1',
            'Correo 2',
            'Sigla',
            'Tipo',
            'Nivel',
            'Categoría',
            'Responsable',
            'CI. Responsable',
            'Representante Legal',
            'CI. Representante Legal',
            'Departamento',
            'Provincia',
            'Municipio',
            'Dirección',
            'Cel. 1',
            'Cel. 2',
        ];
    }

    public function collection()
    {
        $inscripciones = Inscripcion::all();
        $query = Inscripcion::query()
            ->aprobadoOrVencido()
            ->with(['detalleInscripciones', 'laboratorio.tipo', 'laboratorio.categoria', 'laboratorio.nivel', 'pagos', 'laboratorio.departamento', 'laboratorio.provincia', 'laboratorio.municipio']);

        if (isset($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('id_lab', 'like', "%{$search}%")
                    ->orWhereHas('laboratorio', function ($q2) use ($search) {
                        $q2->where('nombre_lab', 'like', "%{$search}%")
                            ->orWhere('mail_lab', 'like', "%{$search}%")
                            ->orWhere('cod_lab', 'like', "%{$search}%");
                    });
            });
        }

        if (isset($this->filters['fecha_desde'])) {
            $query->whereDate('created_at', '>=', $this->filters['fecha_desde']);
        }

        if (isset($this->filters['fecha_hasta'])) {
            $query->whereDate('created_at', '<=', $this->filters['fecha_hasta']);
        }
        if (isset($this->filters['gestion'])) {
            $query->where('gestion', $this->filters['gestion']);
        } else {
            $query->where('gestion', now()->year);
        }

        $inscripciones = $query
            ->orderBy('created_at', 'desc')->get();
        $agrupadas = Inscripcion::agruparPorLaboratorio($inscripciones);
        $numRegistros = 0;
        $data  = $agrupadas->map(function ($item, $index) use (&$numRegistros) {
            $numRegistros++;
            $row = [
                'NRO' => $numRegistros,
                'Código' => $item['laboratorio']->cod_lab,
                'Nombre Laboratorio' => $item['laboratorio']->nombre_lab,
                'Correo 1' => $item['laboratorio']->mail_lab,
                'Correo 2' => $item['laboratorio']->mail2_lab ?? '',
                'Sigla' => $item['laboratorio']->sigla_lab ?? '',
                'Tipo' => optional($item['laboratorio']->tipo)->descripcion,
                'Nivel' => optional($item['laboratorio']->nivel)->descripcion_nivel,
                'Categoria' => optional($item['laboratorio']->categoria)->descripcion,
                'Responsable' => $item['laboratorio']->respo_lab,
                'CI. Responsable' => $item['laboratorio']->ci_respo_lab,
                'Representante Legal' => $item['laboratorio']->repreleg_lab,
                'CI. Representante Legal' => $item['laboratorio']->ci_repreleg,
                'Departamento' => optional($item['laboratorio']->departamento)->nombre_dep,
                'Provincia' => optional($item['laboratorio']->provincia)->nombre_prov,
                'Municipio' => optional($item['laboratorio']->municipio)->nombre_municipio,
                'Direccion' => $item['laboratorio']->direccion_lab,
                'Cel. 1' => $item['laboratorio']->wapp_lab,
                'Cel. 2' => $item['laboratorio']->wapp2_lab,
            ];

            foreach ($this->paquetes as $paquete) {
                $idPaquete = $paquete->id;
                $existe = collect($item['paquetes'])->contains(function ($p) use ($idPaquete) {
                    return $p['id_paquete'] == $idPaquete;
                });
                $row[$paquete->descripcion] = $existe ? 1 : 0;
            }
            $row['Cantidad de inscripciones'] = $item['inscripciones_count'] ?? 0;
            $row['Saldo'] = $item['saldo_total'] ?? '';
            $row['Costo Total'] = $item['costo_total'];
            $row['Estados de cuenta'] = $item['deuda_pendiente'] ? Inscripcion::STATUS_CUENTA[Inscripcion::STATUS_DEUDOR] : Inscripcion::STATUS_CUENTA[Inscripcion::STATUS_PAGADO];
            return $row;
        });
        return $data;
    }

    public function headings(): array
    {

        $descripciones = $this->paquetes->pluck('descripcion')->toArray();

        $headers = array_merge($this->headersLaboratorio, $descripciones, $this->resumen);

        return $headers;
    }

    public function styles(Worksheet $sheet)
    {
        $highestColumn = $sheet->getHighestColumn();
        $colorMain = '1F4E78';
        $colorVertical = '7030A0';
        $colorResumen = '2E7D32';

        $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => $colorMain],
            ],
            'font' => [
                'color' => ['rgb' => 'FFFFFF'],
                'bold' => true,
                'size' => 8,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        $headers = $this->headings();

        foreach ($headers as $index => $header) {
            $colLetter = Coordinate::stringFromColumnIndex($index + 1);
            if (!in_array($header, $this->headersLaboratorio)) {
                $sheet->getStyle($colLetter . '1')->getAlignment()->setTextRotation(90);
                $sheet->getStyle($colLetter . '1')->getAlignment()->setWrapText(false); // Sin wrapText
            }
        }
        foreach ($this->headersLaboratorio as $header) {
            $index = array_search($header, $headers);
            if ($index !== false) {
                $colLetter = Coordinate::stringFromColumnIndex($index + 1);
                $sheet->getStyle($colLetter . '1')->getFill()->applyFromArray([
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => $colorVertical],
                ]);
            }
        }
        $lastCols = $this->resumen ?? [];
        foreach ($headers as $index => $header) {
            if (in_array($header, $lastCols)) {
                $colLetter = Coordinate::stringFromColumnIndex($index + 1);
                $sheet->getStyle($colLetter . '1')->getFill()->applyFromArray([
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => $colorResumen],
                ]);
            }
        }

        return [
            1 => ['alignment' => ['wrapText' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 15,
            'C' => 30,
            'D' => 20,
            'E' => 20,
            // Las demás columnas se ajustarán automáticamente
        ];
    }
}
