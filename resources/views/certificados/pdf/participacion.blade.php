@php
    $cert = $certificado;
@endphp
@extends('certificados.pdf.layout')
@section('contenido')

    <body>
        <div class="page">

            <!-- Cinta superior izquierda -->
            {{-- <div class="ribbon">
                <div class="gold"></div>
                <div class="navy"></div>
            </div> --}}

            <!-- Encabezado -->
            @include('certificados.pdf.header')

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
                <div class='evaluaciones'>
                    <div class="area">{{ mb_strtoupper($ensayosA) }}</div>
                </div>

            </div>

            @include('certificados.pdf.footer')
        </div>
    </body>
@endsection
