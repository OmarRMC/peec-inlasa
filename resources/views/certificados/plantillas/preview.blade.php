@php
use App\Models\PlantillaCertificado;
use App\Models\Certificado;
@endphp

<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Certificado de @if( $type == Certificado::TYPE_PARTICIPACION) participacion @else desempeño @endif </title>
    <style>
        @page { margin: 0mm; }
        body {
            margin: 0;
            font-family: 'DejaVu Sans', sans-serif;
        }

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
            /* letter-spacing: .2pt; */
        }

        .h2 {
            /* font-size: 15pt; */
            font-weight: 800;
            /* letter-spacing: .2pt; */
        }

        .h3 {
            /* font-size: 14pt; */
            font-weight: 800;
            /* letter-spacing: .2pt; */
        }

        .confiere {
            font-size: 15.5pt;
            font-weight: 400;
            color: #6b7280;
        }

        /* gris */
        .cert-title {
            font-size: 18pt;
            color: #6b7280;
        }

        /* gris fuerte */
        .label-a {
            font-size: 20pt;
            font-family: 'Open Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-weight: bold;
        }

        .lab {
            font-size: 14pt;
            /* font-weight: 500;
            letter-spacing: .4pt; */
        }

        .body-bold {
            font-size: 13.5pt;
            font-weight: 800;
            line-height: 1.25;
        }

        .area {
            font-size: 18pt;
            color: #b3841a;
            letter-spacing: .3pt;
            font-weight: bold;
            font-family: 'Montserrat', 'Inter', 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
        }

        .items {
            font-size: 7.5pt;
            line-height: 1.25;
            letter-spacing: .2pt;
        }

        .itemsEnsayos{
            width: 50%;
            top: 105mm !important;
            font-size: 10pt;
            line-height: 1.25;
            letter-spacing: .2pt;
            margin: auto;
        }

        /* ======= Posicionamiento (mm) calibrado a A4 horizontal ======= */
        .block-header {
            /* position: absolute; */
            font-family: Helvetica, sans-serif;
            position: relative;
            font-size: 14pt;
            top: 28mm;
            left: 0;
            right: 0;
            font-weight: bold;
        }

        .block-title {
            /* position: absolute; */
            font-family: Verdana, Geneva, Tahoma, sans-serif;
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
            font-family: Verdana, Geneva, Tahoma, sans-serif;
            font-weight: bold;
        }

        .block-area {
            position: absolute;
            top: 105mm;
            left: 0;
            right: 0;
            /* font-family: Verdana, Geneva, Tahoma, sans-serif; */
        }

        .block-items {
            position: absolute;
            top: 115mm;
            left: 0;
            right: 0;
            font-family: Verdana, Geneva, Tahoma, sans-serif;
        }

        /* Firmas (3 columnas) */
        .signatures {
            width: 100%;
            position: absolute;
            /* deja espacio QR */
            bottom: 25mm;
            text-align: center;
            font-family: 'Raleway', 'Inter', 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
        }

        .sig-name {
            font-size: 9pt;
            font-style: italic;
            /* font-weight: 400; */
        }

        .sig-role {
            font-size: 9pt;
            font-weight: bold;
            /* padding: 0mm 20mm; */
            font-style: italic;


        }

        .sig-org {
            font-size: 9pt;
            font-weight: bold;
        }   

        /* QR - valores dinámicos desde $qrConfig */
        .qr {
            position: absolute;
            @if(isset($qrConfig['position']['right']))
            right: {{ $qrConfig['position']['right'] }}mm;
            @else
            right: 6mm;
            @endif
            @if(isset($qrConfig['position']['bottom']))
            bottom: {{ $qrConfig['position']['bottom'] }}mm;
            @else
            bottom: 6mm;
            @endif
            @if(isset($qrConfig['position']['left']))
            left: {{ $qrConfig['position']['left'] }}mm;
            @endif
            @if(isset($qrConfig['position']['top']))
            top: {{ $qrConfig['position']['top'] }}mm;
            @endif
            @if(isset($qrConfig['size']['width']))
            width: {{ $qrConfig['size']['width'] }}mm;
            @else
            width: 20mm;
            @endif
            @if(isset($qrConfig['size']['height']))
            height: {{ $qrConfig['size']['height'] }}mm;
            @else
            height: 20mm;
            @endif
        }

        .qr img {
            width: 100%;
            height: 100%;
        }

        /* Nota/Disclaimer - valores dinámicos desde $notaConfig */
        .disclaimer {
            position: absolute;
            @if(isset($notaConfig['position']['left']))
            left: {{ $notaConfig['position']['left'] }}mm;
            @else
            left: 45mm;
            @endif
            @if(isset($notaConfig['position']['bottom']))
            bottom: {{ $notaConfig['position']['bottom'] }}mm;
            @else
            bottom: 14mm;
            @endif
            @if(isset($notaConfig['position']['right']))
            right: {{ $notaConfig['position']['right'] }}mm;
            @endif
            @if(isset($notaConfig['position']['top']))
            top: {{ $notaConfig['position']['top'] }}mm;
            @endif
            @if(isset($notaConfig['position']['width']))
            width: {{ $notaConfig['position']['width'] }}mm;
            @else
            width: 70mm;
            @endif
            @if(isset($notaConfig['style']))
            {{ \App\Models\PlantillaCertificado::toInlineCss($notaConfig['style']) }}
            @else
            font-size: 5pt;
            color: #111;
            @endif
        }   
    </style>
</head>

