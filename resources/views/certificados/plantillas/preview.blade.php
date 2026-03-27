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
            margin-left: 28mm;
        }

        .lab {
            font-size: 14pt;
            width: 60% !important;
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
            font-size: 9pt;
            line-height: 1.25;
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
            width: 100%;
        }

        .block-paragraph {
            position: absolute;
            display: inline-block;
            top: 85mm;
            width: 100%;
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
            position: absolute;
            bottom: {{ $signaturesConfig['position']['bottom'] ?? 25 }}mm;
            @if(isset($signaturesConfig['position']['top']))
            top: {{ $signaturesConfig['position']['top'] }}mm;
            @endif
            @if(isset($signaturesConfig['position']['left']))
            left: {{ $signaturesConfig['position']['left'] }}mm;
            @endif
            @if(isset($signaturesConfig['position']['right']))
            right: {{ $signaturesConfig['position']['right'] }}mm;
            @endif
            @if(isset($signaturesConfig['size']['width']))
            width: {{ $signaturesConfig['size']['width'] }}mm;
            @else
            width: 100%;
            @endif
            @if(isset($signaturesConfig['size']['height']))
            height: {{ $signaturesConfig['size']['height'] }}mm;
            @endif
            text-align: center;
            font-family: 'Raleway', 'Inter', 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
            @if(isset($signaturesConfig['style']))
            {{ \App\Models\PlantillaCertificado::toInlineCss($signaturesConfig['style']) }}
            @endif
        }

        .sig-name {
            font-size: {{ $signaturesConfig['text']['font-size'] ?? '9pt' }};
            font-style: italic;
        }

        .sig-role {
            font-size: {{ $signaturesConfig['text']['font-size'] ?? '9pt' }};
            font-weight: bold;
            font-style: italic;
        }

        .sig-org {
            font-size: {{ $signaturesConfig['text']['font-size'] ?? '9pt' }};
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
            @if(isset($qrConfig['style']))
            {{ \App\Models\PlantillaCertificado::toInlineCss($qrConfig['style']) }}
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
            @if(isset($notaConfig['size']['width']))
            width: {{ $notaConfig['size']['width'] }}mm;
            @elseif(isset($notaConfig['position']['width']))
            width: {{ $notaConfig['position']['width'] }}mm;
            @else
            width: 70mm;
            @endif
            @if(isset($notaConfig['size']['height']))
            height: {{ $notaConfig['size']['height'] }}mm;
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
    @php
    $headerLines = $headerConfig['lines'] ?? [
        'INSTITUTO NACIONAL DE LABORATORIOS DE SALUD - INLASA',
        '”DR. NÉSTOR MORALES VILLAZÓN”',
        'PROGRAMA DE EVALUACION EXTERNA DE LA CALIDAD',
    ];
    $headerPos  = $headerConfig['position'] ?? [];
    $headerSize = $headerConfig['size']     ?? [];
    $headerPosCss = !empty($headerPos) ? 'position: absolute;' : '';
    if (isset($headerPos['top']))    $headerPosCss .= ' top: '    . $headerPos['top']    . 'mm;';
    if (isset($headerPos['left']))   $headerPosCss .= ' left: '   . $headerPos['left']   . 'mm;';
    if (isset($headerPos['right']))  $headerPosCss .= ' right: '  . $headerPos['right']  . 'mm;';
    if (isset($headerPos['bottom'])) $headerPosCss .= ' bottom: ' . $headerPos['bottom'] . 'mm;';
    $headerSizeCss = '';
    if (isset($headerSize['width']))  $headerSizeCss .= 'width: '   . $headerSize['width']  . 'mm;';
    if (isset($headerSize['height'])) $headerSizeCss .= ' height: ' . $headerSize['height'] . 'mm;';
    $headerStyleCss = PlantillaCertificado::toInlineCss($headerConfig['style'] ?? []);

    $tituloPos  = $tituloConfig['position'] ?? [];
    $tituloSize = $tituloConfig['size']     ?? [];
    $tituloPosCss = !empty($tituloPos) ? 'position: absolute;' : '';
    if (isset($tituloPos['top']))    $tituloPosCss .= ' top: '    . $tituloPos['top']    . 'mm;';
    if (isset($tituloPos['left']))   $tituloPosCss .= ' left: '   . $tituloPos['left']   . 'mm;';
    if (isset($tituloPos['right']))  $tituloPosCss .= ' right: '  . $tituloPos['right']  . 'mm;';
    if (isset($tituloPos['bottom'])) $tituloPosCss .= ' bottom: ' . $tituloPos['bottom'] . 'mm;';
    $tituloSizeCss = '';
    if (isset($tituloSize['width']))  $tituloSizeCss .= 'width: '   . $tituloSize['width']  . 'mm;';
    if (isset($tituloSize['height'])) $tituloSizeCss .= ' height: ' . $tituloSize['height'] . 'mm;';
    $confiereTxt        = $tituloConfig['confiere']['text']    ?? 'Confiere el presente:';
    $confiereStyleCss   = PlantillaCertificado::toInlineCss($tituloConfig['confiere']['style']   ?? []);
    $certTituloStyleCss = PlantillaCertificado::toInlineCss($tituloConfig['certTitulo']['style'] ?? []);
    @endphp

    @foreach(($areas ?? [[]]) as $paginaArea)
    <div class="page">

        <div class="block-header center upper" style="{{ $headerPosCss }} {{ $headerSizeCss }} {{ $headerStyleCss }}">
            @foreach($headerLines as $headerLine)
            <div>{{ $headerLine }}</div>
            @endforeach
        </div>

        <div class="block-title center" style="{{ $tituloPosCss }} {{ $tituloSizeCss }}">
            <div class="confiere" style="{{ $confiereStyleCss }}">{{ $confiereTxt }}</div>
            <div class="cert-title upper" style="{{ $certTituloStyleCss }}"><b>CERTIFICADO DE
                @if( $type == Certificado::TYPE_PARTICIPACION)
                PARTICIPACIÓN
                @else
                DESEMPEÑO
                @endif
                </b>
            </div>
        </div>

        @php
        $descStyle = is_array($descripcion) ? ($descripcion['style'] ?? []) : [];
        $descFontSize = $descStyle['font-size'] ?? $descStyle['fontSize'] ?? '14pt';
        $labLen = mb_strlen($nombreLaboratorio ?? '');
        if ($labLen <= 80) {
            $labFontSize = $descFontSize;
        } elseif ($labLen <= 120) {
            $labFontSize = '12pt';
        } else {
            $labFontSize = '10pt';
        }

        $labelATxt      = $labelAConfig['text']     ?? 'A:';
        $labelAStyleCss = PlantillaCertificado::toInlineCss($labelAConfig['style'] ?? []);
        $labelAPos      = $labelAConfig['position'] ?? [];
        $labelASize     = $labelAConfig['size']     ?? [];

        $blockACss  = 'position: absolute; width: 100%;';
        $blockACss .= ' top: ' . ($labelAPos['top'] ?? 65) . 'mm;';
        if (isset($labelAPos['left']))   $blockACss .= ' left: '   . $labelAPos['left']   . 'mm;';
        if (isset($labelAPos['right']))  $blockACss .= ' right: '  . $labelAPos['right']  . 'mm;';
        if (isset($labelAPos['bottom'])) $blockACss .= ' bottom: ' . $labelAPos['bottom'] . 'mm;';

        $labelASizeCss = '';
        if (isset($labelASize['width']))  $labelASizeCss .= 'width: '   . $labelASize['width']  . 'mm;';
        if (isset($labelASize['height'])) $labelASizeCss .= ' height: ' . $labelASize['height'] . 'mm;';

        $labelNombreLabStyleCss = PlantillaCertificado::toInlineCss($labelNombreLabConfig['style'] ?? []);
        $labelNombreLabSize     = $labelNombreLabConfig['size'] ?? [];
        $labWidthCss  = isset($labelNombreLabSize['width'])  ? ('width: '  . $labelNombreLabSize['width']  . 'mm;') : 'width: 70%;';
        $labHeightCss = isset($labelNombreLabSize['height']) ? ('height: ' . $labelNombreLabSize['height'] . 'mm;') : '';
        @endphp
        <div style="{{ $blockACss }}">
            <div class="label-a upper" style="height: 12px; {{ $labelASizeCss }} {{ $labelAStyleCss }}">{{ $labelATxt }}</div>
            <div class="upper lab" style="text-align: center; font-size: {{ $labFontSize }}; word-break: break-word; {{ $labWidthCss }} {{ $labHeightCss }} margin:auto; {{ $labelNombreLabStyleCss }}">{{ $nombreLaboratorio }}</div>
        </div>

        <div class="block-paragraph center">
            <span style="display: block; width: 70%; margin:auto; {{ isset($descripcion['style']) ? PlantillaCertificado::toInlineCss($descripcion['style']) : ''}}">
            @if(isset($descripcion['text']))
                {!! Str::markdown($descripcion['text']) !!}
            @endif
            </span>
        </div>

        @if( $type == Certificado::TYPE_PARTICIPACION)
        <div class="block-items center upper itemsEnsayos">
            {{$ensayosA}}
        </div>
        @else
        <div class="block-area center upper" style="font-family: 'DejaVu Sans', sans-serif;">
            <div class="area"> {{ $paginaArea['area'] ?? '' }} </div>
        </div>

        <div class="block-items center upper items">
            @foreach(($paginaArea['detalles'] ?? []) as $detalle)
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
            @php
            $sigImgHeight  = $signaturesConfig['img']['height']  ?? 18;
            $sigImgWidth   = $signaturesConfig['img']['width']   ?? 85;
            $sigImgOverlap = $signaturesConfig['img']['overlap'] ?? 5;
            $sigOrgLabel  = $signaturesConfig['org'] ?? 'INLASA';

            $sigTable        = $signaturesConfig['firmas'] ?? [];
            $sigTableWidth   = $sigTable['width']       ?? '100%';
            $sigTableSpacing = $sigTable['cellspacing']  ?? '0';
            $sigTablePadding = $sigTable['cellpadding']  ?? '0';
            // style de la tabla: si no hay firmas.style, usa el fallback legacy 'padding'
            $sigTableStyleArr = $sigTable['style'] ?? [];
            if (empty($sigTableStyleArr) && isset($signaturesConfig['padding'])) {
                $sigTableStyleArr = ['padding' => $signaturesConfig['padding']];
            }
            if (empty($sigTableStyleArr)) {
                $sigTableStyleArr = ['padding' => '0mm 30mm'];
            }
            $sigTableStyleCss = \App\Models\PlantillaCertificado::toInlineCss($sigTableStyleArr);
            @endphp
            <table width="{{ $sigTableWidth }}" cellspacing="{{ $sigTableSpacing }}" cellpadding="{{ $sigTablePadding }}" style="{{ $sigTableStyleCss }}">
                <tr>
                    @if($sig->isEmpty())
                    <td align="center" valign="bottom" style="width: 100%;">
                        <div class="sig-name">FIRMANTE</div>
                        <div class="sig-role upper">CARGO</div>
                        <div class="sig-org upper">{{ $sigOrgLabel }}</div>
                    </td>
                    @else
                    @foreach($sig as $f)
                    <td align="center" valign="bottom" style="width:{{ $colWidth }}% !important;">
                        <div style="height: {{ $sigImgHeight }}mm; margin-bottom: -{{ $sigImgOverlap }}mm; overflow: visible;">
                            @if(!empty($f['firma_data_uri']))
                            <img class="sig-img" src="{{ $f['firma_data_uri'] }}" style="display: block; margin: 0 auto; height: {{ $sigImgHeight }}mm; width:{{ $sigImgWidth }}%;" alt="Firma">
                            @endif
                        </div>
                        <div class="sig-name">{{ $f['nombre'] ?? '' }}</div>
                        <div class="sig-role upper">{{ $f['cargo'] ?? '' }}</div>
                        <div class="sig-org upper">{{ $sigOrgLabel }}</div>
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