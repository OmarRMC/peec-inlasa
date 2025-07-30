<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Inscripcion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
            'fechaLimitePago'=>$fechaLimitePago,
            'programas' => $programasAgrupados,
            'total' => $inscripcion->costo_total,
            'fecha_inscripcion' => $fechaCarbon->format('d/m/Y'),
            'fecha_generacion' => now()->format('d/m/Y | H:i'),
            'generado_por' => Auth::user()->username ?? 'Sistema'
        ];

        $pdf = Pdf::loadView('pdf.inscripcion_paquete_lab', $data);
        $pdf->setPaper([0, 0, 612, 936], 'portrait');
        $pdf->getDomPDF()->set_option("isHtml5ParserEnabled", true);
        $pdf->getDomPDF()->getCanvas()->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
            $text = "Página $pageNumber / $pageCount";
            $font = $fontMetrics->getFont('DejaVu Sans', 'normal');
            $canvas->text(40, 700, $text, $font, 9);
        });
        return $pdf->stream('formulario-inscripcion.pdf');
    }

    public function generarContrato($id)
    {
        $inscripcion = Inscripcion::with('laboratorio')->findOrFail($id);
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
            'fechaLimitePago' => $fechaLimitePago,
            'convocatoria' => "Convocatoria del PEEC INLASA Gestión {$gestion}",
            'contrato_numero' => "MSyD/INLASA/PEEC/{$numero}/{$gestion}",
            'departamento' => $laboratorio->departamento->nombre_dep
        ];

        $pdf = Pdf::loadView('pdf.contrato_inscripcion_lab', $data);
        $pdf->setPaper('A4', 'portrait');

        $domPdf = $pdf->getDomPDF();
        $domPdf->render();

        // Acceder correctamente a font y canvas
        $fontMetrics = $domPdf->getFontMetrics();
        $canvas = $domPdf->getCanvas();
        $font = $fontMetrics->getFont('DejaVu Sans', 'normal');

        // Establecer numeración después del render
        $canvas->page_script(function ($pageNumber, $pageCount, $canvas) use ($font, $fontMetrics) {
            $text = "Página $pageNumber / $pageCount";
            $size = 6;
            $width = $fontMetrics->getTextWidth($text, $font, $size);
            $x = 550 - $width;
            $y = 810;
            $canvas->text($x, $y, $text, $font, $size);
        });

        return $domPdf->stream('contrato-peec.pdf');
    }
}
