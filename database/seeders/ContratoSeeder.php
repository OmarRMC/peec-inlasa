<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contrato;
use App\Models\ContratoDetalle;

class ContratoSeeder extends Seeder
{
    public function run()
    {
        // Crear contrato principal
        $contrato = Contrato::create([
            'firmante_nombre'      => 'DRA. EVELIN ESTHER FORTÚN FERNÁNDEZ',
            'firmante_cargo'       => 'DIRECTORA GENERAL EJECUTIVA',
            'firmante_institucion' => 'INLASA',
            'firmante_detalle' => "EEFF/CHC\nC.c. Archivo",
            'estado'               => 1,
        ]);


        $detalles = [
            [
                'titulo' => 'CLÁUSULA PRIMERA.- (ANTECEDENTES)',
                'descripcion' => 'Resolución Ministerial N° 0017 de fecha 20 de enero de 2006, crea y reconoce los Programas de Evaluación Externa de Calidad (PEEC´s) y les asigna la tarea de cumplir con la evaluación externa de desempeño bajo responsabilidad Técnica de los Laboratorios de Referencia Nacional y Departamental en el ámbito de su jurisdicción.
Resolución Administrativa N° 014/2013 de fecha 17 de diciembre de 2013, se reconoce Oficialmente el "Programa de Evaluación Externa de la Calidad en Hematología y Química Sanguínea (PEEC)", del Laboratorios de Análisis Clínico dependiente del Instituto Nacional de Laboratorios de Salud.
Resolución Administrativa N.º 08-A de fecha 16 de abril de 2015, crea el Programa de Evaluación Externa de la Calidad, el cual se encuentra dentro del organigrama interno - INLASA de la Unidad de Diagnostico.
El INLASA en el marco de sus competencias en fecha 07 de octubre de 2025, mediante Resolución Administrativa INLASA/UJ N.º 27/2025, aprueba la "{{ convocatoria }}", del Instituto Nacional de Laboratorios de Salud "Dr. Néstor Morales Villazón" - INLASA de conformidad al documento denominado " Programa de Evaluación Externa de la Calidad - Convocatoria Gestión {{ gestionInscripcion }} " y Anexo 1 - "Oferta de Servicios".',
                'posicion' => 1,
                'estado' => 1
            ],
            [
                'titulo' => 'CLÁUSULA SEGUNDA.- (OBJETO)',
                'descripcion' => 'El presente contrato tiene por objeto establecer las condiciones para la prestación de SERVICIOS PRESTADOS POR EL INLASA PARA LA EVALUACIÓN EXTERNA DE LA CALIDAD y lograr mayor efectividad en las acciones realizadas para el cumplimiento de las condiciones establecidas en la "{{ convocatoria }}".',
                'posicion' => 2,
                'estado' => 1
            ],
            [
                'titulo' => 'CLÁUSULA TERCERA.- (OBLIGACIONES DE INLASA)',
                'descripcion' => 'a) Proporcionar al PARTICIPANTE la cantidad de muestras de control de calidad externas establecida en la Convocatoria de cada programa, de acuerdo con el cronograma definido por el PEEC.
b) Recibir y evaluar los resultados enviados por el PARTICIPANTE proporcionando Informes de Evaluación en los plazos establecidos en el cronograma de actividades.
c) Mantener la confidencialidad de la información del PARTICIPANTE y de los resultados de la evaluación, salvo cuando sea legalmente requerido o cuando el laboratorio decida voluntariamente levantarla.
d) Proporcionar al PARTICIPANTE, cuando lo solicite, asesoramiento técnico sobre los programas en los que está inscrito.
e) Difundir las actividades del PEEC - INLASA en todo el territorio nacional.
f) Extender anualmente al PARTICIPANTE el Certificado Digital de Participación; siempre y cuando el PARTICIPANTE no tenga deudas pendientes con la institución.
g) Extender anualmente al PARTICIPANTE el Certificado Digital de Desempeño; condicionado al cumplimiento de los requisitos mínimos de calificación establecidos por el Programa.
h) Poner a disposición del PARTICIPANTE instrucciones relevantes al Programa y actividades a desarrollarse durante la prestación del servicio.
i) Informar con la debida antelación, de cambios en la estructura que puedan incidir en el flujo de trabajo previamente establecido como formato de formularios de resultados, informe de evaluación, Cronograma u otros que pudieran influir en el servicio.
j) Informar al laboratorio participante cuando se subcontrata el suministro de la muestra de control.',
                'posicion' => 3,
                'estado' => 1
            ],
            [
                'titulo' => 'CLÁUSULA CUARTA.- (OBLIGACIONES DEL LABORATORIO PARTICIPANTE)',
                'descripcion' => 'a) Realizar las pruebas incluidas en los programas contratados con el PEEC - INLASA.
b) Seguir las instrucciones proporcionadas por el PEEC - INLASA para realizar correctamente el análisis de los ítems de ensayo.
c) Enviar los resultados obtenidos al enlace proporcionado según el área que corresponda cumpliendo las fechas establecidas en el Cronograma.
d) Participar de las reuniones de retroalimentación y responder a encuestas de satisfacción realizadas por el PEEC - INLASA para mejorar el Programa.
e) Apelar los resultados de su evaluación, cuando lo considere necesario.
f) Mantener actualizados sus registros de métodos, reactivos y equipos el en reporte de resultados al PEEC - INLASA.
g) Brindar información sobre el PEEC – INLASA a organismos evaluadores cuando se requiera en el marco de procesos oficiales.
h) Cumplir la política de divulgación de informes establecida en los protocolos de cada ensayo de aptitud.
i) Pagar la tasa de inscripción por la participación en el Programa, de conformidad a los plazos establecidos en la  {{ convocatoria }}',
                'posicion' => 4,
                'estado' => 1
            ],
            [
                'titulo' => 'CLÁUSULA QUINTA.- (VIGENCIA Y VALIDEZ)',
                'descripcion' => 'El presente contrato tendrá vigencia desde el 01 de enero al 31 de diciembre de {{ gestionInscripcion }}. No obstante, las obligaciones económicas, administrativas o técnicas derivadas de su ejecución que no se hubieran cumplido dentro del periodo señalado subsistirán hasta su total cumplimiento, manteniendo su validez bajo los términos y condiciones establecidos en el presente documento.
Durante este periodo, cualquier inscripción, acción o acuerdo relacionado con los términos del presente contrato será considerada válida y estará amparada por las disposiciones aquí establecidas. Las partes reconocen que este documento constituye el marco legal que respalda dichas inscripciones.',
                'posicion' => 5,
                'estado' => 1
            ],
            [
                'titulo' => 'CLÁUSULA SEXTA.- (RESOLUCIÓN DE CONTRATO)',
                'descripcion' => 'Este contrato podrá rescindirse por las siguientes causales:
a) Incumplimiento al punto 11. Pago del Servicio de la "{{ convocatoria }}".
b) Incumplimiento en el envío de muestras establecido en el cronograma de actividades por parte de INLASA.
c) Incumplimiento en loestablecido en la "{{ convocatoria }}".

