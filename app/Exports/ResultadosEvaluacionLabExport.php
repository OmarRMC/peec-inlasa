<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use App\Models\FormularioEnsayo;
use App\Models\FormularioEnsayoResultado;
use App\Models\RespuestaFormulario;
use Carbon\Carbon;

class ResultadosEvaluacionLabExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
{
    protected $formularios; // Collection de FormularioEnsayo (con resultados)
    protected $formRanges = []; // rangos por formulario (para merge y color)
    protected $maxEnviosPorForm = []; // [form_id => maxEnvios]
    protected $ordenCamposPorForm = []; // [form_id => array de campoIds en el orden de columnas]
    protected $respuestasIndex = []; // [resultado_id => [parametro_id => respuestasArray]]
    protected $labsIndex = []; // [lab_id => ['gestion'=>..,'id_ciclo'=>..,'nombre_lab'=>..,...]]

    protected $sinDato;
    /**
     * $formulariosCollection: collection de FormularioEnsayo ya cargado con ->resultados y relaciones de estructura.
     */
    public function __construct(Collection $formulariosCollection, $sinDato = false)
    {
        $this->sinDato = $sinDato;
        // Esperamos que la colección venga con 'resultados' eager loaded
        $this->formularios = $formulariosCollection->map(function ($f) {
            // Si no trae estructura completa, lo cargamos:
            if (!isset($f->secciones)) {
                $f = FormularioEnsayo::with('secciones.parametros.campos')->find($f->id);
            }
            return $f;
        });

        $this->prepareMeta();
    }

    /**
     * Prepara índices auxiliares:
     * - calcula max envios por formulario (por laboratorio)
     * - arma el orden de campos por formulario (array de ids)
     * - indexa respuestas de cada resultado para acceso rápido
     * - indexa labs (lista única de laboratorios)
     */
    protected function prepareMeta()
    {
        $this->maxEnviosPorForm = [];
        $this->ordenCamposPorForm = [];
        $this->respuestasIndex = [];
        $this->labsIndex = [];

        foreach ($this->formularios as $form) {
            // 1) Construir orden de campos tal como en headings:
            $camposOrder = [];
            foreach ($form->secciones as $seccion) {
                foreach ($seccion->parametros as $parametro) {
                    foreach ($parametro->campos as $campo) {
                        $camposOrder[] = [
                            'parametro_id' => $parametro->id,
                            'campo_id' => $campo->id,
                        ];
                        // si campo->unidad genera otra columna, el campo ya está representado
                        // y la "unidad" normalmente aparece como otro campo en $parametro->campos.
                        // Si tu modelo marca unidad con una propiedad, ajusta aquí.
                    }
                }
            }
            $this->ordenCamposPorForm[$form->id] = $camposOrder;

            // 2) Por cada resultado del formulario indexar las respuestas y contar envíos por laboratorio
            $countsByLab = [];
            foreach ($form->resultados as $resultado) {
                // asegurar que relaciones respuestas estén cargadas
                if (!isset($resultado->respuestas)) {
                    $resultado = FormularioEnsayoResultado::with('respuestas')->find($resultado->id) ?? $resultado;
                }
                // indexar respuestas por parametro
                $paramMap = [];
                foreach ($resultado->respuestas as $resp) {
                    // resp->respuestas es un array JSON con elementos {id, valor, ...}
                    $paramMap[$resp->id_parametro] = $resp->respuestas ?? [];
                }
                $this->respuestasIndex[$resultado->id] = $paramMap;

                // indexar labs unico y datos administrativos (usamos la última info disponible)
                $labId = $resultado->id_laboratorio;
                $this->labsIndex[$labId] = [
                    'gestion' => $resultado->gestion,
                    'id_ciclo' => $resultado->id_ciclo,
                    'nombre_lab' => $resultado->nombre_lab,
                    'departamento' => $resultado->departamento,
                    'cod_lab' => $resultado->cod_lab,
                    'fecha_envio' => $resultado->fecha_envio,
                ];

                if (isset($this->labsIndex[$labId])) {
                    $fechaNueva = $resultado->fecha_envio ? Carbon::parse($resultado->fecha_envio) : null;
                    $fechaGuardada = $this->labsIndex[$labId]['fecha_envio']
                        ? Carbon::parse($this->labsIndex[$labId]['fecha_envio'])
                        : null;

                    if ($fechaNueva && (!$fechaGuardada || $fechaNueva->gt($fechaGuardada))) {
                        $this->labsIndex[$labId]['fecha_envio'] = $fechaNueva->toDateTimeString();
                    }
                } else {
                    $this->labsIndex[$labId] = [
                        'fecha_envio' => $resultado->fecha_envio,
                    ];
                }
                // contadores por laboratorio
                if (!isset($countsByLab[$labId])) $countsByLab[$labId] = 0;
                $countsByLab[$labId]++;
            }

            $this->maxEnviosPorForm[$form->id] = $countsByLab ? max($countsByLab) : 1;
        }
    }

