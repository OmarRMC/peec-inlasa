@component('mail::message')
    # ¡Bienvenido a SIGPEEC!

    Hola **{{ $usuario->nombre }}**,

    Gracias por registrar tu laboratorio **{{ $laboratorio->nombre_lab }}** en SIGPEEC.

    Para completar el proceso, por favor haz clic en el siguiente botón para verificar tu correo electrónico:

    @component('mail::button', ['url' => $url_verificacion])
        Verificar Correo
    @endcomponent

    Si tú no realizaste esta acción, puedes ignorar este correo.

    Gracias,<br>
    Equipo SIGPEEC
@endcomponent
