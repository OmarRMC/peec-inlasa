@php
    use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Formulario PEEC - INLASA</title>
    <style>
        /* body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 40px;
        } */

        body {
            margin: 0;
            font-family: Calibri, 'Trebuchet MS', sans-serif;
            font-size: 11px;
            /* evitar márgenes extra internos */
            transform: scaleY(1.05);
        }

        .flex-container {
            display: flex;
            justify-content: space-between;
        }

        .logo {
            width: 100px;
        }

        .header {
            text-align: center;
            line-height: 1.4;
        }

        .right {
            text-align: right;
            font-size: 10px;
        }

        .section-title {
            font-weight: bold;
            font-size: 13px;
            margin-top: 15px;
            border-top: 1px solid black;
            padding-top: 4px;
        }

        /*

        table {
            width: 100%;
            font-size: 11px;
            margin-bottom: 8px;
        }

        td {
            padding: 2px 4px;
            vertical-align: top;
        } */

        .bold {
            font-weight: bold;
        }

        .area-title {
            font-weight: bold;
            margin-left: 10px;
        }

        .test-line {
            margin-left: 20px;
        }

        .right-amount {
            float: right;
            font-weight: bold;
        }

        .footer {
            font-size: 9px;
            margin-top: 35px;
        }

        .signature {
            text-align: center;
            margin-top: 40px;
        }

        .signature td {
            padding-top: 40px;
        }

        .pagenum:before {
            content: counter(page);
        }

        .pagecount:before {
            content: counter(pages);
        }

        @page {
            margin: 30px 25px 35px 25px;
            /* margin: 100px 50px 120px 50px; */
            /* top right bottom left */
            /* margin: 20px; */
            /* top, right, bottom, left */
        }

        .contenido {
            padding-left: 40px;
        }
    </style>
</head>