    /**
     * Construye la colección de filas: una fila por laboratorio, respetando
     * la estructura y los N envíos por formulario (rellena con null cuando falte).
     */
    public function collection()
    {
        $rows = [];
        if ($this->sinDato) {
            return collect($rows);
        }
        // Lista única de labs (ids)
        $labIds = array_keys($this->labsIndex);
        sort($labIds);

        foreach ($labIds as $labId) {
            $labMeta = $this->labsIndex[$labId];
            $row = [];

            // 6 columnas administrativas (una sola vez)
            $row[] = $labMeta['gestion'] ?? null;
            $row[] = $labMeta['id_ciclo'] ?? null;
            $row[] = $labMeta['nombre_lab'] ?? null;
            $row[] = $labMeta['departamento'] ?? null;
            $row[] = $labMeta['cod_lab'] ?? null;
            $row[] = formatDate($labMeta['fecha_envio']) ?? null;

            // PARA CADA FORMULARIO: traer los envíos de ese laboratorio, ordenarlos por created_at
            foreach ($this->formularios as $form) {
                // obtener todos los resultados de ese formulario para este laboratorio
                $resPorLab = $form->resultados->filter(fn($r) => $r->id_laboratorio == $labId)
                    ->sortBy('created_at')
                    ->values();

                $maxEnvios = max(1, $this->maxEnviosPorForm[$form->id] ?? 1);

                for ($envIndex = 0; $envIndex < $maxEnvios; $envIndex++) {
                    if (isset($resPorLab[$envIndex])) {
                        $resultado = $resPorLab[$envIndex];
                        // rellenar los valores en el mismo orden que ordenCamposPorForm
                        foreach ($this->ordenCamposPorForm[$form->id] as $campoDef) {
                            $valor = $this->getValorParaCampo($resultado->id, $campoDef['parametro_id'], $campoDef['campo_id']);
                            $row[] = $valor;
                        }
                    } else {
                        // no existe ese envío para este lab -> agregar nulos por cada campo del formulario
                        $nCampos = count($this->ordenCamposPorForm[$form->id]);
                        for ($k = 0; $k < $nCampos; $k++) $row[] = null;
                    }
                }
            }

            $rows[] = $row;
        }

        return collect($rows);
    }

    /**
     * Busca en $this->respuestasIndex el valor para un determinado resultado, parametro y campo.
     * Devuelve el valor (string) o null si no existe.
     */
    protected function getValorParaCampo($resultadoId, $parametroId, $campoId)
    {
        $paramMap = $this->respuestasIndex[$resultadoId] ?? [];
        $arr = $paramMap[$parametroId] ?? [];
        if (!is_array($arr)) return null;
        foreach ($arr as $item) {
            // item puede tener keys 'id','valor' (según tu ejemplo)
            if ((string)($item['id'] ?? '') === (string)$campoId  && (string)($item['tipo'] ?? '') == 'select') {
                return $item['valorTexto'] ?? $item['valor'];
            }
            if ((string)($item['id'] ?? '') === (string)$campoId) {
                return $item['valor'] ?? null;
            }
        }
        return null;
    }

