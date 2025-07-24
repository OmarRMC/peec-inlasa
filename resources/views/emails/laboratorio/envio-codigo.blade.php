@extends('emails.layout')

@section('content')
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f9fafb; padding: 2rem;">
        <tr>
            <td align="center">
                <h1 style="color: #2563EB; font-size: 24px; margin-bottom: 10px;">🎉 ¡Registro completado exitosamente!</h1>
                <p style="font-size: 16px; color: #374151; max-width: 600px;">
                    Hola <strong>{{ $usuario->nombre }}</strong>,
                </p>

                <p style="font-size: 16px; color: #374151; max-width: 600px;">
                    Gracias por registrar tu laboratorio <strong>{{ $laboratorio->nombre_lab }}</strong> en la plataforma
                    <strong>SIGPEEC</strong>.
                    Ahora formas parte del sistema oficial de evaluación externa de la calidad. ¡Bienvenido!
                </p>

                <div
                    style="background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 1.5rem; margin: 2rem 0; max-width: 600px; text-align: left;">
                    <h3 style="color: #2563EB; margin-bottom: 10px;">🔐 Credenciales de acceso</h3>
                    <p><strong>Usuario (código de laboratorio):</strong> {{ $usuario->username }}</p>
                    <p><strong>Contraseña:</strong> La que registraste al momento de tu inscripción.</p>
                    <p style="font-size: 14px; color: #6b7280;">(Por seguridad, te recomendamos no compartir esta
                        información.)</p>
                </div>

                <a href="{{ route('login') }}"
                    style="display: inline-block; padding: 12px 24px; background-color: #2563EB; color: white; font-weight: bold; text-decoration: none; border-radius: 6px; margin-bottom: 20px;">
                    🔑 Iniciar sesión
                </a>

                <p style="font-size: 14px; color: #6b7280; max-width: 600px;">
                    Si tú no realizaste esta acción, puedes ignorar este mensaje.
                </p>

                <p style="margin-top: 30px; font-size: 14px; color: #4b5563;">
                    Atentamente,<br>
                    <strong>Equipo SIGPEEC</strong>
                </p>
            </td>
        </tr>
    </table>
@endsection
