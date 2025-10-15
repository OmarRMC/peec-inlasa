<!DOCTYPE html>
<html lang="es" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <meta content="telephone=no" name="format-detection">
    <title>Verificación de Registro de Laboratorio</title>
    <style type="text/css">
        body {
            width: 100% !important;
            margin: 0;
            padding: 0;
            background-color: #FAFAFA;
            font-family: Arial, Helvetica, sans-serif;
        }

        table {
            border-collapse: collapse;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            color: #333333;
        }

        .header-logo {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .header-logo img {
            display: block;
            border: 0;
        }

        h2,
        h3,
        h5,
        p {
            margin: 0;
        }

        h2 {
            font-size: 26px;
            font-weight: bold;
        }

        h5 {
            font-size: 24px;
            font-weight: normal;
        }

        .data-table {
            width: 100%;
            margin-top: 15px;
        }

        .data-table td {
            padding: 6px 10px;
            vertical-align: top;
        }

        .data-table td.label {
            font-weight: bold;
            width: 40%;
        }

        .data-table td.value {
            width: 60%;
        }

        hr {
            border: 0;
            border-top: 1px solid #ddd;
            margin: 10px 0;
        }

        .btn-confirm {
            display: inline-block;
            background-color: #5C68E2;
            color: #ffffff;
            padding: 10px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 15px;
            margin: 15px 0;
        }

        .footer {
            font-size: 12px;
            line-height: 16px;
            text-align: center;
            padding: 20px 10px;
        }

        .footer a {
            color: #333333;
            text-decoration: none;
        }

        @media screen and (max-width: 480px) {
            .header-logo {
                flex-direction: column;
                text-align: center;
            }

            .data-table td {
                display: block;
                width: 100%;
            }

            .data-table td.label {
                margin-bottom: 4px;
            }
        }
    </style>
</head>

<body>
    <div class="container" style="box-shadow: 0px 0px 2px #b3b3b3">
        <!-- Header -->
        <div class="header-logo">
            <img src="https://elnlheb.stripocdn.email/content/guids/CABINET_184d695e9c5699aadff4ec35803489d8b8bd9fd9912e8388d470049cddb42cb5/images/logo_inlasa.png"
                width="130" alt="Logo INLASA">

            <div>
                <h5>PEEC-INLASA</h5>
                <h2>Verifique sus datos para continuar con el registro</h2>
            </div>
        </div>

        <!-- Saludo -->
        <p style="font-size:16px; line-height:1.6; margin-bottom:10px;">Estimado/a:
            <strong>{{ $laboratorio->repreleg_lab }}</strong>
        </p>
        <p style="font-size:16px; line-height:1.6; margin-bottom:10px;">
            Reciba un cordial saludo.
        </p>
        <p style="font-size:16px; line-height:1.6; margin-bottom:10px;">
            En el marco del proceso de registro al <strong>Programa de Evaluación Externa de la Calidad (PEEC)</strong>,
            le informamos que antes de completar su registro, es necesario confirmar los datos proporcionados en el
            <strong>registro preliminar</strong> de su laboratorio.
        </p>

        <p style="font-size:16px; line-height:1.6; margin-bottom:20px;">
            A continuación, detallamos la información registrada:
        </p>

        <!-- Tabla de datos -->
        <table class="data-table">
            <tr>
                <td class="label">
                    DATO SOLICITADO
                </td>
                <td class="label">
                    REGISTRO
                </td>
            </tr>
            <tr>
                <td class="label">Registro SEDES</td>
                <td class="value">{{ $laboratorio->numsedes_lab ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td colspan="2">
                    <hr>
                </td>
            </tr>

            <tr>
                <td class="label">Nombre del laboratorio</td>
                <td class="value">{{ $laboratorio->nombre_lab }}</td>
            </tr>
            <tr>
                <td colspan="2">
                    <hr>
                </td>
            </tr>

            <tr>
                <td class="label">NIT</td>
                <td class="value">{{ $laboratorio->nit_lab }}</td>
            </tr>
            <tr>
                <td colspan="2">
                    <hr>
                </td>
            </tr>

            <tr>
                <td class="label">Responsable del laboratorio</td>
                <td class="value">
                    {{ $laboratorio->respo_lab }} <br>
                    {{ $laboratorio->ci_respo_lab }}
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <hr>
                </td>
            </tr>

            <tr>
                <td class="label">Representante Legal</td>
                <td class="value">
                    {{ $laboratorio->repreleg_lab }} <br>
                    {{ $laboratorio->ci_repreleg_lab }}
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <hr>
                </td>
            </tr>

            <tr>
                <td class="label">Nivel / Categoría</td>
                <td class="value">{{ $nivel }} / {{ $categoria }}</td>
            </tr>
            <tr>
                <td colspan="2">
                    <hr>
                </td>
            </tr>

            <tr>
                <td class="label">Tipo</td>
                <td class="value">{{ $tipo }}</td>
            </tr>
            <tr>
                <td colspan="2">
                    <hr>
                </td>
            </tr>

            <tr>
                <td class="label">Departamento</td>
                <td class="value">{{ $departamento }}</td>
            </tr>
            <tr>
                <td class="label">Provincia</td>
                <td class="value">{{ $provincia }}</td>
            </tr>
            <tr>
                <td class="label">Municipio</td>
                <td class="value">{{ $municipio }}</td>
            </tr>
            <tr>
                <td colspan="2">
                    <hr>
                </td>
            </tr>

            <tr>
                <td class="label">Dirección</td>
                <td class="value">{{ $laboratorio->direccion_lab_tem }}</td>
            </tr>
            <tr>
                <td colspan="2">
                    <hr>
                </td>
            </tr>

            <tr>
                <td class="label">Teléfono</td>
                <td class="value">{{ $laboratorio->telefono ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Celular (WhatsApp)</td>
                <td class="value">{{ $laboratorio->wapp_lab }}</td>
            </tr>
            <tr>
                <td class="label">Celular adicional</td>
                <td class="value">{{ $laboratorio->wapp2_lab ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td colspan="2">
                    <hr>
                </td>
            </tr>

            <tr>
                <td class="label">Correo principal</td>
                <td class="value">{{ $laboratorio->mail_lab }}</td>
            </tr>
            <tr>
                <td class="label">Correo opcional</td>
                <td class="value">{{ $laboratorio->mail2_lab ?? 'N/A' }}</td>
            </tr>
        </table>

        <!-- Mensaje y botón -->
        <p style="font-size:16px; line-height:1.6; text-align:center; margin-top:20px;">
            Le solicitamos revisar cuidadosamente estos datos y confirmarnos si son correctos o, en caso contrario,
            llene el registro nuevamente.
        </p>

        <p style="font-size:16px; line-height:1.6; text-align:center;">
            Si el registro está correcto, haga click en el botón para que se le asigne un código
            <strong>BOLXXXX</strong> y pueda continuar con su inscripción al <strong>PEEC - INLASA</strong>.
        </p>

        <div style="text-align:center; color: rgb(255, 255, 255); font-weight: bold">
            <a href="{{ route('confirmar.datos.lab', $laboratorio->id) }}" style="color: rgb(255, 255, 255)"
                class="btn-confirm" target="_blank">
                MIS DATOS SON CORRECTOS
            </a>
        </div>

        <!-- Footer -->
        <div
            style="max-width:600px;margin:0 auto;background:#ffffff;padding:15px 20px;font-family:Arial, Helvetica, sans-serif;color:#333333;">

            <!-- Logo y datos -->
            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;">
                <div style="flex:1;min-width:150px;padding-top:5px;">
                    <img src="https://elnlheb.stripocdn.email/content/guids/CABINET_184d695e9c5699aadff4ec35803489d8b8bd9fd9912e8388d470049cddb42cb5/images/logo_inlasa.png"
                        alt="INLASA Logo" width="130"
                        style="display:block;border:0;outline:none;text-decoration:none;">
                </div>
                <!-- Datos de contacto -->
                <div style="flex:1;min-width:250px;font-size:13px;line-height:1.4;">
                    <strong style="font-size:15px;">PEEC - INLASA</strong><br>
                    PROGRAMA DE EVALUACIÓN EXTERNA DE LA CALIDAD<br>
                    <a href="tel:+59165636538" style="color:#333333;text-decoration:none;">+591 65636538</a><br>
                    <a href="mailto:controlexterno.inlasa@gmail.com"
                        style="color:#333333;text-decoration:none;">controlexterno.inlasa@gmail.com</a><br>
                    <span style="display:inline-block;margin-top:6px;">
                        <a href="https://www.facebook.com/share/1G18ZNf6SC/" target="_blank"
                            style="text-decoration:none;margin-right:6px;">
                            <img src="https://elnlheb.stripocdn.email/content/assets/img/social-icons/logo-black/facebook-logo-black.png"
                                alt="Facebook" width="20" height="20"
                                style="display:inline-block;border:0;vertical-align:middle;">
                        </a>
                        <a href="https://youtube.com/@peecinlasa?si=jnwqqukuWtkOo7zM" target="_blank"
                            style="text-decoration:none;">
                            <img src="https://elnlheb.stripocdn.email/content/assets/img/social-icons/logo-black/youtube-logo-black.png"
                                alt="YouTube" width="20" height="20"
                                style="display:inline-block;border:0;vertical-align:middle;">
                        </a>
                    </span>
                </div>

            </div>

            <!-- Separador -->
            <div style="border-top:1px solid #ddd;margin:15px 0;"></div>

            <!-- Derechos reservados -->
            <div style="text-align:center;font-size:12px;line-height:16px;color:#333333;">
                <p style="margin:0 0 6px 0;"><strong>SIGPEEC v.02 © 2025 INLASA.</strong> Todos los derechos
                    reservados.</p>
                <p style="margin:0;">Pasaje Rafael Zubieta #1889 (Lado Estado Mayor del Ejército),<br>Zona Miraflores,
                    La Paz - Bolivia</p>
            </div>
        </div>
    </div>
</body>

</html>