    /**
     * Construye los encabezados multi-fila. Para cada formulario repetimos
     * sus columnas tantas veces como maxEnviosPorForm[form_id].
     */
    public function headings(): array
    {
        $topRow = [];   // Nombre del bloque (DATOS LAB + cada formulario con sufijo de envío)
        $row1 = [];     // Sección
        $row2 = [];     // Parámetro
        $row3 = [];     // Campo
        $this->formRanges = [];

        // 1) Columnas administrativas (una sola vez)
        $adminHeaders = ['GESTIÓN', 'CICLO', 'LABORATORIO', 'DEPARTAMENTO', 'CÓDIGO', 'FECHA DE ENVIO'];
        foreach ($adminHeaders as $h) {
            $topRow[] = 'DATOS LABORATORIO';
            $row1[] = ' ';
            $row2[] = 'Laboratorio';
            $row3[] = $h;
        }
        $adminStart = 1;
        $adminEnd = count($row1);
        $this->formRanges[] = [
            'form_nombre' => 'DATOS LABORATORIO',
            'start_col' => Coordinate::stringFromColumnIndex($adminStart),
            'end_col' => Coordinate::stringFromColumnIndex($adminEnd),
            'color' => '4F81BD', // color fallback
        ];

        // 2) Para cada formulario: repetir su bloque N veces (N = max envios)
        foreach ($this->formularios as $form) {
            $maxEnv = ($this->sinDato) ? 1 : max(1, $this->maxEnviosPorForm[$form->id] ?? 1);

            // generar columnas por cada envío
            for ($env = 1; $env <= $maxEnv; $env++) {
                $startIndex = count($row1) + 1;
                // para topRow ponemos nombre + "(envío n)" para distinguir bloques
                $labelTop = $form->nombre . " (envío {$env})";

                // recorrer secciones/parametros/campos en el mismo orden que ordenCamposPorForm
                // pero necesitamos reconstruir los nombres de sección/parametro/campo:
                foreach ($form->secciones as $seccion) {
                    foreach ($seccion->parametros as $parametro) {
                        foreach ($parametro->campos as $campo) {
                            $topRow[] = $labelTop;
                            $row1[]   = $seccion->nombre;
                            $row2[]   = $parametro->nombre;
                            // usar label de campo si existe, o id
                            //$row3[]   = $campo->nombre ?? ("Campo {$campo->id}");
                            $row3[]   = $campo->label ?? ("Campo {$campo->id}");
                        }
                    }
                }
                // $topRow[] = $labelTop;
                // $row1[]   = 'Observacion';
                // $row2[]   = 'Observacion';
                // $row3[]   = 'Observacion';

                $endIndex = count($row1);
                $this->formRanges[] = [
                    'form_nombre' => $form->nombre,
                    'start_col' => Coordinate::stringFromColumnIndex($startIndex),
                    'end_col'   => Coordinate::stringFromColumnIndex($endIndex),
                    'color'     => $this->normalizeHexColor($form->color_primario ?? null) ?? '4F81BD',
                ];
            }
        }

        return [$topRow, $row1, $row2, $row3];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $delegate = $sheet->getDelegate();

                $headings = $this->headings();
                $headerRowsCount = count($headings);
                $lastColumnIndex = count($headings[0]);
                $lastColumn = Coordinate::stringFromColumnIndex($lastColumnIndex);

                // formato base (sin fill global)
                $headerRange = "A1:{$lastColumn}{$headerRowsCount}";
                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ]
                    ]
                ]);

                // mergear fila superior por formRanges
                foreach ($this->formRanges as $range) {
                    $start = $range['start_col'] . '1';
                    $end = $range['end_col'] . '1';
                    if ($range['start_col'] !== $range['end_col']) {
                        $sheet->mergeCells("{$start}:{$end}");
                    }
                }

                // mergear filas 2..headerRowsCount por valores iguales (como antes)
                for ($rowIndex = 2; $rowIndex <= $headerRowsCount; $rowIndex++) {
                    $rowValues = $delegate->rangeToArray("A{$rowIndex}:{$lastColumn}{$rowIndex}")[0];
                    $start = 0;
                    $prev = $rowValues[0] ?? null;
                    $n = count($rowValues);
                    for ($i = 1; $i <= $n; $i++) {
                        if ($i == $n || ($rowValues[$i] ?? null) !== $prev) {
                            if ($i - 1 > $start) {
                                $startCol = Coordinate::stringFromColumnIndex($start + 1);
                                $endCol = Coordinate::stringFromColumnIndex($i);
                                $sheet->mergeCells("{$startCol}{$rowIndex}:{$endCol}{$rowIndex}");
                            }
                            if ($i < $n) {
                                $prev = $rowValues[$i];
                                $start = $i;
                            }
                        }
                    }
                }

                // aplicar color por bloque (desde formRanges). Aplicamos a todo el bloque de header rows.
                foreach ($this->formRanges as $range) {
                    $hex = $this->normalizeHexColor($range['color'] ?? null) ?? '4F81BD';
                    $fontColor = $this->getContrastColor($hex);
                    $startCol = $range['start_col'];
                    $endCol = $range['end_col'];
                    $rangeStr = "{$startCol}1:{$endCol}{$headerRowsCount}";
                    $sheet->getStyle($rangeStr)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'color' => ['rgb' => $hex],
                        ],
                        'font' => [
                            'color' => ['rgb' => $fontColor],
                            'bold' => true,
                        ],
                    ]);
                }

                // ajustar alto de filas header y congelar filas debajo del header
                for ($r = 1; $r <= $headerRowsCount; $r++) {
                    $delegate->getRowDimension($r)->setRowHeight(25);
                }
                // Freeze por debajo del header
                $delegate->freezePane('A' . ($headerRowsCount + 1));
            }
        ];
    }

    protected function normalizeHexColor($color)
    {
        if (empty($color)) return null;
        $c = trim($color);
        if (substr($c, 0, 1) === '#') $c = substr($c, 1);
        if (strlen($c) === 3) {
            $c = $c[0] . $c[0] . $c[1] . $c[1] . $c[2] . $c[2];
        }
        $c = strtoupper($c);
        if (preg_match('/^[A-F0-9]{6}$/', $c)) return $c;
        return null;
    }

    protected function getContrastColor($hex)
    {
        $hex = $this->normalizeHexColor($hex) ?? '4F81BD';
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        $luminance = ($r * 299 + $g * 587 + $b * 114) / 1000;
        return ($luminance < 128) ? 'FFFFFF' : '000000';
    }
}
