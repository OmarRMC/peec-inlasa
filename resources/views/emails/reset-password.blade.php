@extends('emails.layout')

@section('content')
    <h1 style="color: #2563EB;">Restablece tu contraseña</h1>

    <p>Hola <strong>{{ $usuario->nombre }}</strong>,</p>

    @if ($usuario->laboratorio)
        <p>Hemos recibido una solicitud para restablecer la contraseña de tu cuenta asociada al laboratorio
            <strong>{{ $laboratorio->nombre_lab ?? 'Sin nombre' }}</strong>.
        </p>

        <div>
            Código de LAB:
            <span>
                {{ $usuario->username }}
            </span>
        </div>
    @endif

    <p>Haz clic en el siguiente botón para crear una nueva contraseña:</p>

    <p style="text-align: center; margin: 30px 0;">
        <a href="{{ $url }}" class="btn">Restablecer Contraseña</a>
    </p>

    <p>Este enlace expirará en 60 minutos.</p>

    <p>Si tú no solicitaste este restablecimiento, puedes ignorar este mensaje.</p>

    <p>Gracias,<br>Equipo SIGPEEC</p>
@endsection
