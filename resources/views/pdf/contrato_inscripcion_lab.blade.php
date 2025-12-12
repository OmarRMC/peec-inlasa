@php
    use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Contrato de PEEC - INLASA</title>
    <style>
        body {
            font-family: Calibri, 'Trebuchet MS', sans-serif;
            font-size: 11px;
            /* margin: 0px; */
            transform: scaleY(1.05);
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .section {
            margin-top: 10px;
        }

        .clause {
            margin-bottom: 10px;
            text-align: justify;
        }

        .signature-section {
            margin-top: 100px;
        }

        .signature-table {
            width: 100%;
            text-align: center;
            font-size: 11px;
        }

        .footer {
            position: fixed;
            bottom: 0px;
            left: 0;
            right: 0;
            font-size: 9px;
        }

        .strong {
            font-weight: 600;
        }

        @page {
            /* margin: 30px 25px 35px 25px; */
        }

        p {
            margin: 0;
            line-height: 1.1;
        }

        div {
            margin: 0;
        }

        .contenido {
            position: relative;
            padding-left: 45px;
            margin-bottom: 30px;
        }

        .title {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <table style="width: 100%;">
        <tr>
            <td style="width: 15%;">
                <img src="{{ public_path('img/logoinlasa.jpg') }}" style="width: 100px; height: 100px;object-fit: cover"
                    alt="Logo INLASA">
            </td>
            <td style="width: 55%; font-size: 10px; line-height: 12px;">
                <strong style="font-size: 15px;">INLASA</strong><br>
                <strong style="font-size: 13px">Instituto Nacional de Laboratorios de Salud</strong><br>
                <div style="font-size: 10px; line-height: 10px;">
                    "Dr. Néstor Morales Villazón"<br>
                    Dirección: Pasaje Rafael Zubieta N°1889, Miraflores<br>
                    Teléfonos: 2226048 - 2226670 - 2225194<br>
                    Sitio web : www.inlasa.gob.bo
                </div>
            </td>
            <td style="width: 30%;" class="text-right">
                {{-- <strong>CONTRATO</strong><br>
                N° {{ $contrato_numero ?? 'MSyD/INLASA/PEEC/XXXX/2025' }}<br>
                <i>Fecha: {{ $fecha_contrato ?? now()->format('d/m/Y') }}</i> --}}
            </td>
        </tr>
    </table>

    <div class="contenido">
        <p class="text-center bold title" style="font-size: 13px;">
            CONTRATO N° {{ $contrato_numero }}
        </p>

        <div class="clause" style="margin: 0">
            <p>
                El Instituto Nacional de Laboratorios de Salud "Dr Néstor Morales Villazón" INLASA, con Número de
                Identificación Tributaria N° 1001419025 y domicilio establecido en el pasaje Rafael Zubieta N°1889 (al
                Lado del Hospital del Niño) de la zona de Miraflores de la ciudad de La Paz, legalmente representado por
                su Directora General Ejecutiva {{ $contrato->firmante_nombre ?? 'PATRICIA CARLA VACAFLOR TORRICO' }},
                titular de la Cédula de Identidad N.º
                {{ $contrato->identificacion ?? '1881125' }}, en mérito a la Resolución Ministerial N° 0533 de 16 de
                diciembre 2020, quien en adelante y para
                efectos del presente contrato se denominará INLASA.
                </br>
                Por otro lado, el Laboratorio <strong>{{ $laboratorio->nombre_lab }}</strong> participante del Programa
                de
                Evaluación Externa de la Calidad - INLASA {{ $gestion }}, con domicilio establecido en
                <strong>{{ $laboratorio->zona_lab }}, {{ $laboratorio->direccion_lab }}</strong>
                de la ciudad/departamento de <strong>{{ $departamento_raw }}</strong>, legalmente representado
                por <strong>{{ $laboratorio->repreleg_lab }}</strong>, titular de la cedula identidad
                N.º <strong>{{ $laboratorio->ci_repreleg_lab }}</strong>, que para fines de ejecución y seguimiento del
                presente
                contrato se denominara "PARTICIPANTE".
            </p>
        </div>
        @if (!$contrato)
            @include('pdf.partials.clausulas')
        @else
            @forelse ($contrato->detalles as $detalle)
                <div class="section">
                    <strong>{{ $detalle->titulo }}</strong>
                    <p class="clause">
                        {!! nl2br(e($detalle->descripcion)) !!}
                    </p>
                </div>
            @empty
                @include('pdf.partials.clausulas')
            @endforelse
        @endif
        <div class="text-right section">
            {{ $departamento }}, {{ $fecha_contrato ?? '20 de Febrero de 2025' }}
        </div>

        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td>
                        ________________________________________<br>
                        @if ($contrato)
                            {{ mb_strtoupper($contrato->firmante_nombre, 'UTF-8') }}
                        @else
                            DRA. EVELIN ESTHER FORTÚN FERNÁNDEZ
                        @endif
                        <br>
                        <strong>
                            @if ($contrato)
                                {{ mb_strtoupper($contrato->firmante_cargo, 'UTF-8') }}
                            @else
                                DIRECTORA GENERAL EJECUTIVA
                            @endif
                        </strong>
                        <br>
                        <strong>
                            @if ($contrato)
                                {{ mb_strtoupper($contrato->firmante_institucion, 'UTF-8') }}
                            @else
                                INLASA
                            @endif
                        </strong>
                    </td>
                    <td>
                        ________________________________________<br>
                        {{ $laboratorio->repreleg_lab }}<br>
                        <strong> REPRESENTANTE LEGAL/PROPIETARIO </strong><br>
                        <strong>{{ $laboratorio->nombre_lab }}</strong>
                    </td>
                </tr>
            </table>
        </div>

        <div style="margin-top: 50px; font-size: 8px;">
            @if ($contrato)
                {!! nl2br(e($contrato->firmante_detalle)) !!}
            @else
                EEFF/CHC </br>
                C.c. Archivo
            @endif
        </div>
        <div style="position: fixed; bottom: -15px; left: 45px; right: 0; height: 50px; font-size: 9px;">
            <table style="width: 100%;">
                <tr>
                    <td style="text-align: left; width: 33%">
                        <em>Generado por: {{ $generado_por }} el {{ $fecha_generacion }}</em><br>
                        <em>SigPEEC | INLASA</em>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    {{-- 
    <div class="footer">
        <table style="width: 100%;">
            <tr>
                <td style="text-align: left;">
                    <em>Generado por: {{ $generado_por }} el {{ $fecha_generacion }}</em>
                </td>
                <td style="text-align: center;">
                    SigPEEC | INLASA
                </td>
                <td class="text-right">
                    <em>Página {PAGE_NUM} / {PAGE_COUNT}</em>
                </td>
            </tr>
        </table>
    </div> --}}
</body>

</html>
