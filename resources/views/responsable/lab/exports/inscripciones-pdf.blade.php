<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inscripciones a {{ $ensayosAptitud->descripcion }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #1f2937; }
        h1 { font-size: 15px; margin: 0 0 4px 0; }
        .meta { font-size: 9px; color: #6B7280; margin-bottom: 12px; }
        .meta strong { color: #1f2937; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 10px; }
        th, td { border: 1px solid #E5E7EB; padding: 4px 6px; text-align: left; vertical-align: top; }
        th { background-color: #4F81BD; font-weight: bold; font-size: 9px; text-transform: uppercase; color: #FFFFFF; }
        tbody tr:nth-child(even) { background-color: #F9FAFB; }
        .footer { margin-top: 20px; padding-top: 6px; border-top: 1px solid #E5E7EB; font-size: 8px; color: #9CA3AF; }
        .vacio { font-style: italic; color: #6B7280; }
    </style>
</head>
<body>
    <h1>Lista de inscripciones a {{ $ensayosAptitud->descripcion }}</h1>
    <div class="meta">
        <strong>Paquete:</strong> {{ $ensayosAptitud->paquete->descripcion ?? 'N/D' }}<br>
        <strong>Gestión:</strong> {{ $gestion }} &nbsp;|&nbsp;
        <strong>Total de laboratorios:</strong> {{ $laboratorios->count() }}
    </div>

    @if ($laboratorios->isEmpty())
        <p class="vacio">No se encontraron inscripciones para la gestión seleccionada.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Fecha/Hora</th>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>WhatsApp</th>
                    <th>Departamento</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($laboratorios as $lab)
                    <tr>
                        <td>{{ $lab[0] }}</td>
                        <td>{{ $lab[1] }}</td>
                        <td>{{ $lab[2] }}</td>
                        <td>{{ $lab[3] }}</td>
                        <td>{{ $lab[4] }}</td>
                        <td>{{ $lab[5] }}</td>
                        <td>{{ $lab[6] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        Generado por {{ $generado_por }} el {{ $generado_en->format('d/m/Y H:i') }}
    </div>
</body>
</html>
