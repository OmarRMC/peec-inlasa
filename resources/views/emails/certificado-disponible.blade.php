@extends('emails.layouts.peec')

@section('titulo', 'Certificado disponible')

@section('content')
    <p style="margin:0 0 14px;">Estimado laboratorio <strong>{{ $nombreLab }}</strong>,</p>

    <p style="margin:0 0 16px;">
        Nos complace informarle que su
        <strong>certificado{{ $gestion ? ' de la gestión ' . $gestion : '' }}</strong>
        ya se encuentra <strong>disponible</strong> en el sistema. Puede ingresar para visualizarlo
        y descargarlo.
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 auto 8px;">
        <tr>
            <td align="center" style="border-radius:4px;background-color:#16a34a;">
                <a href="{{ $url }}" target="_blank"
                    style="display:inline-block;padding:11px 26px;color:#ffffff;font-size:15px;text-decoration:none;border-radius:4px;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;">
                    Ver mis certificados
                </a>
            </td>
        </tr>
    </table>
@endsection
