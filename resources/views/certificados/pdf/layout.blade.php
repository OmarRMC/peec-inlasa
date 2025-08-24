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

        F html,
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
            padding: 4mm 4mm 4mm 4mm;
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
            margin-top: 5mm;
            height: 27mm;
        }

        .seal {
            /* left: 50%;
            transform: translateX(-50%); */
            width: 34mm;
            height: 34mm;
            object-fit: contain;
            margin-bottom: 5mm;
        }

        .logos {
            position: absolute;
            right: 8mm;
            top: 0;
            display: flex;
            align-content: flex-start;
            flex-wrap: wrap;
            gap: 6mm;
        }

        .logos img {
            height: 26mm;
            object-fit: contain;
        }

        .general_title {
            display: flex;
            flex-direction: column;
            gap: 1;
        }

        /* Títulos principales */
        .title {
            font-size: 47pt;
            font-weight: bold;
            letter-spacing: 1px;
            margin: 0px;
        }

        .subtitle {
            font-size: 20pt;
        }

        .labname {
            font-size: 27pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* Cuerpo */
        .content {
            margin: 8mm 10mm 0 10mm;
            line-height: 1.5;
            font-size: 15pt;
            text-align: justify;
        }

        .content .center {
            text-align: center;
            margin: 2mm 0px;
        }

        .area {
            margin-top: 2mm;
            font-size: 16pt;
            font-weight: bold;
            text-align: center;
        }

        .resultado {
            font-size: 12.5pt;
            text-align: center;
            font-weight: bold;
        }

        .resultado b {
            font-weight: bold;
        }

        .evaluaciones {
            min-height: 45mm;
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
            left: 3mm;
            bottom: 2mm;
            font-size: 6pt;
            line-height: 1;
            text-align: justify;
            width: 55%;
            font-family: Calibri, 'Gill Sans', 'Gill Sans MT', 'Trebuchet MS', sans-serif;
        }

        .qr {
            position: absolute;
            right: 1mm;
            bottom: 1mm;
            width: 30mm;
            height: 30mm;
            /* border: 1px solid #000; */
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
@yield('contenido')

</html>
