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
            border: 1.5px solid #aa9900;
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
            /* left: 50%;
            transform: translateX(-50%); */
            width: 45mm;
            height: 45mm;
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
            vertical-align: middle;
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

        .sig .instituto {
            font-size: 8pt;
            line-height: 1.25;
        }

        .firma {
            position: relative;
            height: 50px;
            text-align: center;
            vertical-align: middle;
        }

        /* Imágenes de firmas (opcionales) arriba de la línea) */
        .firma img.signature {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
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
            font-family: Arial, Helvetica, sans-serif;
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

        .qr svg {
            margin: 0px;
            width: 100%;
            top: 0px;
        }
    </style>
</head>

<body>
    <div class="page">

        <!-- Cinta superior izquierda -->
        {{-- <div class="ribbon">
                <div class="gold"></div>
                <div class="navy"></div>
            </div> --}}

        <!-- Encabezado -->
        <div class="header">
            <div class="logos">
                <img src="{{ public_path('img/logoinlasa.jpg') }}" alt="Bicentenario de Bolivia">
                <img src="{{ public_path('img/logoinlasa.jpg') }}" alt="Bolivia">
                <img src="{{ public_path('img/logoinlasa.jpg') }}" alt="Ministerio de Salud y Deportes">
            </div>
        </div>

        <!-- Títulos -->
        <div>
            <div style="text-align: center">
                <img class="seal" src="{{ public_path('img/logoinlasa.jpg') }}" alt="Sello INLASA">
            </div>
            <div class="title" style="text-align:center;">CERTIFICADO</div>
            <div class="subtitle" style="text-align:center;">DE PARTICIPACIÓN A</div>
            <div class="labname" style="text-align:center;">
                {{ mb_strtoupper($certificado->nombre_laboratorio) }}
            </div>
        </div>

        <!-- Cuerpo -->
        <div class="content">
            <p class="center">
                Por su participación en el Programa de Evaluación Externa de la Calidad del
                Instituto Nacional de Laboratorios de Salud - PEEC INLASA, en
            </p>
            <div class="area">{{ mb_strtoupper($ensayosA) }}</div>
        </div>

        <!-- Firmas -->
        <table class="signatures">
            <tr>
                <td class='firma'>
                    @if (!empty($certificado->firma_jefe))
                        <img class="signature" src="{{ public_path($certificado->firma_jefe) }}" alt="Firma Jefe">
                    @endif
                </td>
                <td class='firma'>
                    @if (!empty($certificado->firma_coordinador))
                        <img class="signature" src="{{ public_path($certificado->firma_coordinador) }}"
                            alt="Firma Coordinadora">
                    @endif
                </td>
                <td class='firma'>
                    @if (!empty($certificado->firma_director))
                        <img class="signature" src="{{ public_path($certificado->firma_director) }}"
                            alt="Firma Directora">
                    @endif
                </td>
            </tr>
            <tr>
                <td class='sig'>
                    <div class="name">{{ $certificado->nombre_jefe }}</div>
                </td>
                <td class='sig'>
                    <div class="name">{{ $certificado->nombre_director }}</div>
                </td>
                <td class='sig'>
                    <div class="name">{{ $certificado->nombre_director }}</div>
                </td>
            </tr>
            <tr>
                <td class='sig'>
                    <div class="role">JEFE PROGRAMA DE EVALUACIÓN<br>EXTERNA DE LA CALIDAD</div>
                </td>
                <td class='sig'>
                    <div class="role">COORDINADORA DIVISIÓN RED DE<br>LABORATORIOS DE SALUD PÚBLICA</div>
                </td>
                <td class='sig'>
                    <div class="role">DIRECTORA GENERAL EJECUTIVA</div>
                </td>
            </tr>
            <tr>
                <td class='sig'>
                    <div class="instituto">INLASA</div>
                </td>
                <td class='sig'>
                    <div class="instituto">INLASA</div>
                </td>
                <td class='sig'>
                    <div class="instituto">INLASA</div>
                </td>
            </tr>
        </table>

        <div class="gestion">Gestión {{ $certificado->gestion_certificado }}</div>

        <div class="footer">
            El PEEC INLASA, extiende el presente Certificado únicamente a los Laboratorios Clínicos que
            cumplieron con el porcentaje de participación y/o desempeño, según procedimientos internos;
            respaldados por los informes de Evaluación emitidos durante la gestión
            {{ $certificado->gestion_certificado }}.
        </div>

        <!-- QR -->
        <div class="qr">
            <img src="data:image/png;base64,{{ $qr }}" alt="QR">
        </div>
    </div>
</body>

</html>
