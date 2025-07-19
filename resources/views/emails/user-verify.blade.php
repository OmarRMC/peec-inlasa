@extends('emails.layout')

@section('content')
    <h1 style="color: #2563EB;">¡Bienvenido a SIGPEEC!</h1>

    <p>Hola <strong>{{ $usuario->nombre }}</strong>,</p>

    <p>Para completar el proceso, por favor haz clic en el siguiente botón para verificar tu correo electrónico:</p>

    <p style="text-align: center; margin: 30px 0;">
        <a href="{{ $url_verificacion }}" class="btn">Verificar Correo</a>
    </p>

    <p>Si tú no realizaste esta acción, puedes ignorar este correo.</p>

    <p>Gracias,<br>Equipo SIGPEEC</p>
@endsection
