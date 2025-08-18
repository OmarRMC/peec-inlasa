@php
    $fontRegular = storage_path('fonts/TimesNewRoman.ttf');
    $fontBold = storage_path('fonts/TimesNewRoman-Bold.ttf');
@endphp
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Certificado</title>
    <style>
        @page {
            margin: 5mm 5mm 5mm 5mm;
        }

        html,
        body {
            height: 100%;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            color: #000;
        }

        .page {
            position: relative;
            height: 275mm;
            /* deja espacio al borde superior @page */
            border: 1px solid #CFCFCF;
            padding: 5mm 5mm 5mm 5mm;
        }

        /* Cinta superior izquierda (dos franjas como en la imagen) */
        .ribbon {
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 0;
        }

        .ribbon .gold {
            position: absolute;
            top: -18mm;
            left: -18mm;
            width: 120mm;
            height: 18mm;
            background: #d1a229;
            transform: rotate(-30deg);
        }

        .ribbon .navy {
            position: absolute;
            top: -8mm;
            left: -14mm;
            width: 90mm;
            height: 10mm;
            background: #0a2740;
            transform: rotate(-30deg);
        }

        /* Encabezado: sello centro, logos derecha */
        .header {
            position: relative;
            margin-top: 8mm;
            height: 22mm;
        }

        .seal {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 22mm;
            height: 22mm;
            object-fit: contain;
        }

        .logos {
            position: absolute;
            right: 0;
            top: 0;
            display: flex;
            gap: 6mm;
            align-items: center;
        }

        .logos img {
            height: 16mm;
            object-fit: contain;
        }

        /* Títulos principales */
        .title {
            margin-top: 10mm;
            font-size: 34pt;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .subtitle {
            font-size: 16pt;
            margin-top: 2mm;
        }

        .labname {
            margin-top: 4mm;
            font-size: 18pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* Cuerpo */
        .content {
            margin: 10mm 8mm 0 8mm;
            line-height: 1.5;
            font-size: 12pt;
            text-align: justify;
        }

        .content .center {
            text-align: center;
        }

        .area {
            margin-top: 5mm;
            font-size: 16pt;
            font-weight: bold;
            text-align: center;
        }

        .resultado {
            margin-top: 2mm;
            font-size: 12.5pt;
            text-align: center;
        }

        .resultado b {
            font-weight: bold;
        }

        /* Firmas */
        .signatures {
            display: table;
            width: 100%;
            margin-top: 18mm;
            table-layout: fixed;
            font-size: 8px;
        }

        .sig {
            display: table-cell;
            text-align: center;
            vertical-align: bottom;
            padding: 0 4mm;
        }

        .sig .line {
            margin-top: 18mm;
            border-top: 1px solid #000;
            width: 100%;
        }

        .sig .name {
            margin-top: 2mm;
            font-weight: bold;
            font-size: 8pt;
        }

        .sig .role {
            margin-top: 1mm;
            font-size: 8pt;
            line-height: 1.25;
        }

        /* Imágenes de firmas (opcionales) arriba de la línea) */
        .sig img.signature {
            height: 18mm;
            margin-bottom: 2mm;
            object-fit: contain;
        }

        /* Gestión */
        .gestion {
            margin-top: 10mm;
            text-align: center;
            font-size: 11.5pt;
        }

        /* Pie y QR */
        .footer {
            position: absolute;
            left: 14mm;
            right: 40mm;
            bottom: 12mm;
            font-size: 8.5pt;
            line-height: 1.35;
            text-align: justify;
            width: 60%;
        }

        .qr {
            position: absolute;
            right: 12mm;
            bottom: 10mm;
            width: 28mm;
            height: 28mm;
            border: 1px solid #000;
            padding: 1mm;
        }

        .qr img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }
    </style>
</head>

@foreach ($data as $area => $detalles)
    @php
        $cert = $detalles['certificado'];

        // Texto que deseas codificar en el QR (URL de verificación, hash, etc.)
        // Si el controlador te pasa $cert->qr_text, úsalo; si no, genera uno.
        $qrText = $cert->qr_text ?? $cert->nombre_laboratorio . '|' . $cert->gestion_certificado . '|' . $area;
        // Requiere "simplesoftwareio/simple-qrcode"
        // $qrPng = base64_encode(QrCode::format('png')->size(460)->margin(0)->generate($qrText));
    @endphp

    <body style="page-break-after: always;">
        <div class="page">

            <!-- Cinta superior izquierda -->
            {{-- <div class="ribbon">
                <div class="gold"></div>
                <div class="navy"></div>
            </div> --}}

            <!-- Encabezado -->
            <div class="header">
                {{-- Sello (escudo redondo INLASA) --}}
                <img class="seal" src="{{ public_path('img/logoinlasa.jpg') }}" alt="Sello INLASA">
                {{-- Logos (Bicentenario, Bolivia, Ministerio) --}}
                <div class="logos">
                    <img src="{{ public_path('img/logoinlasa.jpg') }}" alt="Bicentenario de Bolivia">
                    <img src="{{ public_path('img/logoinlasa.jpg') }}" alt="Bolivia">
                    <img src="{{ public_path('img/logoinlasa.jpg') }}" alt="Ministerio de Salud y Deportes">
                </div>
            </div>

            <!-- Títulos -->
            <div class="title" style="text-align:center;">CERTIFICADO</div>
            <div class="subtitle" style="text-align:center;">DE DESEMPEÑO A</div>
            <div class="labname" style="text-align:center;">
                {{ mb_strtoupper($cert->nombre_laboratorio) }}
            </div>

            <!-- Cuerpo -->
            <div class="content">
                <p class="center">
                    Por su desempeño en el Programa de Evaluación Externa de la Calidad del Instituto Nacional de
                    Laboratorios de Salud - PEEC INLASA, en el área de:
                </p>

                <div class="area">{{ mb_strtoupper($area) }}</div>

                @foreach ($detalles['detalles'] as $detalle)
                    <div class="resultado">
                        {{ mb_strtoupper($detalle['ensayo']) }} : <b>{{ mb_strtoupper($detalle['ponderacion']) }}</b>
                    </div>
                @endforeach
            </div>

            <!-- Firmas -->
            <div class="signatures">
                <div class="sig">
                    @if (!empty($cert->firma_jefe))
                        <img class="signature" src="{{ public_path($cert->firma_jefe) }}" alt="Firma Jefe">
                    @endif
                    <div class="line"></div>
                    <div class="name">{{ $cert->nombre_jefe }}</div>
                    <div class="role">JEFE PROGRAMA DE EVALUACIÓN<br>EXTERNA DE LA CALIDAD<br>INLASA</div>
                </div>
                <div class="sig">
                    @if (!empty($cert->firma_coordinador))
                        <img class="signature" src="{{ public_path($cert->firma_coordinador) }}"
                            alt="Firma Coordinadora">
                    @endif
                    <div class="line"></div>
                    <div class="name">{{ $cert->nombre_coordinador }}</div>
                    <div class="role">COORDINADORA DIVISIÓN RED DE<br>LABORATORIOS DE SALUD PÚBLICA<br>INLASA</div>
                </div>
                <div class="sig">
                    @if (!empty($cert->firma_director))
                        <img class="signature" src="{{ public_path($cert->firma_director) }}" alt="Firma Directora">
                    @endif
                    <div class="line"></div>
                    <div class="name">{{ $cert->nombre_director }}</div>
                    <div class="role">DIRECTORA GENERAL EJECUTIVA<br>INLASA</div>
                </div>
            </div>

            <div class="gestion">Gestión {{ $cert->gestion_certificado }}</div>

            <!-- Pie -->
            <div class="footer">
                El PEEC INLASA, extiende el presente Certificado únicamente a los Laboratorios Clínicos que cumplieron
                con
                el porcentaje de participación y/o desempeño exigido, según procedimientos internos; respaldados por los
                Informes de Evaluación emitidos durante la gestión {{ $cert->gestion_certificado }}.
            </div>

            <!-- QR -->
            <div class="qr">
                {{-- <img src="data:image/png;base64,{{ $qrPng }}" alt="QR"> --}}
            </div>
        </div>
    </body>
@endforeach

</html>
