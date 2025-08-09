<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $subject ?? 'Correo de SIGPEEC' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 30px 0;
        }
        .email-container {
            background-color: white;
            max-width: 600px;
            margin: auto;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.05);
        }
        .btn {
            display: inline-block;
            padding: 12px 25px;
            background-color: #2563EB;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            font-size: 0.85em;
            color: #888;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        @yield('content')
    </div>
    <div class="footer">
        © {{ date('Y') }} SIGPEEC. Todos los derechos reservados.
    </div>
</body>
</html>
