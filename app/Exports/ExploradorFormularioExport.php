<?php

namespace App\Exports;

use App\Models\Ciclo;
use App\Models\EnsayoAptitud;
use App\Models\FormularioEnsayo;
use App\Services\Estadistica\ExploradorRespuestas;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExploradorFormularioExport implements FromArray, WithEvents, ShouldAutoSize
{
    public function __construct(
        protected EnsayoAptitud $ensayo,
        protected Ciclo $ciclo,
        protected FormularioEnsayo $formulario,
    ) {}

    public function array(): array
    {
        $rows = [];

        // Bloque 1: metadata
        $rows[] = ['EXPLORADOR DESCRIPTIVO DE RESPUESTAS'];
        $rows[] = ['Ensayo:', $this->ensayo->descripcion];
        $rows[] = ['Paquete:', $this->ensayo->paquete->descripcion ?? '—'];
        $rows[] = ['Ciclo:', $this->ciclo->nombre, 'Gestión:', $this->ciclo->gestion];
        $rows[] = ['Formulario:', $this->formulario->nombre];
        $rows[] = ['Generado:', now()->format('Y-m-d H:i')];
        $rows[] = [];

        // Bloque 2: completitud por campo
        $completitud = ExploradorRespuestas::completitudFormulario(
            $this->formulario->id,
            $this->ciclo->id
        );

        $rows[] = ['COMPLETITUD POR CAMPO'];
        $rows[] = ['Campo', 'Respuestas', 'Envíos totales', '% Completitud'];
        foreach ($completitud as $c) {
            $rows[] = [$c->label, $c->n_respuestas, $c->n_envios, $c->pct_completitud];
        }
        $rows[] = [];

        // Bloque 3: stats descriptivos por campo
        $resumen = ExploradorRespuestas::resumenFormulario(
            $this->formulario->id,
            $this->ciclo->id
        );

        $rows[] = ['RESUMEN DESCRIPTIVO POR CAMPO'];
        $rows[] = ['Sección', 'Parámetro', 'Campo', 'Tipo descriptivo', 'Stat resumido'];
        foreach ($resumen as $item) {
            $rows[] = [
                $item->seccion,
                $item->parametro,
                $item->label,
                $item->tipo_descriptivo,
                $item->snippet,
            ];
        }
        $rows[] = [];

        // Bloque 4: detalle numérico por campo evaluable
        $rows[] = ['DETALLE NUMÉRICO'];
        $rows[] = ['Sección', 'Parámetro', 'Campo', 'N', 'Completitud %', 'Media', 'Mediana', 'σ', 'CV%', 'Mín', 'Q1', 'Q3', 'Máx'];
        foreach ($resumen as $item) {
            if ($item->tipo_descriptivo !== 'numerico') continue;
            $d = ExploradorRespuestas::descriptivoNumerico($this->ciclo->id, $item->id_campo);
            if ($d['n'] === 0) continue;
            $rows[] = [
                $item->seccion, $item->parametro, $item->label,
                $d['n'], $d['pct_completitud'],
                $d['media'], $d['mediana'], $d['desv'], $d['cv_pct'],
                $d['minimo'], $d['q1'], $d['q3'], $d['maximo'],
            ];
        }
        $rows[] = [];

        // Bloque 5: detalle categórico (frecuencias)
        $rows[] = ['DETALLE CATEGÓRICO (frecuencias por opción)'];
        $rows[] = ['Campo', 'Opción', 'Cantidad', '%'];
        foreach ($resumen as $item) {
            if ($item->tipo_descriptivo !== 'categorico') continue;
            $freq = ExploradorRespuestas::frecuenciasCategorico($this->ciclo->id, $item->id_campo);
            foreach ($freq as $f) {
                $rows[] = [$item->label, $f->etiqueta, $f->count, $f->pct];
            }
        }

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Título principal
                $sheet->mergeCells('A1:F1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(24);

                // Estilos de headers de bloques: detectar filas que comienzan con texto en mayúsculas
                $highest = $sheet->getHighestRow();
                for ($r = 2; $r <= $highest; $r++) {
                    $val = $sheet->getCell("A{$r}")->getValue();
                    if (!is_string($val)) continue;

                    // Header de bloque (todo mayúsculas, sin valor en B)
                    $valB = $sheet->getCell("B{$r}")->getValue();
                    if (preg_match('/^[A-ZÁÉÍÓÚÑ\s\(\)%áéíóú]+$/u', $val) && empty($valB)) {
                        $sheet->mergeCells("A{$r}:F{$r}");
                        $sheet->getStyle("A{$r}")->applyFromArray([
                            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '6366F1']],
                        ]);
                    }
                }

                // Bordes ligeros a todo el contenido con valores
                $sheet->getStyle("A1:N{$highest}")->applyFromArray([
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_NONE],
                    ],
                ]);
            },
        ];
    }
}
