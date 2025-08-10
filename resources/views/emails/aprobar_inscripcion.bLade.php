{{-- @extends('emails.layout')

@section('content')
    <h1 style="color: #2563EB;">¡Bienvenido a SIGPEEC!</h1>

    <p>Hola <strong>{{ $usuario->nombre }}</strong>,</p>

    <p>Gracias por registrar tu laboratorio <strong>{{ $laboratorio->nombre_lab }}</strong> en SIGPEEC.</p>

    <div>
        Codigo de LAB
        <span>
            {{ $usuario->username }}
        </span>
    </div>
    <p>Para completar el proceso, por favor haz clic en el siguiente botón para verificar tu correo electrónico:</p>

    <p style="text-align: center; margin: 30px 0;">
        <a href="{{ $url_verificacion }}" class="btn">Verificar Correo</a>
    </p>

    <p>Si tú no realizaste esta acción, puedes ignorar este correo.</p>

    <p>Gracias,<br>Equipo SIGPEEC</p>
@endsection --}}
@php
    use App\Models\Configuracion;
@endphp

<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="ltr" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns="http://www.w3.org/1999/xhtml" lang="es">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="telephone=no" name="format-detection">
    <title>Veficacion de correo| Bienvenido</title><!--[if (mso 16)]>
    <style type="text/css">
    a {text-decoration: none;}
    </style>
    <![endif]--><!--[if gte mso 9]><style>sup { font-size: 100% !important; }</style><![endif]--><!--[if gte mso 9]>
<noscript>
         <xml>
           <o:OfficeDocumentSettings>
           <o:AllowPNG></o:AllowPNG>
           <o:PixelsPerInch>96</o:PixelsPerInch>
           </o:OfficeDocumentSettings>
         </xml>
      </noscript>
<![endif]--><!--[if mso]><xml>
    <w:WordDocument xmlns:w="urn:schemas-microsoft-com:office:word">
      <w:DontUseAdvancedTypographyReadingMail></w:DontUseAdvancedTypographyReadingMail>
    </w:WordDocument>
    </xml><![endif]-->
    <style type="text/css">
        .btn {
            display: inline-block;
            padding: 12px 25px;
            background-color: #2563EB;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }

        .rollover:hover .rollover-first {
            max-height: 0px !important;
            display: none !important;
        }

        .rollover:hover .rollover-second {
            max-height: none !important;
            display: block !important;
        }

        .rollover span {
            font-size: 0px;
        }

        u+.body img~div div {
            display: none;
        }

        #outlook a {
            padding: 0;
        }

        span.MsoHyperlink,
        span.MsoHyperlinkFollowed {
            color: inherit;
            mso-style-priority: 99;
        }

        a.a {
            mso-style-priority: 100 !important;
            text-decoration: none !important;
        }

        a[x-apple-data-detectors],
        #MessageViewBody a {
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        .e {
            display: none;
            float: left;
            overflow: hidden;
            width: 0;
            max-height: 0;
            line-height: 0;
            mso-hide: all;
        }

        .r:hover {
            border-color: #1376c8 #1376c8 #1376c8 #1376c8 !important;
            background: #2391ea !important;
        }

        .r:hover a.a,
        .r:hover button.a {
            background: #2391ea !important;
        }

        .r:hover a.a {
            background: #2391ea !important;
        }

        @media only screen and (max-width:600px) {
            .bc {
                padding-bottom: 20px !important
            }

            *[class="gmail-fix"] {
                display: none !important
            }

            p,
            a {
                line-height: 150% !important
            }

            h1,
            h1 a {
                line-height: 120% !important
            }

            h2,
            h2 a {
                line-height: 120% !important
            }

            h3,
            h3 a {
                line-height: 120% !important
            }

            h4,
            h4 a {
                line-height: 120% !important
            }

            h5,
            h5 a {
                line-height: 120% !important
            }

            h6,
            h6 a {
                line-height: 120% !important
            }

            .z p {}

            .x p {}

            h1 {
                font-size: 30px !important;
                text-align: center
            }

            h2 {
                font-size: 26px !important;
                text-align: center
            }

            h3 {
                font-size: 20px !important;
                text-align: center
            }

            h4 {
                font-size: 24px !important;
                text-align: left
            }

            h5 {
                font-size: 20px !important;
                text-align: left
            }

            h6 {
                font-size: 16px !important;
                text-align: left
            }

            .z p,
            .z a {
                font-size: 16px !important
            }

            .x p,
            .x a {
                font-size: 12px !important
            }

            .u,
            .u h1,
            .u h2,
            .u h3,
            .u h4,
            .u h5,
            .u h6 {
                text-align: center !important
            }

            .t img,
            .u img,
            .v img {
                display: inline !important
            }

            .t .rollover:hover .rollover-second,
            .u .rollover:hover .rollover-second,
            .v .rollover:hover .rollover-second {
                display: inline !important
            }

            a.a,
            button.a {
                font-size: 20px !important;
                padding: 10px 20px 10px 20px !important;
                line-height: 120% !important;
                padding-top: 0px !important;
                padding-bottom: 0px !important;
                padding-right: 0px !important;
                padding-left: 0px !important
            }

            a.a,
            button.a,
            .r {
                display: inline-block !important
            }

            .n,
            .n .a,
            .o,
            .o td,
            .c {
                display: inline-block !important
            }

            .k table,
            .l,
            .m {
                width: 100% !important
            }

            .h table,
            .i table,
            .j table,
            .h,
            .j,
            .i {
                width: 100% !important;
                max-width: 600px !important
            }

            .adapt-img {
                width: 100% !important;
                height: auto !important
            }

            table.b,
            .esd-block-html table {
                width: auto !important
            }

            .h-auto {
                height: auto !important
            }

            a.a {
                border-left-width: 0px !important;
                border-right-width: 0px !important
            }
        }

        @media screen and (max-width:384px) {
            .mail-message-content {
                width: 414px !important
            }
        }
    </style>
