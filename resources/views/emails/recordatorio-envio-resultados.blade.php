@php
    $esFinal = ($fase ?? 'apertura') === 'final';
@endphp

@extends('emails.layouts.peec')

@section('titulo', $esFinal ? 'Recordatorio final de envío de resultados' : 'Envío de resultados habilitado')

@section('content')
    <p style="margin:0 0 14px;">Estimado laboratorio <strong>{{ $nombreLab }}</strong>,</p>

    @if ($esFinal)
        <p style="margin:0 0 16px;">
            Le recordamos que el plazo para el <strong>envío de resultados</strong> está próximo a
            finalizar. Por favor registre sus resultados antes de la fecha límite para no quedar fuera
            del proceso de evaluación.
        </p>
    @else
        <p style="margin:0 0 16px;">
            Le informamos que ya se encuentra <strong>habilitado el envío de resultados</strong> para el
            ensayo de aptitud en el que su laboratorio está inscrito. Le invitamos a registrar sus
            resultados dentro del plazo establecido.
        </p>
    @endif

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
        style="background-color:#f3f6fb;border-radius:6px;margin:0 0 20px;">
        <tr>
            <td style="padding:14px 16px;font-size:14px;color:#333333;line-height:21px;">
                @if (!empty($ensayo))
                    <p style="margin:0 0 6px;"><strong>Ensayo de aptitud:</strong> {{ $ensayo }}</p>
                @endif
                @if (!empty($ciclo))
                    <p style="margin:0 0 6px;"><strong>Ciclo:</strong> {{ $ciclo }}</p>
                @endif
                @if (!empty($fechaLimite))
                    <p style="margin:0;"><strong>Fecha límite de envío:</strong>
                        {{ formatDate($fechaLimite, false) }}</p>
                @endif
            </td>
        </tr>
    </table>

    @if ($esFinal)
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
            style="background-color:#fef2f2;border-left:4px solid #dc2626;border-radius:4px;margin:0 0 20px;">
            <tr>
                <td style="padding:12px 14px;font-size:13px;color:#7f1d1d;">
                    <strong>Atención:</strong> el periodo de envío de resultados está próximo a finalizar.
                </td>
            </tr>
        </table>
    @endif

    <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 auto;">
        <tr>
            <td align="center" style="border-radius:4px;background-color:#1376c8;">
                <a href="{{ url('/') }}" target="_blank"
                    style="display:inline-block;padding:11px 26px;color:#ffffff;font-size:15px;text-decoration:none;border-radius:4px;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;">
                    Ingresar al sistema
                </a>
            </td>
        </tr>
    </table>
@endsection
