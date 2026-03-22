@php
    $cert = $certificado;
@endphp
@extends('certificados.pdf.layout')
@section('contenido')

    <body>
        <div class="ribbon">
            <div class="img">
                <img src="{{ public_path('certificados/gestion2024_dis-ok.png') }}" alt="">
            </div>
            {{-- <div class="gold"></div>
                <div class="navy"></div> --}}
        </div>
        <div class="page">

            <!-- Encabezado -->
            @include('certificados.pdf.header')

            <!-- Títulos -->
            <div>
                <div style="text-align: center">
                    <img class="seal" src="{{ public_path('img/logoinlasa.jpg') }}" alt="Sello INLASA">
                </div>
                <div class="title" style="text-align:center;">CERTIFICADO</div>
                <div class="subtitle" style="text-align:center;">DE PARTICIPACIÓN A</div>
                @php
                $labName = mb_strtoupper($certificado->nombre_laboratorio ?? '');
                $labLen = mb_strlen($labName);
                if ($labLen <= 35) {
                    $labFontSize = '25pt';
                } elseif ($labLen <= 55) {
                    $labFontSize = '20pt';
                } elseif ($labLen <= 75) {
                    $labFontSize = '16pt';
                } else {
                    $labFontSize = '13pt';
                }
                @endphp
                <div class="labname" style="text-align:center; font-size: {{ $labFontSize }}; word-break: break-word;">
                    {{ $labName }}
                </div>
            </div>

            <!-- Cuerpo -->
            <div class="content">
                <p class="center">
                    {{ $descripcionTexto ?? 'Por su participación en el Programa de Evaluación Externa de la Calidad del Instituto Nacional de Laboratorios de Salud - PEEC INLASA, en' }}
                </p>
                <div class='evaluaciones'>
                    <div class="ensayos">{{ mb_strtoupper($ensayosA) }}</div>
                </div>

            </div>

            @include('certificados.pdf.footer')
        </div>
    </body>
@endsection