</head>

<body class="body"
    style="width:100%;height:100%;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;padding:0;Margin:0">
    <div dir="ltr" class="es-wrapper-color" lang="es" style="background-color:#F6F6F6"><!--[if gte mso 9]>
   <v:background xmlns:v="urn:schemas-microsoft-com:vml" fill="t">
    <v:fill type="tile" color="#f6f6f6"></v:fill>
   </v:background>
  <![endif]-->
        <table width="100%" cellspacing="0" cellpadding="0" class="es-wrapper" role="none"
            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;padding:0;Margin:0;width:100%;height:100%;background-repeat:repeat;background-position:center top;background-color:#F6F6F6">
            <tr style="border-collapse:collapse">
                <td valign="top" style="padding:0;Margin:0">
                    <table cellspacing="0" cellpadding="0" align="center" class="h" role="none"
                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:100%;table-layout:fixed !important">
                        <tr style="border-collapse:collapse">
                            <td align="center" style="padding:0;Margin:0">
                                <table bgcolor="transparent" align="center" cellspacing="0" cellpadding="0"
                                    class="z"
                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:transparent;width:600px"
                                    role="none">
                                    <tr style="border-collapse:collapse">
                                        <td align="left" data-custom-paddings="true" style="padding:10px;Margin:0">
                                            <table width="100%" cellspacing="0" cellpadding="0" role="none"
                                                style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                <tr style="border-collapse:collapse">
                                                    <td valign="top" align="center"
                                                        style="padding:0;Margin:0;width:580px">
                                                        <table width="100%" cellspacing="0" cellpadding="0"
                                                            role="presentation"
                                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                            <tr style="border-collapse:collapse">
                                                                <td align="center" class="x"
                                                                    style="padding:0;Margin:0">
                                                                    <p
                                                                        style="Margin:0;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:18px;letter-spacing:0;color:#CCCCCC;font-size:12px">
                                                                        Put your preheader text here</p>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <table cellspacing="0" cellpadding="0" align="center" class="i" role="none"
                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:100%;table-layout:fixed !important;background-color:transparent;background-repeat:repeat;background-position:center top">
                        <tr style="border-collapse:collapse">
                            <td align="center" style="padding:0;Margin:0">
                                <table cellpadding="0" bgcolor="#040404" align="center"
                                    background="https://elnlheb.stripocdn.email/content/guids/CABINET_ceb2eff80ad6e6a463516d1a0654504da724f990220ae94c91ba19fca0e3eba8/images/2.png"
                                    cellspacing="0" class="ba"
                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#040404;border-top:4px solid #ffffff;background-repeat:no-repeat;border-right:4px solid #ffffff;border-left:4px solid #ffffff;width:600px;background-image:url(https://elnlheb.stripocdn.email/content/guids/CABINET_ceb2eff80ad6e6a463516d1a0654504da724f990220ae94c91ba19fca0e3eba8/images/2.png);background-position:30% 40%;border-bottom:4px solid #ffffff"
                                    role="none">
                                    <tr style="border-collapse:collapse">
                                        <td align="left" data-custom-paddings="true"
                                            style="Margin:0;padding-top:40px;padding-right:20px;padding-bottom:40px;padding-left:20px">
                                            <table width="100%" cellspacing="0" cellpadding="0" role="none"
                                                style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                <tr style="border-collapse:collapse">
                                                    <td valign="top" align="center"
                                                        style="padding:0;Margin:0;width:552px">
                                                        <table width="100%" cellspacing="0" cellpadding="0"
                                                            role="presentation"
                                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                            <tr style="border-collapse:collapse">
                                                                <td align="left"
                                                                    style="padding:0;Margin:0;padding-top:40px;padding-bottom:10px">
                                                                    <h2
                                                                        style="Margin:0;font-family:tahoma, verdana, segoe, sans-serif;mso-line-height-rule:exactly;letter-spacing:0;font-size:38px;font-style:normal;font-weight:bold;line-height:45.6px;color:#ffffff">
                                                                        BIENVENIDO AL</h2>
                                                                    <h2
                                                                        style="Margin:0;font-family:tahoma, verdana, segoe, sans-serif;mso-line-height-rule:exactly;letter-spacing:0;font-size:38px;font-style:normal;font-weight:bold;line-height:45.6px;color:#ffffff">
                                                                        PEEC- INLASA</h2>
                                                                </td>
                                                            </tr>
                                                            <tr style="border-collapse:collapse">
                                                                <td align="left"
                                                                    style="padding:0;Margin:0;padding-bottom:40px">
                                                                    <h3
                                                                        style="Margin:0;font-family:tahoma, verdana, segoe, sans-serif;mso-line-height-rule:exactly;letter-spacing:0;font-size:20px;font-style:normal;font-weight:normal;line-height:24px;color:#ffffff">
                                                                        Programa de Evaluación Externa de la Calidad
                                                                        (PEEC INLASA) Gestión {{ now()->year }}.</h3>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <table cellspacing="0" cellpadding="0" align="center" class="h" role="none"
                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:100%;table-layout:fixed !important">
                        <tr style="border-collapse:collapse">
                            <td align="center" style="padding:0;Margin:0">
                                <table cellpadding="0" bgcolor="#ffffff" align="center" cellspacing="0"
                                    class="z" role="none"
                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px">
                                    <tr style="border-collapse:collapse">
                                        <td align="left"
                                            style="padding:0;Margin:0;padding-right:20px;padding-left:20px;padding-top:20px;background-position:center center">
                                            <!--[if mso]><table style="width:560px" cellpadding="0" cellspacing="0"><tr><td style="width:345px" valign="top"><![endif]-->
                                            <table cellpadding="0" align="left" cellspacing="0" class="l"
                                                role="none"
                                                style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left">
                                                <tr style="border-collapse:collapse">
                                                    <td align="left" class="bc"
                                                        style="padding:0;Margin:0;width:345px">
                                                        <table width="100%" cellspacing="0" cellpadding="0"
                                                            role="presentation"
                                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                            <tr style="border-collapse:collapse">
                                                                <td align="left"
                                                                    style="padding:0;Margin:0;padding-top:10px">
                                                                    <p
                                                                        style="Margin:0;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;letter-spacing:0;color:#333333;font-size:14px">
                                                                        Nos alegra informarle que su laboratorio ha
                                                                        completado con éxito el proceso de
                                                                        inscripción, y su participación ha sido
                                                                        <b>aceptada</b>. A partir de este momento,
                                                                        podrá verificar en la plataforma <a
                                                                            {{-- href="https://www.vigilancia.inlasa.gob.bo/sigpeec/vistas/login.html" --}}
                                                                            href="{{ url('/') }}"
                                                                            style="mso-line-height-rule:exactly;text-decoration:underline;color:#1376C8;font-size:14px">SIGPEEC</a>
                                                                        que el estado de su inscripción ha cambiado a
                                                                        <b>VIGENTE</b>.
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                            <!--[if mso]></td><td style="width:10px"></td><td style="width:205px" valign="top"><![endif]-->
                                            <table cellspacing="0" cellpadding="0" align="right" class="m"
                                                role="none"
                                                style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right">
                                                <tr style="border-collapse:collapse">
                                                    <td align="left" style="padding:0;Margin:0;width:205px">
                                                        <table width="100%" cellspacing="0" cellpadding="0"
                                                            role="presentation"
                                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                            <tr style="border-collapse:collapse">
                                                                <td align="center"
                                                                    style="padding:0;Margin:0;font-size:0px"><a
                                                                        target="_blank"
                                                                        href="https://www.vigilancia.inlasa.gob.bo/sigpeec/vistas/login.html"
                                                                        style="mso-line-height-rule:exactly;text-decoration:underline;color:#1376C8;font-size:14px"><img
                                                                            alt="" width="205"
                                                                            src="https://elnlheb.stripocdn.email/content/guids/CABINET_ceb2eff80ad6e6a463516d1a0654504da724f990220ae94c91ba19fca0e3eba8/images/logoinlasa.jpg"
                                                                            class="adapt-img"
                                                                            style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></a>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table><!--[if mso]></td></tr></table><![endif]-->
                                        </td>
                                    </tr>
                                    <tr style="border-collapse:collapse">
                                        <td align="left"
                                            style="padding:0;Margin:0;padding-right:20px;padding-left:20px;padding-top:20px">
                                            <table cellpadding="0" width="100%" cellspacing="0" role="none"
                                                style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                <tr style="border-collapse:collapse">
                                                    <td valign="top" align="center"
                                                        style="padding:0;Margin:0;width:560px">
                                                        <table width="100%" cellspacing="0" cellpadding="0"
                                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:separate;border-spacing:0px;border-radius:4px"
                                                            role="presentation">
                                                            <tr style="border-collapse:collapse">
                                                                <td align="left" style="padding:0;Margin:0">
                                                                    <p
                                                                        style="Margin:0;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;letter-spacing:0;color:#333333;font-size:14px">
                                                                        El código asignado a su laboratorio es el
                                                                        siguiente:</p>
                                                                </td>
                                                            </tr>
                                                            <tr style="border-collapse:collapse">
                                                                <td align="left"
                                                                    style="padding:0;Margin:0;padding-top:10px">
                                                                    <h2
                                                                        style="Margin:0;font-family:arial, 'helvetica neue', helvetica, sans-serif;mso-line-height-rule:exactly;letter-spacing:0;font-size:24px;font-style:normal;font-weight:bold;line-height:28.8px;color:#0b5394">
                                                                        CÓDIGO:&nbsp; {{ $usuario->username }}</h2>
                                                                    <h2
                                                                        style="Margin:0;font-family:arial, 'helvetica neue', helvetica, sans-serif;mso-line-height-rule:exactly;letter-spacing:0;font-size:24px;font-style:normal;font-weight:bold;line-height:28.8px;color:#0b5394">
                                                                        LABORATORIO: {{ $laboratorio->nombre_lab }}</h2>
                                                                    <h2
                                                                        style="Margin:0;font-family:arial, 'helvetica neue', helvetica, sans-serif;mso-line-height-rule:exactly;letter-spacing:0;font-size:26px;font-style:normal;font-weight:bold;line-height:31.2px;color:#0b5394">
                                                                        <br>
                                                                    </h2>
                                                                </td>
                                                            </tr>
                                                            <tr style="border-collapse:collapse">
                                                                <td height="39" align="left" bgcolor="#0b5394"
                                                                    valign="middle" class="h-auto"
                                                                    style="padding:5px;Margin:0">
                                                                    <h1
                                                                        style="Margin:0;font-family:tahoma, verdana, segoe, sans-serif;mso-line-height-rule:exactly;letter-spacing:0;font-size:24px;font-style:normal;font-weight:normal;line-height:28.8px;color:#ffffff">
                                                                        <b>INFORMACIÓN</b>
                                                                    </h1>
                                                                </td>
                                                            </tr>
                                                            <tr style="border-collapse:collapse">
                                                                <td align="center" style="padding:0;Margin:0">
                                                                    <p
                                                                        style="Margin:0;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:14.4px;letter-spacing:0;color:#333333;font-size:12px">
                                                                        <br>
                                                                    </p>
                                                                    <p
                                                                        style="Margin:0;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:14.4px;letter-spacing:0;color:#333333;font-size:12px">
                                                                        Agradecemos la confianza depositada en el
                                                                        <b>PEEC INLASA</b>, para nosotros es un
                                                                        placer contar con su participación y lo
                                                                        invitamos a ser parte de este proceso de
                                                                        aprendizaje mutuo, que fortalece la calidad de
                                                                        los laboratorios clínicos en Bolivia.
                                                                    </p>
                                                                    <p
                                                                        style="Margin:0;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:14.4px;letter-spacing:0;color:#333333;font-size:12px">
                                                                        <br>
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                            <tr style="border-collapse:collapse">
                                                                <td align="left" style="padding:0;Margin:0">
                                                                    <p
                                                                        style="Margin:0;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;letter-spacing:0;color:#333333;font-size:14px">
                                                                        <br>
                                                                    </p>
                                                                    <p
                                                                        style="Margin:0;mso-line-height-rule:exactly;font-family:tahoma, verdana, segoe, sans-serif;line-height:18px;letter-spacing:0;color:#333333;font-size:12px;text-align:center">
                                                                        Haga click en el boton y encontrará la
                                                                        información para el inicio del PEEC INLASA
                                                                        2025 como:</p>
                                                                    <p
                                                                        style="Margin:0;mso-line-height-rule:exactly;font-family:-apple-system, blinkmacsystemfont, 'segoe ui', roboto, helvetica, arial, sans-serif, 'apple color emoji', 'segoe ui emoji', 'segoe ui symbol';line-height:21px;letter-spacing:0;color:#333333;font-size:14px;text-align:center">
                                                                        <strong>Información de pago</strong>
                                                                    </p>
                                                                    <p
                                                                        style="Margin:0;mso-line-height-rule:exactly;font-family:-apple-system, blinkmacsystemfont, 'segoe ui', roboto, helvetica, arial, sans-serif, 'apple color emoji', 'segoe ui emoji', 'segoe ui symbol';line-height:21px;letter-spacing:0;color:#333333;font-size:14px;text-align:center">
                                                                        <strong>Convocatoria&nbsp;</strong>
                                                                    </p>
                                                                    <p
                                                                        style="Margin:0;mso-line-height-rule:exactly;font-family:-apple-system, blinkmacsystemfont, 'segoe ui', roboto, helvetica, arial, sans-serif, 'apple color emoji', 'segoe ui emoji', 'segoe ui symbol';line-height:21px;letter-spacing:0;color:#333333;font-size:14px;text-align:center">
                                                                        <strong>Video de inicio de gestión</strong>
                                                                    </p>
                                                                    <p
                                                                        style="Margin:0;mso-line-height-rule:exactly;font-family:-apple-system, blinkmacsystemfont, 'segoe ui', roboto, helvetica, arial, sans-serif, 'apple color emoji', 'segoe ui emoji', 'segoe ui symbol';line-height:21px;letter-spacing:0;color:#333333;font-size:14px;text-align:center">
                                                                        <strong>Cronograma</strong>
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr style="border-collapse:collapse">
                                        <td align="left"
                                            style="Margin:0;padding-right:20px;padding-left:20px;padding-top:20px;padding-bottom:0px">
                                            <table cellpadding="0" cellspacing="0" width="100%" role="none"
                                                style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                <tr style="border-collapse:collapse">
                                                    <td align="left" style="padding:0;Margin:0;width:560px">
                                                        <table cellpadding="0" cellspacing="0" width="100%"
                                                            role="presentation"
                                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                            <tr style="border-collapse:collapse">
                                                                <td align="center" style="padding:0;Margin:0"><span
                                                                        class="r"
                                                                        style="border-style:solid;border-color:#1376C8;background:#1376C8;border-width:0px;display:inline-block;border-radius:3px;width:auto"><a
                                                                            href="https://drive.google.com/drive/folders/1L0bXKo45Caj6iaXe7FgSl_ycYIrr7Fjf"
                                                                            target="_blank" class="a"
                                                                            style="mso-style-priority:100 !important;text-decoration:none !important;mso-line-height-rule:exactly;color:#FFFFFF;font-size:18px;padding:10px 20px 10px 20px;display:inline-block;background:#1376C8;border-radius:3px;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-weight:normal;font-style:normal;line-height:21.6px;width:auto;text-align:center;letter-spacing:0;mso-padding-alt:0;mso-border-alt:10px solid #1376C8">
                                                                            GESTION {{ $gestion }}
                                                                        </a></span></td>
                                                            </tr>
                                                            <tr style="border-collapse:collapse">
                                                                <td align="left" style="padding:0;Margin:0">
                                                                    <p
                                                                        style="Margin:0;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:16.5px;letter-spacing:0;color:#333333;font-size:11px;text-align:center">
                                                                        Si esto no funciona, copia el siguiente link y
                                                                        pégalo en un navegador.
                                                                        https://drive.google.com/drive/folders/1L0bXKo45Caj6iaXe7FgSl_ycYIrr7Fjf
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td align="left" style="padding:0;Margin:0">
                                                                    {!! configuracion(Configuracion::EMAIL_INFORMACION) !!}
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr style="border-collapse:collapse">
                                        <td align="left"
                                            style="padding:0;Margin:0;padding-right:20px;padding-left:20px;padding-top:20px">
                                            <table width="100%" cellspacing="0" cellpadding="0" role="none"
                                                style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                <tr style="border-collapse:collapse">
                                                    <td valign="top" align="center"
                                                        style="padding:0;Margin:0;width:560px">
                                                        <table width="100%" cellspacing="0" cellpadding="0"
                                                            role="presentation"
                                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                            <tr style="border-collapse:collapse">
                                                                <td align="center"
                                                                    style="padding:0;Margin:0;padding-top:10px">
                                                                    <h2
                                                                        style="Margin:0;font-family:arial, 'helvetica neue', helvetica, sans-serif;mso-line-height-rule:exactly;letter-spacing:0;font-size:25px;font-style:normal;font-weight:bold;line-height:30px;color:#333333;text-align:center">
                                                                        CONTACTOS</h2>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <table cellspacing="0" cellpadding="0" align="center" class="h" role="none"
                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:100%;table-layout:fixed !important">
                        <tr style="border-collapse:collapse">
                            <td align="center" style="padding:0;Margin:0">
                                <table cellspacing="0" cellpadding="0" bgcolor="#ffffff" align="center"
                                    class="z" role="none"
                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px">
                                    <tr style="border-collapse:collapse">
                                        <td align="left" data-custom-paddings="true" style="padding:20px;Margin:0">
                                            <!--[if mso]><table style="width:560px" cellpadding="0" cellspacing="0"><tr><td style="width:356px" valign="top"><![endif]-->
                                            <table cellpadding="0" align="left" cellspacing="0" class="l"
                                                role="none"
                                                style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left">
                                                <tr style="border-collapse:collapse">
                                                    <td align="left" class="bc"
                                                        style="padding:0;Margin:0;width:356px">
                                                        <table width="100%" cellspacing="0" cellpadding="0"
                                                            role="presentation"
                                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                            <tr style="border-collapse:collapse">
                                                                <td align="left" bgcolor="#efefef"
                                                                    style="padding:0;Margin:0">
                                                                    <p class="u"
                                                                        style="Margin:0;mso-line-height-rule:exactly;font-family:helvetica, 'helvetica neue', arial, verdana, sans-serif;line-height:21px;letter-spacing:0;color:#0b5394;font-size:14px">
                                                                        Pasaje Rafael Zubieta 1889 <a
                                                                            href="https://www.google.com/search?sca_esv=757125cedafc38ea&rlz=1C1GCEU_esBO1119BO1119&sxsrf=ADLYWIKqHGFAphhbu0TFG9RDD-u9y8-2rA:1733419056106&q=Av+Saavedra+2346&ludocid=278277793522613037&lsig=AB86z5UKQaG_wslRRInrs0it0bS2&sa=X&sqi=2&ved=2ahUKEwiO79eYkZGKAxWOALkGHeUEMWoQ8G0oAHoECC8QAQ"
                                                                            style="mso-line-height-rule:exactly;text-decoration:underline;color:#1376C8;font-size:14px;font-family:helvetica, 'helvetica neue', arial, verdana, sans-serif">Av
                                                                            Saavedra 2346</a>, La Paz</p>
                                                                    <p class="u"
                                                                        style="Margin:0;mso-line-height-rule:exactly;font-family:helvetica, 'helvetica neue', arial, verdana, sans-serif;line-height:21px;letter-spacing:0;color:#0b5394;font-size:14px">
                                                                        Horario de atención: Lunes a Viernes de
                                                                        &nbsp;8:00 a 14:00</p>
                                                                    <p class="u"
                                                                        style="Margin:0;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;letter-spacing:0;color:#0b5394;font-size:14px">
                                                                        <br>
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                            <!--[if mso]></td><td style="width:20px"></td><td style="width:184px" valign="top"><![endif]-->
                                            <table align="right" cellspacing="0" cellpadding="0" role="none"
                                                style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                <tr style="border-collapse:collapse">
                                                    <td align="left" style="padding:0;Margin:0;width:184px">
                                                        <table width="100%" cellspacing="0" cellpadding="0"
                                                            role="presentation"
                                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                            <tr style="border-collapse:collapse">
                                                                <td align="left" bgcolor="#efefef"
                                                                    style="padding:0;Margin:0;padding-bottom:10px">
                                                                    <p class="u"
                                                                        style="Margin:0;mso-line-height-rule:exactly;font-family:helvetica, 'helvetica neue', arial, verdana, sans-serif;line-height:21px;letter-spacing:0;color:#0b5394;font-size:14px">
                                                                        Celular: 65636538</p>
                                                                    <p class="u"
                                                                        style="Margin:0;mso-line-height-rule:exactly;font-family:helvetica, 'helvetica neue', arial, verdana, sans-serif;line-height:21px;letter-spacing:0;color:#0b5394;font-size:14px">
                                                                        Teléfono: <a
                                                                            href="https://www.google.com/search?q=INLASA&rlz=1C1GCEU_esBO1119BO1119&oq=INLASA&gs_lcrp=EgZjaHJvbWUyEQgAEEUYJxg5GOMCGIAEGIoFMgYIARBFGDwyEggCEC4YJxivARjHARiABBiKBTIGCAMQRRg8MgYIBBBFGDwyBggFEEUYPDIGCAYQRRg8MgYIBxBFGD3SAQgxOTM1ajBqN6gCALACAA&sourceid=chrome&ie=UTF-8#"
                                                                            style="mso-line-height-rule:exactly;text-decoration:underline;color:#1376C8;font-size:14px;font-family:helvetica, 'helvetica neue', arial, verdana, sans-serif">2
                                                                            2226670</a> int 2863</p>
                                                                </td>
                                                            </tr>
                                                            <tr style="border-collapse:collapse">
                                                                <td align="left" class="u"
                                                                    style="padding:0;Margin:0;padding-top:5px;font-size:0">
                                                                    <table cellspacing="0" cellpadding="0"
                                                                        class="b o" role="presentation"
                                                                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                                        <tr style="border-collapse:collapse">
                                                                            <td valign="top" align="center"
                                                                                style="padding:0;Margin:0;padding-right:10px">
                                                                                <a href="https://viewstripo.email/"
                                                                                    style="mso-line-height-rule:exactly;text-decoration:underline;color:#1376C8;font-size:14px"><img
                                                                                        src="https://elnlheb.stripocdn.email/content/assets/img/social-icons/logo-gray/x-logo-gray.png"
                                                                                        alt="X" width="24"
                                                                                        height="24" title="X"
                                                                                        style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></a>
                                                                            </td>
                                                                            <td valign="top" align="center"
                                                                                style="padding:0;Margin:0;padding-right:10px">
                                                                                <a href="https://viewstripo.email/"
                                                                                    style="mso-line-height-rule:exactly;text-decoration:underline;color:#1376C8;font-size:14px"><img
                                                                                        height="24"
                                                                                        title="Facebook"
                                                                                        src="https://elnlheb.stripocdn.email/content/assets/img/social-icons/logo-gray/facebook-logo-gray.png"
                                                                                        alt="Fb" width="24"
                                                                                        style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></a>
                                                                            </td>
                                                                            <td valign="top" align="center"
                                                                                style="padding:0;Margin:0;padding-right:10px">
                                                                                <a href="https://viewstripo.email/"
                                                                                    style="mso-line-height-rule:exactly;text-decoration:underline;color:#1376C8;font-size:14px"><img
                                                                                        alt="Yt" width="24"
                                                                                        height="24" title="Youtube"
                                                                                        src="https://elnlheb.stripocdn.email/content/assets/img/social-icons/logo-gray/youtube-logo-gray.png"
                                                                                        style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></a>
                                                                            </td>
                                                                            <td valign="top" align="center"
                                                                                style="padding:0;Margin:0"><a
                                                                                    href="https://viewstripo.email/"
                                                                                    style="mso-line-height-rule:exactly;text-decoration:underline;color:#1376C8;font-size:14px"><img
                                                                                        width="24" height="24"
                                                                                        title="Vkontakte"
                                                                                        src="https://elnlheb.stripocdn.email/content/assets/img/social-icons/logo-gray/vk-logo-gray.png"
                                                                                        alt="Vk"
                                                                                        style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></a>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table><!--[if mso]></td></tr></table><![endif]-->
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <table cellspacing="0" cellpadding="0" align="center" class="h" role="none"
                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:100%;table-layout:fixed !important">
                        <tr style="border-collapse:collapse">
                            <td align="center" style="padding:0;Margin:0">
                                <table cellspacing="0" cellpadding="0" align="center" class="z"
                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:transparent;width:600px"
                                    role="none">
                                    <tr style="border-collapse:collapse">
                                        <td align="left" data-custom-paddings="true"
                                            style="Margin:0;padding-right:20px;padding-left:20px;padding-top:30px;padding-bottom:30px">
                                            <table width="100%" cellspacing="0" cellpadding="0" role="none"
                                                style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                <tr style="border-collapse:collapse">
                                                    <td valign="top" align="center"
                                                        style="padding:0;Margin:0;width:560px">
                                                        <table cellpadding="0" width="100%" cellspacing="0"
                                                            role="presentation"
                                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                            <tr style="border-collapse:collapse">
                                                                <td align="center" class="x made_with"
                                                                    style="padding:0;Margin:0;font-size:0"><a
                                                                        target="_blank"
                                                                        href="https://viewstripo.email/?utm_source=templates&utm_medium=email&utm_campaign=photography2&utm_content=amp_image_lightbox"
                                                                        style="mso-line-height-rule:exactly;text-decoration:underline;color:#CCCCCC;font-size:12px"><img
                                                                            src="https://elnlheb.stripocdn.email/content/guids/CABINET_ceb2eff80ad6e6a463516d1a0654504da724f990220ae94c91ba19fca0e3eba8/images/image.png"
                                                                            alt="" width="560"
                                                                            class="adapt-img"
                                                                            style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></a>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
