@extends('certificados.pdf.layout')
@section('contenido')
    @foreach ($data as $area => $detalles)
        @php
            $cert = $detalles['certificado'];
        @endphp

        <body style="page-break-after: always;">
            <div class="page">

                <!-- Cinta superior izquierda -->
                {{-- <div class="ribbon">
                <div class="gold"></div>
                <div class="navy"></div>
            </div> --}}
                @include('certificados.pdf.header')

                <!-- Títulos -->
                <div>
                    <div style="text-align: center">
                        <img class="seal" src="{{ public_path('img/logoinlasa.jpg') }}" alt="Sello INLASA">
                    </div>
                    <div class="general_title">
                        <div class="title" style="text-align:center;">CERTIFICADO</div>
                        <div class="subtitle" style="text-align:center;">DE DESEMPEÑO A</div>
                        <div class="labname" style="text-align:center;">
                            {{ mb_strtoupper($cert->nombre_laboratorio) }}
                        </div>
                    </div>
                </div>

                <!-- Cuerpo -->
                <div class="content">
                    <p class="center">
                        Por su desempeño en el Programa de Evaluación Externa de la Calidad del Instituto Nacional de
                        Laboratorios de Salud - PEEC INLASA, en el área de:
                    </p>
                    <div class='evaluaciones'>
                        <div class="area">{{ mb_strtoupper($area) }}</div>

                        @foreach ($detalles['detalles'] as $detalle)
                            <div class="resultado">
                                {{ mb_strtoupper($detalle['ensayo']) }} :
                                <b>{{ mb_strtoupper($detalle['ponderacion']) }}</b>
                            </div>
                        @endforeach
                    </div>

                </div>

                @include('certificados.pdf.footer')
            </div>
        </body>
    @endforeach
@endsection

</html>
