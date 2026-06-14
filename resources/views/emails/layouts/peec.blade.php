<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('titulo', 'PEEC - INLASA')</title>
</head>

<body style="margin:0;padding:0;background-color:#f1f4f8;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
        style="background-color:#f1f4f8;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0"
                    style="width:600px;max-width:600px;background-color:#ffffff;border-radius:8px;overflow:hidden;border:1px solid #e5e9f0;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;">

                    {{-- Header con logo PEEC embebido --}}
                    <tr>
                        <td align="center" style="padding:28px 24px 12px;">
                            @isset($message)
                                <img src="{{ $message->embed(public_path('img/logoinlasa.png')) }}"
                                    alt="PEEC - INLASA" width="84"
                                    style="display:block;border:0;outline:none;text-decoration:none;width:84px;height:auto;">
                            @endisset
                        </td>
                    </tr>

                    {{-- Título --}}
                    @hasSection('titulo')
                        <tr>
                            <td align="center" style="padding:0 24px;">
                                <h1
                                    style="margin:8px 0 0;color:#0b5394;font-size:20px;font-weight:bold;line-height:26px;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;">
                                    @yield('titulo')
                                </h1>
                            </td>
                        </tr>
                    @endif

                    {{-- Separador --}}
                    <tr>
                        <td style="padding:16px 24px 0;">
                            <div style="height:3px;background-color:#0b5394;border-radius:2px;width:48px;margin:0 auto;"></div>
                        </td>
                    </tr>

                    {{-- Cuerpo --}}
                    <tr>
                        <td style="padding:20px 28px 28px;color:#333333;font-size:14px;line-height:21px;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;">
                            @yield('content')
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color:#0b5394;padding:18px 28px;">
                            <p style="margin:0;color:#ffffff;font-size:13px;font-weight:bold;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;">
                                PEEC - INLASA
                            </p>
                            <p style="margin:4px 0 0;color:#cfe0f3;font-size:11px;line-height:16px;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;">
                                Programa de Evaluación Externa de la Calidad<br>
                                Pasaje Rafael Zubieta Nº 1889 (Lado Hospital del Niño), Miraflores, La Paz
                            </p>
                        </td>
                    </tr>
                </table>

                <p style="margin:14px 0 0;color:#9aa5b1;font-size:11px;text-align:center;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;">
                    Este es un mensaje automático, por favor no responda a este correo.<br>
                    © {{ date('Y') }} PEEC - INLASA. Todos los derechos reservados.
                </p>
            </td>
        </tr>
    </table>
</body>

</html>