<body>

    <table style="width: 100%; margin-bottom: 8px;">
        <tr style="height: 100px">
            <td style="width: 15%;  vertical-align: middle; position: relative;  padding-top: 2px;">
                <img src="{{ public_path('img/logoinlasa.jpg') }}" alt="Logo INLASA" style="width: 90%;">
            </td>
            <td style="width: 55%; text-align: left; font-size: 10px; line-height: 1;">
                <strong style="font-size: 15px;">INLASA</strong><br>
                <strong style="font-size: 13px">Instituto Nacional de Laboratorios de Salud</strong><br>
                <div>
                    "Dr. Néstor Morales Villazón"<br>
                    Dirección: Pasaje Rafael Zubieta N°1889, Miraflores<br>
                    Teléfonos: 2226048 - 2226670 - 2225194<br>
                    Sitio web : www.inlasa.gob.bo
                </div>
            </td>
            <td style="width: 30%; text-align: right; font-size: 10px;">
                <strong style="font-size: 11px;">FORMULARIO DE INSCRIPCION</strong><br>
                {{ $formulario->codigo }} v.{{ $formulario->version }}<br>
                <i>Fecha de inscripción: {{ $fecha_inscripcion }}</i>
            </td>
        </tr>
    </table>

    <div class="contenido">
        <div style="text-align: center;">
            <strong
                style="font-size: 13px;">{{ $formulario->titulo ?? 'Programa de Evaluación Externa de la Calidad' }}</strong><br>
            <strong style="font-size: 12px;">GESTIÓN {{ $inscripcion->gestion }}</strong>
        </div>

        <p style="font-size: 8.5px; font-style: italic; margin-top: 5px;">
            NOTA: Para cualquier modificación de los datos proporcionados en el formulario de inscripción deberán ser
            notificados vía correo electrónico al PEEC INLASA de manera oportuna.
        </p>

        <div style="font-weight: bold; font-size: 11px; margin-top: 10px; margin-bottom:5px;">
            1. INFORMACION DEL LABORATORIO
        </div>

        <table style="width: 100%; font-size: 10px; line-height: 1.06; border-spacing: 0;">
            <tr>
                <td style="width: 33%;"><strong>Código del laboratorio:</strong> {{ $laboratorio->cod_lab }}</td>
                <td style="width: 33%;"><strong>Nro. registro SEDES:</strong> {{ $laboratorio->numsedes_lab ?? '' }}
                </td>
            </tr>
            <tr>
                <td colspan="3"><strong>Nombre del Laboratorio:</strong> {{ $laboratorio->nombre_lab }}</td>
            </tr>
            <tr>
                <td style="width: 33%;"><strong>Sigla:</strong> {{ $laboratorio->sigla_lab }}</td>
                <td style="width: 33%;"> <strong>Nivel del laboratorio:</strong>
                    {{ $laboratorio->nivel->descripcion_nivel }}</td>
                <td style="width: 33%;"><strong>Tipo de laboratorio: </strong>{{ $laboratorio->tipo->descripcion }} /
                    {{ $laboratorio->categoria->descripcion }}</td>
            </tr>
            <tr>
                <td colspan="3">
                    <strong>Resp. laboratorio:</strong> {{ $laboratorio->respo_lab }} &nbsp;&nbsp; <strong>C.I.
                        :</strong>
                    {{ $laboratorio->ci_respo_lab }}
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <strong>Rep. Legal:</strong> {{ $laboratorio->repreleg_lab }} &nbsp;&nbsp; <strong>C.I.
                        :</strong> {{ $laboratorio->ci_repreleg_lab }}
                </td>
            </tr>

            <tr>
                <td style="width: 33%;"><strong>Departamento:</strong> {{ $laboratorio->departamento->nombre_dep }}
                </td>
                <td style="width: 33%;"><strong>Provincia:</strong> {{ $laboratorio->provincia->nombre_prov }}</td>
                <td style="width: 33%;"><strong>Municipio:</strong> {{ $laboratorio->municipio->nombre_municipio }}
                </td>
            </tr>
            <tr>
                <td colspan="3"><strong>Dirección:</strong> {{ $laboratorio->direccion_lab }}</td>
            </tr>
            <tr>
                <td style="width: 33%;"><strong>Zona:</strong> {{ $laboratorio->zona_lab }}</td>
                <td style="width: 33%;"><strong>Nro. Teléfono:</strong> {{ $laboratorio->usuario->telefono }}</td>
                <td style="width: 33%;"><strong>Nro. Celular(1):</strong> {{ $laboratorio->wapp_lab }}</td>
            </tr>
            <tr>
                <td style="width: 33%;" colspan="3"><strong>Nro. Celular(2):</strong> {{ $laboratorio->wapp2_lab }}</td>
            </tr>
            <tr>
                <td colspan="3" style="width: 100%">
                    <div style="width:49%; display: inline-block;">
                        <strong>E-mail (1):</strong> {{ $laboratorio->mail_lab }}
                    </div>
                    <div style="width: 49%; display: inline-block;">
                        <strong>E-mail (2):</strong> {{ $laboratorio->mail2_lab }}
                    </div>
                </td>
            </tr>
        </table>


        {{-- SECCIÓN 2 --}}
        <div style="font-weight: bold; font-size: 11px; margin-top: 20px; margin-bottom: 5px;">
            2. PROGRAMAS INSCRITOS
        </div>

        @foreach ($programas as $programa)
            <div style="font-weight: bold; border: 1px solid black; padding: 3px 4px; margin-top: 10px;">
                PROGRAMA: {{ strtoupper($programa['nombre']) }}
            </div>

            @foreach ($programa['areas'] as $nombreArea => $areas)
                <div style="margin-left: 10px;">
                    ÁREA: {{ strtoupper($nombreArea) }}
                </div>

                <table style=" padding-left: 25px ; width: 100%; border-collapse: collapse; margin-bottom: 1px;">
                    <tbody>
                        @foreach ($areas as $line)
                            <tr>
                                <td style=" width:80%; border-bottom: 1px solid #eee;">
                                    {{ $line['paquete'] }}
                                </td>
                                <td style=" width: 3%; border-bottom: 1px solid #eee;">
                                    <strong>Costo:</strong>
                                </td>
                                <td style="width: 17%; text-align: right; border-bottom: 1px solid #eee;">
                                    {{ number_format($line['costo'], 0) }} Bs.
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach
        @endforeach

        <div style="text-align: right; font-weight: bold; font-size: 12px; margin-top: 10px;">
            {{-- Costo Total: {{ number_format($total, 0) }} Bs. --}}
            Costo Total: {{ $total }} Bs.
        </div>


        {{-- SECCIÓN 3 --}}

        <div style="font-weight: bold;  font-size: 11px; margin-top: 5px; margin-bottom: 1px;">
            3. CONDICIONES DE INSCRIPCIÓN
        </div>

        <p style="font-size: 11px; text-align: justify;line-height: 1;">
            <i>
                El Laboratorio participante se compromete a cumplir con las condiciones de participación estipuladas en
                la
                convocatoria del PEEC INLASA 2025
                aceptando el pago de los aranceles establecidos para cada programa. El Laboratorio participante se
                compromete a realizar la cancelación del total hasta el {{ $fechaLimitePago }} impostergablemente.
            </i>
        </p>

        <p style="font-size: 11px;"><i>Como constancia de la conformidad de lo declarado, firmamos al pie.</i></p>

        <p style="text-align: right; font-size: 11px; margin-top: 10px;">
            <i>Fecha de inscripción: {{ $fecha_inscripcion }}</i>
        </p>

        <table style="width: 100%; margin-top: 100px; font-size: 10px; text-align: center;">
            <tr>
                <td style="width: 50%;">
                    ___________________________________<br>
                    {{ $laboratorio->respo_lab }}<br>
                    RESPONSABLE DE LABORATORIO<br>
                    CI: {{ $laboratorio->ci_respo_lab }}
                </td>
                <td style="width: 50%;">
                    ___________________________________<br>
                    {{ $laboratorio->repreleg_lab }}<br>
                    REPRESENTANTE LEGAL<br>
                    CI: {{ $laboratorio->ci_repreleg_lab }}
                </td>
            </tr>
        </table>
    </div>
    {{-- Footer de página --}}
    <div style="position: fixed; bottom: -15px; left: 40px; right: 0; height: 50px; font-size: 9px;">
        <table style="width: 100%;">
            <tr>
                <td style="text-align: left; width: 33%">
                    <em>Generado por: {{ $generado_por }} el {{ $fecha_generacion }}</em><br>
                    <em>Emisión: 16/10/2022</em>
                </td>
                <td style="text-align: center; font-weight: bold; ; width: 33%">
                    SigPEEC | INLASA
                </td>
                <td style="text-align: right;">
                    {{-- <em style="font-size: 9px;">
                        Página <span class="pagenum"></span> / <span class="pagecount"></span>
                    </em> --}}
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
