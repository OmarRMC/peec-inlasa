@php
    // Variables esperadas:
    // $usuario      -> objeto User (nombre, username)
    // $laboratorio  -> objeto Laboratorio (opcional)
    // $url          -> enlace de restablecimiento
    $supportMail = $supportMail ?? 'controlexterno.inlasa@gmail.com';
@endphp
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Recuperación de contraseña - PEEC INLASA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        /* Estilos compatibles con clientes de correo */
        .wrapper {
            width: 100%;
            background: #f3f6f9;
            margin: 0;
            padding: 24px 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #1f2d3d
        }

        .card {
            max-width: 640px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #e6ebf1;
            border-radius: 8px;
            overflow: hidden
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
            margin: 0 auto 6px
        }

        .title {
            font-size: 20px;
            margin: 8px 0 0;
            color: #0f3d66;
            border-top: 3px solid #2c6ea5;
            padding-top: 10px
        }

        .body {
            padding: 24px
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
            padding: 12px 14px;
            margin: 8px 0 16px
        }

        .codeLabel {
            font-size: 12px;
            color: #68768a;
            margin: 0 0 6px
        }

        .codeValue {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 0.5px;
            color: #0e2f50;
            margin: 0
        }

        .btnWrap {
            text-align: center;
            margin: 20px 0
        }

        .btn {
            display: inline-block;
            background: #2c6ea5;
            color: #fff !important;
            text-decoration: none;
            font-weight: 700;
            border-radius: 6px;
            padding: 14px 22px
        }

        .alert {
            font-size: 12px;
            color: #c1121f;
            font-weight: 700;
            margin: 16px 0
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
        <div class="card">
            <div class="header">
                <img class="logo" alt="PEEC-INLASA"
                    src="https://elnlheb.stripocdn.email/content/guids/CABINET_ceb2eff80ad6e6a463516d1a0654504da724f990220ae94c91ba19fca0e3eba8/images/logoinlasa.jpg">
                <h1 class="title">¡Recuperación de contraseña PEEC INLASA!</h1>
            </div>

            <div class="body">
                <p class="p">
                    Estimado(a) {{ $usuario->nombre ?? 'participante' }}, usted solicitó restablecer su contraseña para
                    el
                    <strong>Sistema del PEEC INLASA</strong>. Para hacerlo, haga clic en el siguiente botón.
                </p>

                @if (!empty($laboratorio))
                    <div class="codeBox">
                        <p class="codeLabel">CÓDIGO DE LAB:</p>
                        <p class="codeValue">{{ strtoupper($usuario->username) }}</p>
                    </div>
                @endif

                <div class="btnWrap">
                    <a class="btn" href="{{ $url }}" target="_blank" rel="noopener">RESTABLECER
                        CONTRASEÑA</a>
                </div>

                <p class="p small">Si el botón no funciona, copie y pegue este enlace en su navegador:<br>
                    <a href="{{ $url }}" target="_blank" rel="noopener">{{ $url }}</a>
                </p>

                <p class="p small">Este enlace expirará en <strong>60 minutos</strong>.</p>

                <p class="alert">
                    RECORDAMOS QUE ESTE ES UN MENSAJE AUTOMÁTICO, POR FAVOR NO RESPONDA A ESTE CORREO
                    Y NO REMITA DOCUMENTACIÓN, RESULTADOS O CONSULTAS, POR ESTE MEDIO.
                </p>

                <p class="p small">
                    Para cualquier consulta puede comunicarse al correo habilitado del PEEC-INLASA:
                    <a href="mailto:{{ $supportMail }}">{{ $supportMail }}</a>
                </p>
            </div>

            <div class="footer">
                <p class="small">Programa de Evaluación Externa de la Calidad - INLASA</p>
            </div>
        </div>
    </div>
</body>

</html>
