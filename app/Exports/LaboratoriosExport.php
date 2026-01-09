<?php

namespace App\Exports;

use App\Models\Laboratorio;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LaboratoriosExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * Datos
     */
    public function collection(): Collection
    {
        $query = Laboratorio::query()
            ->with([
                'pais',
                'departamento',
                'provincia',
                'municipio',
                'tipo',
                'categoria'
            ]);

        if ($this->request->pais) {
            $query->where('id_pais', $this->request->pais);
        }

        if ($this->request->dep) {
            $query->where('id_dep', $this->request->dep);
        }

        if ($this->request->prov) {
            $query->where('id_prov', $this->request->prov);
        }

        if ($this->request->mun) {
            $query->where('id_municipio', $this->request->mun);
        }

        if ($this->request->tipo) {
            $query->where('id_tipo', $this->request->tipo);
        }

        if ($this->request->categoria) {
            $query->where('id_categoria', $this->request->categoria);
        }

        if ($this->request->status !== null && $this->request->status !== '') {
            $query->where('status', $this->request->status);
        }

        if ($this->request->gestionesDisponibles != null && $this->request->gestionesDisponibles !== '') {
            $query->whereYear('created_at', $this->request->gestionesDisponibles);
        }

        if ($this->request->search) {
            $query->where(function ($q) {
                $q->where('nombre_lab', 'like', '%' . $this->request->search . '%')
                    ->orWhere('cod_lab', 'like', '%' . $this->request->search . '%')
                    ->orWhere('mail_lab', 'like', '%' . $this->request->search . '%');
            });
        }

        $nro = 0;

        return $query->orderBy('created_at', 'desc')->get()->map(function ($lab) use (&$nro) {
            $nro++;

            return [
                $nro,
                $lab->cod_lab,
                $lab->nombre_lab,
                $lab->sigla_lab,
                optional($lab->tipo)->descripcion,
                optional($lab->categoria)->descripcion,
                $lab->respo_lab,
                $lab->ci_respo_lab,
                $lab->mail_lab,
                $lab->wapp_lab,
                optional($lab->pais)->nombre_pais,
                optional($lab->departamento)->nombre_dep,
                optional($lab->provincia)->nombre_prov,
                optional($lab->municipio)->nombre_municipio,
                $lab->direccion_lab,
                $lab->status ? 'Activo' : 'Inactivo',
                $lab->created_at
            ];
        });
    }

    /**
     * Cabeceras
     */
    public function headings(): array
    {
        return [
            'NRO',
            'Código',
            'Nombre Laboratorio',
            'Sigla',
            'Tipo',
            'Categoría',
            'Responsable',
            'CI Responsable',
            'Correo',
            'WhatsApp',
            'País',
            'Departamento',
            'Provincia',
            'Municipio',
            'Dirección',
            'Estado',
            'Fecha Registro',
        ];
    }

    /**
     * Estilos
     */
    public function styles(Worksheet $sheet)
    {
        $highestColumn = $sheet->getHighestColumn();

        // 🎨 Cabecera principal
        $sheet->getStyle("A1:{$highestColumn}1")->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => '1F4E78'], // Azul institucional
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

        return [
            1 => ['alignment' => ['wrapText' => true]],
        ];
    }

    /**
     * Ancho de columnas
     */
    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 15,
            'C' => 35,
            'D' => 15,
            'E' => 20,
            'F' => 20,
            'G' => 25,
            'H' => 18,
            'I' => 25,
            'J' => 18,
            'K' => 20,
            'L' => 22,
            'M' => 22,
            'N' => 22,
            'O' => 35,
            'P' => 15,
            'Q' => 18,
        ];
    }
}