De ocurrir una de las causas anteriormente señaladas, cualquiera de las partes deberá notificar a la otra su intención de resolver el contrato, estableciendo en forma clara y específica la causa en la que se funda.
La Primera notificación de intención de resolución del contrato, deberá ser cursada en un plazo de cinco (5) días hábiles posteriores al hecho generador de la resolución del contrato, especificando la causal de resolución, que deberán ser efectuadas mediante carta dirigida al INLASA o al PARTICIPANTE según corresponda. 
Si la causal argumentada es subsanada, no prosigue la resolución. Empero, si no existe solución a la conclusión en el plazo de diez (10) días hábiles, se debe cursar una segunda carta comunicando que la resolución se ha hecho efectiva. 
Cuando se efectúe la resolución del contrato se procederá a una liquidación de saldos deudores y acreedores de ambas partes, efectuándose los pagos a que hubiere lugar, conforme la evaluación del grado de cumplimiento del PARTICIPANTE.',
                'posicion' => 6,
                'estado' => 1
            ],
            [
                'titulo' => 'CLÁUSULA SÉPTIMA.- (SEGURIDAD Y CONFIDENCIALIDAD DE DATOS)',
                'descripcion' => 'El INLASA se compromete mantener la confidencialidad y seguridad de los datos propios del PARTICIPANTE. Todo el personal del PEEC, asume el compromiso de confidencialidad mediante la firma de un documento interno.
El INLASA establece controles técnicos y administrativos apropiados para garantizar la confidencialidad, integridad y disponibilidad de los Datos Personales sujetos a Tratamiento, además de velar por el cumplimiento de la normativa vigente relacionada a la Protección de Datos y demás normas que versan sobre privacidad y protección de datos personales.
El INLASA, en el marco de sus responsabilidades como proveedor del Ensayo de Aptitud, garantiza la confidencialidad de toda información relativa al PARTICIPANTE o CLIENTE que haya sido obtenida a través de terceros, tales como organismos reguladores o personas externas. En estos casos, se resguardará la identidad de la fuente de información y esta no será revelada sin su autorización expresa.
El PARTICIPANTE asume la responsabilidad de protección de datos y confidencialidad de la información de su cuenta en el SIGPEEC, a la cual tiene acceso mediante un Usuario y Contraseña propio de cada laboratorio.',
                'posicion' => 7,
                'estado' => 1
            ],
            [
                'titulo' => 'CLÁUSULA OCTAVA.- (SOLUCIÓN DE CONTROVERSIAS)',
                'descripcion' => 'En caso de surgir dudas sobre los derechos y obligaciones de las partes durante la ejecución del presente contrato y que no puedan ser solucionadas por la vía de la concertación, las partes están facultadas para acudir a la vía judicial o autoridad competente.',
                'posicion' => 8,
                'estado' => 1
            ],
            [
                'titulo' => 'CLÁUSULA NOVENA.- (ACEPTACIÓN Y CONFORMIDAD)',
                'descripcion' => 'En señal de conformidad, para su fiel y estricto cumplimiento, las partes firman el presente Acuerdo en cuatro ejemplares con un mismo tenor y validez.',
                'posicion' => 9,
                'estado' => 1
            ]
        ];
        foreach ($detalles as $detalle) {
            $contrato->detalles()->create($detalle);
        }
    }
}