<body>
    @foreach(($areas ?? [1]) as $area => $detalles)
    <div class="page">

        <div class="block-header center upper">
            <div>MINISTERIO DE SALUD Y DEPORTES</div>
            <div>INSTITUTO NACIONAL DE LABORATORIOS DE SALUD</div>
            <div>“DR. NÉSTOR MORALES VILLAZÓN”</div>
        </div>

        <div class="block-title center">
            <div class="confiere">Confiere el presente:</div>
            <div class="cert-title upper"><b>CERTIFICADO DE
                @if( $type == Certificado::TYPE_PARTICIPACION)
                PARTICIPACIÓN
                @else
                DESEMPEÑO
                @endif
                </b>
            </div>
        </div>

        <div class="block-a">
            <table width="80%" cellspacing="0" cellpadding="0" style="margin: auto;">
                <tr>
                    <td style="width: 5%; vertical-align: top;">
                        <div class="label-a upper">A:</div>
                    </td>
                    <td style="vertical-align: top ; padding-top: 4mm;">
                        <div class="center upper lab" style="text-align: center;">{{ $nombreLaboratorio }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="block-paragraph center" style="{{ isset($descripcion['style']) ? PlantillaCertificado::toInlineCss($descripcion['style']) : ''}}">
            @if(isset($descripcion['text']))
            {!! Str::markdown($descripcion['text']) !!}
            @endif
        </div>

        @if( $type == Certificado::TYPE_PARTICIPACION)
        <div class="block-items center upper itemsEnsayos">
            {{$ensayosA}}
        </div>
        @else
        <div class="block-area center upper" style="font-family: 'DejaVu Sans', sans-serif;">
            <div class="area"> {{ $area }} </div>
        </div>

        <div class="block-items center upper items">
            @foreach(($detalles['detalles'] ?? []) as $detalle)
            <div>
                {{ mb_strtoupper($detalle['ensayo']) }} :
                {{ mb_strtoupper($detalle['ponderacion']) }}    
            </div>
            @endforeach
        </div>
        @endif

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
                        <div style="height: 10mm;">
                            @if(!empty($f['firma_data_uri']))
                            <img class="sig-img"src="{{ $f['firma_data_uri'] }}" style="height: 18mm; width:85%;" alt="Firma">
                            @endif
                        </div>
                        <div class="sig-name">{{ $f['nombre'] ?? '' }}</div>
                        <div class="sig-role upper">{{ $f['cargo'] ?? '' }}</div>
                        <div class="sig-org upper">INLASA</div>
                    </td>
                    @endforeach
                    @endif
                </tr>
            </table>
        </div>

        <div class="qr">
            <img src="{{ $qr }}" alt="QR">
        </div>

        @if(!empty($notaConfig['text']))
        <div class="disclaimer">
            {!! Str::markdown($notaConfig['text']) !!}
        </div>
        @endif

        {{-- ======= ELEMENTOS DINÁMICOS desde diseno.elements ======= --}}
        @foreach(($elements ?? []) as $index => $element)
        @php
        $elType = $element['type'] ?? 'text';
        $elPosition = $element['position'] ?? [];
        $elSize = $element['size'] ?? [];
        $elStyle = $element['style'] ?? [];
        $elText = $element['text'] ?? '';

        // Construir estilos de posición (solo left, right, top, bottom)
        $positionCss = 'position: absolute;';
        if (isset($elPosition['left'])) $positionCss .= " left: {$elPosition['left']}mm;";
        if (isset($elPosition['right'])) $positionCss .= " right: {$elPosition['right']}mm;";
        if (isset($elPosition['top'])) $positionCss .= " top: {$elPosition['top']}mm;";
        if (isset($elPosition['bottom'])) $positionCss .= " bottom: {$elPosition['bottom']}mm;";

        // Construir estilos de tamaño (width, height desde size)
        $sizeCss = '';
        if (isset($elSize['width'])) $sizeCss .= " width: {$elSize['width']}mm;";
        if (isset($elSize['height'])) $sizeCss .= " height: {$elSize['height']}mm;";

        // Estilos adicionales
        $styleCss = PlantillaCertificado::toInlineCss($elStyle);

        // Reemplazar variables en el texto
        $elText = str_replace('{{ gestion }}', $gestion ?? '', $elText);
        $elText = str_replace('{{ area }}', $firstArea ?? '', $elText);
        $elText = str_replace('{{ nombreLaboratorio }}', $nombreLaboratorio ?? '', $elText);
        @endphp

        @if($elType === 'text' && !empty($elText))
        <div class="dynamic-element-{{ $index }}" style="{{ $positionCss }} {{ $sizeCss }} {{ $styleCss }}">
            {!! Str::markdown($elText) !!}
        </div>
        @elseif($elType === 'qr')
        @php
        $qrWidth = $elSize['width'] ?? 20;
        $qrHeight = $elSize['height'] ?? 20;
        @endphp
        <div class="dynamic-qr-{{ $index }}" style="{{ $positionCss }} width: {{ $qrWidth }}mm; height: {{ $qrHeight }}mm;">
            <img src="{{ $qr }}" alt="QR" style="width: 100%; height: 100%;">
        </div>
        @elseif($elType === 'image' && (!empty($element['src_data_uri']) || !empty($element['src'])))
        @php
        $imgWidth = isset($elSize['width']) ? "width: {$elSize['width']}mm;" : '';
        $imgHeight = isset($elSize['height']) ? "height: {$elSize['height']}mm;" : '';
        $imgSrc = $element['src_data_uri'] ?? $element['src'];
        @endphp
        <div class="dynamic-image-{{ $index }}" style="{{ $positionCss }}">
            <img src="{{ $imgSrc }}" alt="" style="{{ $imgWidth }} {{ $imgHeight }} {{ $styleCss }}">
        </div>
        @endif
        @endforeach
    </div>
    @endforeach
</body>

</html>