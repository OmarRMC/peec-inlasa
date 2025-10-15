@php
    // Variables esperadas:
    // $usuario        -> objeto User (usa $usuario->username como CÓDIGO)
    // $lab         -> objeto Lab (opcional)
    // $loginUrl    -> URL de inicio de sesión
    $supportMail = $supportMail ?? 'controlexterno.inlasa@gmail.com';
@endphp
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Bienvenido(a) - PEEC INLASA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        .wrapper {
            width: 100%;
            background: #f3f6f9;
            margin: 0;
            padding: 24px 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #1f2d3d
        }

        .container {
            max-width: 640px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e6ebf1
        }

        .header {
            padding: 20px 24px;
            background: #ffffff;
            border-bottom: 1px solid #eef2f6;
            text-align: center
        }

        .logo {
            height: 56px;
            display: block;
            margin: 0 auto
        }

        .body {
            padding: 24px
        }

        .h1 {
            font-size: 20px;
            margin: 0 0 16px;
            color: #0f3d66
        }

        .p {
            font-size: 14px;
            line-height: 1.6;
            margin: 0 0 12px
        }

        .codeBox {
            background: #f4f8fc;
            border: 1px dashed #bcd2e6;
            border-radius: 6px;
            padding: 14px 16px;
            margin: 12px 0
        }

        .codeLabel {
            font-size: 12px;
            color: #68768a;
            margin: 0 0 6px
        }

        .codeValue {
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 1px;
            color: #0e2f50;
            margin: 0
        }

        .btnWrap {
            margin: 20px 0 10px;
            text-align: center
        }

        .btn {
            display: inline-block;
            background: #2c6ea5;
            color: #fff !important;
            text-decoration: none;
            font-weight: 700;
            border-radius: 6px;
            padding: 12px 20px
        }

        .small {
            font-size: 12px;
            color: #5b6b7c
        }

        .footer {
            padding: 16px 24px;
            background: #ffffff;
            border-top: 1px solid #eef2f6;
            text-align: center
        }

        a {
            color: #2c6ea5
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <img class="logo" alt="PEEC-INLASA"
                    src="https://elnlheb.stripocdn.email/content/guids/CABINET_ceb2eff80ad6e6a463516d1a0654504da724f990220ae94c91ba19fca0e3eba8/images/logoinlasa.jpg">
            </div>

            <div class="body">
                <h1 class="h1">¡Bienvenido(a) al PEEC INLASA!</h1>

                <p class="p">
                    Estimado(a) participante, gracias por su registro en el <strong>PEEC INLASA</strong>.
                    Desde ahora puede ingresar al sistema para completar su inscripción y gestionar sus actividades.
                </p>

                @if (!empty($lab?->nombre_lab))
                    <p class="p">Laboratorio: <strong>{{ $lab->nombre_lab }}</strong></p>
                @endif

                <div class="codeBox">
                    <p class="codeLabel">CÓDIGO ASIGNADO A SU LABORATORIO:</p>
                    <p class="codeValue">CÓDIGO: {{ strtoupper($usuario->username) }}</p>
                </div>

                <p class="p"><strong>Contraseña:</strong> es la que definió al momento del registro.</p>

                <div class="btnWrap">
                    <a class="btn" href="{{ route('login') }}" target="_blank" rel="noopener">INICIAR SESIÓN</a>
                </div>

                <p class="p small">
                    Si el botón no funciona, copie y pegue este enlace en su navegador:<br>
                    <a href="{{ $loginUrl }}" target="_blank" rel="noopener">{{ $loginUrl }}</a>
                </p>

                <p class="p small" style="margin-top:16px;">
                    Este es un mensaje automático. Por favor, no responda a este correo ni envíe documentación por este
                    medio.
                </p>

                <p class="p small">
                    Consultas: <a href="mailto:{{ $supportMail }}">{{ $supportMail }}</a>
                </p>
            </div>

            <div class="footer">
                <p class="small">Programa de Evaluación Externa de la Calidad – INLASA</p>
            </div>
        </div>
    </div>
</body>

</html>
