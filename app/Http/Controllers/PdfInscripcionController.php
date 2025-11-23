<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use App\Models\Contrato;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Inscripcion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PdfInscripcionController extends Controller
{
    public function generar($id)
    {
        $inscripcion = Inscripcion::with([
            'laboratorio.departamento',
            'laboratorio.provincia',
            'laboratorio.municipio',
            'laboratorio.nivel',
            'laboratorio.tipo',
            'detalleInscripciones.paquete.area.programa'
        ])->findOrFail($id);

        $laboratorio = $inscripcion->laboratorio;

        $programasAgrupados = [];

        foreach ($inscripcion->detalleInscripciones as $detalle) {
            $programa = $detalle->paquete->area->programa->descripcion;
            $area = $detalle->paquete->area->descripcion;
            $prueba = $detalle->descripcion_paquete;
            $costo = $detalle->costo_paquete;

            $programasAgrupados[$programa]['nombre'] = $programa;
            $programasAgrupados[$programa]['areas'][$area][] = [
                'paquete' => $prueba,
                'costo' => $costo
            ];
        }
        $fechaCarbon = Carbon::createFromFormat('d/m/Y H:i', $inscripcion->fecha_inscripcion);
        $formulario = $inscripcion->formulario;
        $fechaLimitePago = Carbon::parse($inscripcion->fecha_limite_pago)->locale('es')->translatedFormat('d \d\e F \d\e Y');
        $data = [
            'inscripcion' => $inscripcion,
            'laboratorio' => $laboratorio,
            'formulario' => $formulario,
            'fechaLimitePago' => $fechaLimitePago,
            'programas' => $programasAgrupados,
            'total' => $inscripcion->costo_total,
            'fecha_inscripcion' => $fechaCarbon->format('d/m/Y'),
            'fecha_generacion' => now()->format('d/m/Y | H:i'),
            'generado_por' => Auth::user()->username ?? 'Sistema'
        ];

        // return view('pdf.inscripcion_paquete_lab', $data);
        $pdf = Pdf::loadView('pdf.inscripcion_paquete_lab', $data);
        // $pdf->setPaper([0, 0, 612, 936], 'portrait');
        $pdf->setPaper('A4', 'portrait');
        $pdf->getDomPDF()->set_option("isHtml5ParserEnabled", true);
        $pdf->render();
        $pdf->getDomPDF()->getCanvas()->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
            $text = "Página $pageNumber / $pageCount";
            $font = $fontMetrics->getFont('DejaVu Sans', 'normal');
            $canvas->text(535, 810, $text, $font, 6);
        });
        return $pdf->stream('formulario-inscripcion.pdf');
    }

    public function generarContrato($id)
    {
        $inscripcion = Inscripcion::with('laboratorio')->findOrFail($id);
        $contrato = $inscripcion->contrato;
        if ($contrato) {
            $contrato->load('detallesActivos');
        } else {
            $contrato = Contrato::with(['detalles' => function ($q) {
                $q->where('estado', 1);
            }])->Activo()->orderBy('created_at', 'desc')->first();
        }

        $laboratorio = $inscripcion->laboratorio;
        $fechaContrato = Carbon::now()->locale('es')->translatedFormat('d \d\e F \d\e Y');
        $gestion = $inscripcion->gestion;
        $fechaLimitePago = Carbon::parse($inscripcion->fecha_limite_pago)->locale('es')->translatedFormat('d \d\e F \d\e Y');
        // $fechaLimitePago = '232';
        $codLab = $laboratorio->cod_lab;

        preg_match('/\d+/', $codLab, $coincidencias);

        $numero = ltrim($coincidencias[0], '0');
        $numero = $numero === '' ? '0' : $numero;

        $data = [
            'laboratorio' => $laboratorio,
            'generado_por' => Auth::user()->username ?? 'Sistema',
            'fecha_generacion' => now()->format('d/m/Y | H:i'),
            'fecha_contrato' => $fechaContrato,
            'gestion' => $gestion,
            'gestionInscripcion' => $gestion,
            'contrato' => $contrato,
            'fechaLimitePago' => $fechaLimitePago,
            'convocatoria' => "Convocatoria del PEEC INLASA Gestión {$gestion}",
            'contrato_numero' => "MSyD/INLASA/PEEC/{$numero}/{$gestion}",
            'departamento_raw' => $laboratorio->departamento->nombre_dep,
            'laboratorioNombreLab' => $laboratorio->nombre_lab,
            'laboratorioZonaLab' => $laboratorio->zona_lab,
            'laboratorioDireccionLab' => $laboratorio->direccion_lab,
            'laboratorioReprelegLab' => $laboratorio->repreleg_lab,
            'laboratorioCiReprelegLab' => $laboratorio->ci_repreleg_lab,
            'departamento' => Str::title(strtolower($laboratorio->departamento->nombre_dep))
        ];

        if ($contrato) {
            foreach ($contrato->detalles as $detalle) {
                $detalle->descripcion = $this->procesarTextoContrato($detalle->descripcion, $data);
                $detalle->titulo = $this->procesarTextoContrato($detalle->titulo, $data);
            }
        }
        // $pdf = Pdf::loadView('pdf.contrato_inscripcion_lab', $data);
        // $pdf->setPaper('A4', 'portrait');

        // $domPdf = $pdf->getDomPDF();
        // $domPdf->render();
        $pdf = Pdf::loadView('pdf.contrato_inscripcion_lab', $data);
        // $pdf->setPaper([0, 0, 612, 936], 'portrait');
        $pdf->setPaper('A4', 'portrait');
        $pdf->getDomPDF()->set_option("isHtml5ParserEnabled", true);
        $pdf->render();

        // Acceder correctamente a font y canvas
        // $fontMetrics = $domPdf->getFontMetrics();
        // $canvas = $domPdf->getCanvas();
        // $font = $fontMetrics->getFont('DejaVu Sans', 'normal');

        // Establecer numeración después del render
        // $canvas->page_script(function ($pageNumber, $pageCount, $canvas) use ($font, $fontMetrics) {
        //     $text = "Página $pageNumber / $pageCount";
        //     $size = 6;
        //     $width = $fontMetrics->getTextWidth($text, $font, $size);
        //     $x = 550 - $width;
        //     $y = 810;
        //     $canvas->text($x, $y, $text, $font, $size);
        // });
        $pdf->getDomPDF()->getCanvas()->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
            $text = "Página $pageNumber / $pageCount";
            $font = $fontMetrics->getFont('DejaVu Sans', 'normal');
            $canvas->text(535, 810, $text, $font, 6);
        });
        return $pdf->stream('contrato-peec.pdf');
        // return $domPdf->stream('contrato-peec.pdf');
    }
    private function procesarTextoContrato($texto, $data)
    {
        $variables = [];
        foreach ($data as $key => $value) {
            if (is_array($value) || is_object($value)) {
                continue;
            }

            $variables["{{ $key }}"] = $value;
        }
        return str_replace(array_keys($variables), array_values($variables), $texto);
    }
}
