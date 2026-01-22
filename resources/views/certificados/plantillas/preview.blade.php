<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0mm; }
        body { margin: 0; font-family: DejaVu Sans, sans-serif; }

        .page {
            width: {{ $plantilla->ancho_mm }}mm;
            height: {{ $plantilla->alto_mm }}mm;
            position: relative;
            overflow: hidden;

            @if(!empty($background))
            background-image: url('{{ $background }}');
            background-repeat: no-repeat;
            background-size: 100% 100%;
            @endif
        }

        .center {
            text-align: center;
        }

        .upper {
            text-transform: uppercase;
        }

        /* ======= Tipografías aproximadas a tu imagen ======= */
        .h1 {
            /* font-size: 16pt; */
            font-weight: 800;
            letter-spacing: .2pt;
        }

        .h2 {
            /* font-size: 15pt; */
            font-weight: 800;
            letter-spacing: .2pt;
        }

        .h3 {
            /* font-size: 14pt; */
            font-weight: 800;
            letter-spacing: .2pt;
        }

        .confiere {
            font-size: 15.5pt;
            font-weight: 400;
            color: #6b7280;
        }

        /* gris */
        .cert-title {
            font-family: 'Agrandir';
            font-size: 18pt;
            font-weight: 900;
            color: #6b7280;
        }

        /* gris fuerte */
        .label-a {
            font-size: 22pt;
            font-weight: 900;
        }

        .lab {
            font-size: 18pt;
            font-weight: 500;
            letter-spacing: .4pt;
        }

        .body-bold {
            font-size: 13.5pt;
            font-weight: 800;
            line-height: 1.25;
        }

        .area {
            font-size: 18pt;
            font-weight: 900;
            color: #b3841a;
            letter-spacing: .3pt;
        }

        .items {
            font-size: 9.5pt;
            font-weight: 800;
            line-height: 1.25;
            letter-spacing: .2pt;
        }

        /* ======= Posicionamiento (mm) calibrado a A4 horizontal ======= */
        .block-header {
            /* position: absolute; */
            position: relative;
            font-size: 14pt;
            top: 28mm;
            left: 0;
            right: 0;
        }

        .block-title {
            /* position: absolute; */
            position: relative;
            top: 30mm;
            left: 0;
            right: 0;
        }

        .block-a {
            position: absolute;
            top: 65mm;
            left: 25mm;
            right: 18mm;
        }

        .block-paragraph {
            position: absolute;
            top: 85mm;
            left: 28mm;
            right: 28mm;
        }

        .block-area {
            position: absolute;
            top: 105mm;
            left: 0;
            right: 0;
        }

        .block-items {
            position: absolute;
            top: 115mm;
            left: 0;
            right: 0;
        }

        /* Firmas (3 columnas) */
        .signatures {
            width: 100%;
            position: absolute;
            /* deja espacio QR */
            bottom: 25mm;
            font-family: Arial, Helvetica, sans-serif;
            text-align: center;
        }

        .sig-name {
            font-size: 8pt;
            font-style: italic;
            font-weight: 400;
        }

        .sig-role {
            font-size: 9pt;
            font-weight: 900;
            /* padding: 0mm 20mm; */
            font-style: italic;

        }

        .sig-org {
            font-size: 9pt;
            font-weight: 900;
        }

        /* QR */
        .qr {
            position: absolute;
            right: 6mm;
            bottom: 6mm;
            width: 20mm;
            height: 20mm;
        }

        .qr img {
            width: 100%;
            height: 100%;
        }

        /* Disclaim + gestión */
        .disclaimer {
            position: absolute;
            left: 45mm;
            bottom: 14mm;
            width: 70mm;
            font-size: 5pt;
            color: #111;
        }

        .gestion {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 18mm;
            text-align: center;
            font-size: 9pt;
            font-weight: 700;
        }
    </style>
</head>

<body>
    <div class="page">

        <div class="block-header center upper">
            <div class="h1">MINISTERIO DE SALUD Y DEPORTES</div>
            <div class="h2">INSTITUTO NACIONAL DE LABORATORIOS DE SALUD</div>
            <div class="h3">“DR. NÉSTOR MORALES VILLAZÓN”</div>
        </div>

        <div class="block-title center">
            <div class="confiere">Confiere el presente:</div>
            <div class="cert-title upper">CERTIFICADO DE PARTICIPACIÓN</div>
        </div>

        <div class="block-a">
            <table width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="width: 5mm; vertical-align: top;">
                        <div class="label-a upper">A:</div>
                    </td>
                    <td style="vertical-align: top;">
                        <div class="center upper lab">{{ $sample['laboratorio_linea_1'] }}</div>
                        <div class="center upper lab" style="margin-top: 2mm;">{{ $sample['laboratorio_linea_2'] }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="block-paragraph center body-bold">
            Por su desempeño en el Programa de Evaluación Externa de la Calidad<br>
            del Instituto Nacional de Laboratorios de Salud - PEEC INLASA en el<br>
            área de:
        </div>

        <div class="block-area center upper">
            <div class="area">{{ $sample['area'] }}</div>
        </div>

        <div class="block-items center upper items">
            @foreach(($sample['items'] ?? []) as $line)
            <div>{{ $line }}</div>
            @endforeach
        </div>

        <div class="signatures">
            @php
            $sig = collect($firmas ?? [])
            ->filter(fn($f) => !empty($f['nombre']) || !empty($f['cargo']) || !empty($f['firma_data_uri']) || !empty($f['firma']))
            ->values();
            $count = max($sig->count(), 1);
            $colWidth = 50 / $count;
            @endphp
            <table width="100%" cellspacing="0" cellpadding="0" style="padding: 0mm 30mm;">
                <tr>
                    @if($sig->isEmpty())
                    {{-- Placeholder opcional (si NO quieres placeholder, elimina este bloque completo) --}}
                    <td align="center" valign="bottom" style="width: 100%;">
                        <div class="sig-name">FIRMANTE</div>
                        <div class="sig-role upper">CARGO</div>
                        <div class="sig-org upper">INLASA</div>
                    </td>
                    @else
                    @foreach($sig as $f)
                    <td align="center" valign="bottom" style="width:{{ $colWidth }}% !important;; background: red;">
                        @if(!empty($f['firma_data_uri']))
                        <img src="{{ $f['firma_data_uri'] }}" style="height: 14mm; width:auto;" alt="Firma">
                        @else
                        <div style="height:14mm;"></div>
                        @endif
                        <div class="sig-name">{{ $f['nombre'] ?? '' }}</div>
                        <div class="sig-role upper">{{ $f['cargo'] ?? '' }}</div>
                        <div class="sig-org upper">INLASA</div>
                    </td>
                    @endforeach
                    @endif
                </tr>
            </table>
        </div>

        <div class="gestion">
            Gestión {{ $sample['gestion'] }}
        </div>

        <div class="disclaimer">
            El PEEC INLASA, extiende el presente Certificado únicamente a los Laboratorios Clínicos que cumplieron
            con el porcentaje de participación y/o desempeño exigido, según procedimientos internos; respaldados
            por los Informes de Evaluación emitidos durante la gestión.
        </div>

        <div class="qr">
            <img src="{{ $qr }}" alt="QR">
        </div>

    </div>
</body>

</html>