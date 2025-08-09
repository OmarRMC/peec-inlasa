@php
    use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Contrato PEEC - INLASA</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 25px;
            transform: scaleY(1.05);
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .section {
            margin-top: 10px;
        }

        .clause {
            margin-bottom: 10px;
            text-align: justify;
        }

        .signature-section {
            margin-top: 100px;
        }

        .signature-table {
            width: 100%;
            text-align: center;
            font-size: 11px;
        }

        .footer {
            position: fixed;
            bottom: 0px;
            left: 0;
            right: 0;
            font-size: 9px;
        }

        .strong {
            font-weight: 600;
        }

        @page {
            margin: 30px 25px 35px 25px;
        }

        p {
            margin: 0;
            line-height: 1.1;
        }

        div {
            margin: 0;
        }
    </style>
</head>

<body>
    <table style="width: 100%;">
        <tr>
            <td style="width: 15%;">
                <img src="{{ public_path('img/logoinlasa.jpg') }}" style="width: 100px;" alt="Logo INLASA">
            </td>
            <td style="width: 55%; font-size: 10px; line-height: 12px;">
                <strong style="font-size: 15px;">INLASA</strong><br>
                <strong style="font-size: 13px">Instituto Nacional de Laboratorios de Salud</strong><br>
                <div style="font-size: 10px; line-height: 10px;">
                    "Dr. Néstor Morales Villazón"<br>
                    Dirección: Pasaje Rafael Zubieta N°1889, Miraflores<br>
                    Teléfonos: 2226048 - 2226670 - 2225194<br>
                    Sitio web : www.inlasa.gob.bo
                </div>
            </td>
            <td style="width: 30%;" class="text-right">
                {{-- <strong>CONTRATO</strong><br>
                N° {{ $contrato_numero ?? 'MSyD/INLASA/PEEC/XXXX/2025' }}<br>
                <i>Fecha: {{ $fecha_contrato ?? now()->format('d/m/Y') }}</i> --}}
            </td>
        </tr>
    </table>

    <p class="text-center bold" style="font-size: 13px;">
        CONTRATO N° {{ $contrato_numero }}
    </p>

    <div class="clause" style="margin: 0">
        <p>
            El INSTITUTO NACIONAL DE LABORATORIOS DE SALUD "Dr Néstor Morales Villazón" INLASA, con Número de
            Identificación Tributaria N° 1001419025 y domicilio establecido en la calle Rafael Zubieta N°1889 (al Lado
            del Hospital
            del Niño) de la zona de Miraflores de la ciudad de La Paz, legalmente representado por su Directora General
            Ejecutiva
            Dra. Evelin Esther Fortún Fernández, titular de la Cédula de Identidad Nº 4824138 L.P., en mérito a la
            Resolución
            Ministerial N° 0533 de 16 de diciembre 2020, quien en adelante y para efectos del presente contrato se
            denominará
            INLASA.
            </br>
            Por otro lado, el Laboratorio <strong>{{ $laboratorio->nombre_lab }}</strong> participante del Programa de
            Evaluación Externa de la Calidad - INLASA {{ $gestion }}, con domicilio establecido en
            <strong>{{ $laboratorio->zona_lab }}, {{ $laboratorio->direccion_lab }}</strong>
            de la ciudad/departamento de <strong>{{ $departamento }}</strong>, legalmente representado
            por <strong>{{ $laboratorio->repreleg_lab }}</strong>, titular de la cedula identidad
            Nro. <strong>{{ $laboratorio->ci_repreleg_lab }}</strong>, bajo responsabilidad técnica del
            Dr/Dra. <strong>{{ $laboratorio->respo_lab }}</strong>, que para fines de ejecución y seguimiento del
            presente
            contrato se denominara "PARTICIPANTE".
        </p>
    </div>

    <div class="section">
        <strong>CLÁUSULA PRIMERA.- (ANTECEDENTES)</strong>
        <p class="clause">
            Resolución Ministerial N° 0017 de fecha 20 de enero de 2006, crea y reconoce los Programas de Evaluación
            Externa de
            Calidad (PEEC´s) y les asigna la tarea de cumplir con la evaluación externa de desempeño bajo
            responsabilidad
            Técnica de los Laboratorios de Referencia Nacional y Departamental en el ámbito de su jurisdicción. </br>
            Resolución Administrativa N° 014/2013 de fecha 17 de diciembre de 2013, se reconoce Oficialmente el
            "Programa de Evaluación Externa de la Calidad en Hematología y Química Sanguínea (PEEC)", del Laboratorios
            de Análisis
            Clínico dependiente del Instituto Nacional de Laboratorios de Salud. </br>
            Resolución Administrativa Nº 08-A de fecha 16 de abril de 2015, crea el Programa de Evaluación Externa de la
            Calidad,
            el cual se encuentra dentro del organigrama interno - INLASA de la Unidad de Diagnostico. </br>
            El INLASA en el marco de sus competencias en fecha 11 de septiembre de 2024, mediante Resolución
            Administrativa
            INLASA/UJ Nº17/2024, aprueba la "{{ $convocatoria }}", del Instituto Nacional de
            Laboratorios de Salud "Dr. Néstor Morales Villazón" - INLASA de conformidad al documento denominado
            "Convocatoria
            Gestión {{ $gestion }} - Programa de Evaluación Externa de la Calidad INLASA" y Anexo 1 - "Oferta de
            Servicios".
        </p>
    </div>

    <div class="section">
        <strong>CLÁUSULA SEGUNDA.- (OBJETO)</strong>
        <p class="clause">
            El presente contrato tiene por objeto establecer las condiciones para la prestación de SERVICIOS PRESTADOS
            POR EL
            INLASA PARA LA EVALUACIÓN EXTERNA DE LA CALIDAD y lograr mayor efectividad en las acciones realizadas para
            el
            cumplimiento de las condiciones establecidas en la "{{ $convocatoria }}".
        </p>
    </div>

    <div class="section">
        <strong>CLÁUSULA TERCERA.- (OBLIGACIONES DE INLASA)</strong>
        <p class="clause">
            a) Proporcionar muestras de control de calidad externas al PARTICIPANTE, según cronograma establecido a
            inicio de la gestión {{ $gestion }}. </br>
            b) Recibir y evaluar los resultados enviados por el PARTICIPANTE proporcionando Informes de Evaluación en
            los plazos establecidos en el cronograma de actividades {{ $gestion }}. </br>
            c) Mantener la confidencialidad de la información del PARTICIPANTE y de los resultados de la evaluación,
            salvo cuando sea legalmente requerido o cuando el laboratorio decida voluntariamente levantarla. </br>
            d) Proporcionar al PARTICIPANTE, cuando lo solicite, asesoramiento técnico sobre los programas en los que
            está inscrito. </br>
            e) Difundir las actividades del PEEC - INLASA en todo el territorio nacional. </br>
            f) Extender anualmente al PARTICIPANTE el Certificado Digital de Participación y Certificado Digital de
            Desempeño; </br>
            siempre y cuando el PARTICIPANTE no tenga deudas pendientes con la institución.</br>
            g) Poner a disposición del PARTICIPANTE instrucciones relevantes al Programa y actividades a desarrollarse
            durante la prestación del servicio. </br>
            h) Informar con la debida antelación, de cambios en la estructura que puedan incidir en el flujo de trabajo
            previamente establecido como formato de formularios de resultados, informe de evaluación, Cronograma u otros
            que
            pudieran influir en el servicio. </br>
            i) Informar al laboratorio participante cuando se subcontrata el suministro de la muestra de control </br>
        </p>
    </div>

    <div class="section">
        <strong>CLÁUSULA CUARTA.- (OBLIGACIONES DEL LABORATORIO PARTICIPANTE)</strong>
        <p class="clause">
            a) Realizar las pruebas incluidas en los programas contratados con el PEEC - INLASA. </br>
            b) Seguir las instrucciones proporcionadas por el PEEC - INLASA para realizar correctamente el análisis de
            los ítems de ensayo. </br>
            c) Enviar los resultados obtenidos al enlace proporcionado según el área que corresponda cumpliendo las
            fechas establecidas en el Cronograma. </br>
            d) Participar de las reuniones de retroalimentación y responder a encuestas de satisfacción realizadas por
            el PEEC - INLASA para mejorar el Programa. </br>
            e) Apelar los resultados de su evaluación, cuando lo considere necesario. </br>
            f) Mantener actualizados sus registros de métodos y equipos, informando al PEEC - INLASA cualquier cambio en
            los mismos. </br>
            g) Pagar la tasa de inscripción por la participación en el Programa, de conformidad a los plazos
            establecidos en la "{{ $convocatoria }}", {{ $fechaLimitePago }}. </br>
        </p>
    </div>

    <div class="section">
        <strong>CLÁUSULA QUINTA.- (VIGENCIA Y VALIDEZ)</strong>
        <p class="clause">
            El presente contrato tendrá vigencia desde el 01 de enero al 31 de diciembre de {{ $gestion }}.
            Durante
            este periodo,
            cualquier
            inscripción, acción o acuerdo relacionado con los términos del presente contrato será considerada válida y
            estará
            amparada por las disposiciones aquí establecidas. Las partes reconocen que este documento constituye el
            marco legal
            que respalda dichas inscripciones.
        </p>
    </div>

    <div class="section">
        <strong>CLÁUSULA SEXTA.- (RESOLUCIÓN DE CONTRATO)</strong>
        <p class="clause">
            Este contrato podrá rescindirse por las siguientes causales:
            a) Incumplimiento al punto 11. Pago del Servicio de la "{{ $convocatoria }}".
            b) Incumplimiento en el envío de muestras establecido en el cronograma de actividades por parte de INLASA.
            c) Incumplimiento en lo establecido en la "{{ $convocatoria }}".
        <p style="text-align: justify;">
            De ocurrir una de las causas anteriormente señaladas, cualquiera de las partes deberá notificar a la otra su
            intención de
            resolver el contrato, estableciendo en forma clara y específica la causa en la que se funda.
            La Primera notificación de intención de resolución del contrato, deberá ser cursada en un plazo de cinco (5)
            días hábiles
            posteriores al hecho generador de la resolución del contrato, especificando la causal de resolución, que
            deberán ser
            efectuadas mediante carta dirigida al INLASA o al PARTICIPANTE según corresponda.
            Si la causal argumentada es subsanada, no prosigue la resolución. Empero, si no existe solución a la
            conclusión en el
            plazo de diez (10) días hábiles, se debe cursar una segunda carta comunicando que la resolución se ha hecho
            efectiva.
        </p>
        <p style="text-align: justify;">
            Cuando se efectúe la resolución del contrato se procederá a una liquidación de saldos deudores y acreedores
            de ambas
            partes, efectuándose los pagos a que hubiere lugar, conforme la evaluación del grado de cumplimiento del
            PARTICIPANTE.
        </p>
        </p>
    </div>

    <div class="section">
        <strong>CLÁUSULA SÉPTIMA.- (SEGURIDAD Y CONFIDENCIALIDAD DE DATOS)</strong>
        <p class="clause">
            El INLASA se compromete mantener la confidencialidad y seguridad de los datos propios del PARTICIPANTE. Todo
            el
            personal del PEEC, asume el compromiso de confidencialidad mediante la firma de un documento interno.
            El INLASA establece controles técnicos y administrativos apropiados para garantizar la confidencialidad,
            integridad y
            disponibilidad de los Datos Personales sujetos a Tratamiento, además de velar por el cumplimiento de la
            normativa
            vigente relacionada a la Protección de Datos y demás normas que versan sobre privacidad y protección de
            datos
            personales.
            </br>
            El PARTICIPANTE asume la responsabilidad de protección de datos y confidencialidad de la información de su
            cuenta
            en el SIGPEEC, a la cual tiene acceso mediante un Usuario y Contraseña propio de cada laboratorio.

        </p>
    </div>

    <div class="section">
        <strong>CLÁUSULA OCTAVA.- (SOLUCIÓN DE CONTROVERSIAS)</strong>
        <p class="clause">
            En caso de surgir dudas sobre los derechos y obligaciones de las partes durante la ejecución del presente
            contrato y
            que no puedan ser solucionadas por la vía de la concertación, las partes están facultadas para acudir a la
            vía judicial o
            autoridad competente.
        </p>
    </div>

    <div class="section">
        <strong>CLÁUSULA NOVENA.- (ACEPTACIÓN Y CONFORMIDAD)</strong>
        <p class="clause">
            En señal de conformidad, para su fiel y estricto cumplimiento, las partes firman el presente Acuerdo en
            cuatro
            ejemplares con un mismo tenor y validez.
        </p>
    </div>

    <div class="text-right section">
        La Paz, {{ $fecha_contrato ?? '20 de Febrero de 2025' }}
    </div>

    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td>
                    _________________________________<br>
                    Dra. Evelin Esther Fortún Fernández<br>
                    <strong>DIRECTORA GENERAL EJECUTIVA</strong><br>
                    <strong>INLASA</strong>
                </td>
                <td>
                    _________________________________<br>
                    {{ $laboratorio->repreleg_lab }}<br>
                    <strong> REPRESENTANTE LEGAL/PROPIETARIO </strong><br>
                    <strong>LABORATORIO CLINICO {{ $laboratorio->nombre_lab }}</strong>
                </td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 50px; font-size: 8px;">
        EEFF/CHC
        </br>
        C.c. Archivo
    </div>

    {{-- 
    <div class="footer">
        <table style="width: 100%;">
            <tr>
                <td style="text-align: left;">
                    <em>Generado por: {{ $generado_por }} el {{ $fecha_generacion }}</em>
                </td>
                <td style="text-align: center;">
                    SigPEEC | INLASA
                </td>
                <td class="text-right">
                    <em>Página {PAGE_NUM} / {PAGE_COUNT}</em>
                </td>
            </tr>
        </table>
    </div> --}}
</body>

</html>
