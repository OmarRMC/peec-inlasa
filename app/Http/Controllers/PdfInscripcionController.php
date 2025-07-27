<?php

namespace App\Http\Controllers;

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

        $data = [
            'inscripcion' => $inscripcion,
            'laboratorio' => $laboratorio,
            'programas' => $programasAgrupados,
            'total' => $inscripcion->costo_total,
            'fecha_inscripcion' =>$fechaCarbon->format('d/m/Y'),
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
}
