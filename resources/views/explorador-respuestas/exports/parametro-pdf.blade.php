<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Explorador {{ $parametro->nombre }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #1f2937; }
        h1 { font-size: 16px; margin: 0 0 4px 0; }
        h2 { font-size: 11px; margin: 14px 0 4px 0; padding-bottom: 2px; border-bottom: 1px solid #6366F1; color: #4338CA; }
        h3 { font-size: 10px; margin: 8px 0 4px 0; color: #4B5563; }
        .meta { font-size: 9px; color: #6B7280; margin-bottom: 10px; }
        .meta strong { color: #1f2937; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 8px; }
        th, td { border: 1px solid #E5E7EB; padding: 3px 5px; text-align: left; vertical-align: top; }
        th { background-color: #F3F4F6; font-weight: bold; font-size: 8px; text-transform: uppercase; color: #374151; }
        .num { text-align: right; font-family: monospace; }
        .badge { display: inline-block; padding: 1px 5px; font-size: 8px; text-transform: uppercase; background: #EEF2FF; color: #4338CA; border-radius: 3px; }
        .footer { margin-top: 14px; padding-top: 4px; border-top: 1px solid #E5E7EB; font-size: 8px; color: #9CA3AF; }
    </style>
</head>
<body>
    <h1>{{ $parametro->nombre }}</h1>
    <div class="meta">
        <strong>Ensayo:</strong> {{ $ensayo->descripcion }} &nbsp;|&nbsp;
        <strong>Paquete:</strong> {{ $ensayo->paquete->descripcion ?? '—' }}<br>
        <strong>Ciclo:</strong> {{ $ciclo->nombre }} (gestión {{ $ciclo->gestion }}) &nbsp;|&nbsp;
        @if ($formulario)
            <strong>Formulario:</strong> {{ $formulario->nombre }} &nbsp;|&nbsp;
        @endif
        @if ($seccion)
            <strong>Sección:</strong> {{ $seccion->nombre }}
        @endif
    </div>

    <h2>Tabla de respuestas por laboratorio ({{ $valoresLab->count() }} labs)</h2>
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Laboratorio</th>
                @foreach ($campos as $c)
                    <th>{{ $c->label }} <span class="badge">{{ $c->tipo_descriptivo }}</span></th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($valoresLab as $row)
                <tr>
                    <td>{{ $row->cod_lab ?? '—' }}</td>
                    <td>{{ $row->nombre_lab }}</td>
                    @foreach ($campos as $c)
                        <td>{{ $row->valores[$c->id_campo] ?? '—' }}</td>
                    @endforeach
                </tr>
            @empty
                <tr><td colspan="{{ $campos->count() + 2 }}" style="text-align:center; color:#9CA3AF;"><em>Sin respuestas</em></td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Estadísticas por campo</h2>
    @foreach ($statsPorCampo as $sc)
        <h3>{{ $sc->campo->label }} <span class="badge">{{ $sc->campo->tipo_descriptivo }}</span></h3>
        @switch($sc->campo->tipo_descriptivo)
            @case('numerico')
                @if ($sc->stats['n'] === 0)
                    <p><em>Sin datos numéricos.</em></p>
                @else
                    <table>
                        <tr>
                            <th>N</th><th>Compl.</th><th>Media</th><th>Mediana</th><th>σ</th><th>CV %</th>
                            <th>Mín</th><th>Q1</th><th>Q3</th><th>Máx</th><th>IQR</th>
                        </tr>
                        <tr>
                            <td class="num">{{ $sc->stats['n'] }}</td>
                            <td class="num">{{ $sc->stats['pct_completitud'] }}%</td>
                            <td class="num">{{ $sc->stats['media'] }}</td>
                            <td class="num">{{ $sc->stats['mediana'] }}</td>
                            <td class="num">{{ $sc->stats['desv'] }}</td>
                            <td class="num">{{ $sc->stats['cv_pct'] ?? '—' }}</td>
                            <td class="num">{{ $sc->stats['minimo'] }}</td>
                            <td class="num">{{ $sc->stats['q1'] }}</td>
                            <td class="num">{{ $sc->stats['q3'] }}</td>
                            <td class="num">{{ $sc->stats['maximo'] }}</td>
                            <td class="num">{{ $sc->stats['iqr'] }}</td>
                        </tr>
                    </table>
                @endif
                @break

            @case('categorico')
                @if ($sc->stats['frecuencias']->isEmpty())
                    <p><em>Sin datos.</em></p>
                @else
                    <table>
                        <thead><tr><th>Opción</th><th class="num">Cantidad</th><th class="num">%</th></tr></thead>
                        <tbody>
                            @foreach ($sc->stats['frecuencias'] as $f)
                                <tr><td>{{ $f->etiqueta }}</td><td class="num">{{ $f->count }}</td><td class="num">{{ $f->pct }}%</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
                @break

            @case('booleano')
                @if ($sc->stats['n'] === 0)
                    <p><em>Sin datos.</em></p>
                @else
                    <table>
                        <tr><td>Marcado</td><td class="num">{{ $sc->stats['marcado'] }} ({{ $sc->stats['pct_marcado'] }}%)</td></tr>
                        <tr><td>No marcado</td><td class="num">{{ $sc->stats['no_marcado'] }}</td></tr>
                        <tr><td>Total</td><td class="num">{{ $sc->stats['n'] }}</td></tr>
                    </table>
                @endif
                @break

            @case('fecha')
                @if ($sc->stats['fechas']->isEmpty())
                    <p><em>Sin fechas.</em></p>
                @else
                    <table>
                        <thead><tr><th>Período</th><th class="num">Cantidad</th></tr></thead>
                        <tbody>
                            @foreach ($sc->stats['fechas'] as $f)
                                <tr><td>{{ $f->periodo }}</td><td class="num">{{ $f->count }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
                @break

            @default
                @if (empty($sc->stats['top']) || $sc->stats['top']->isEmpty())
                    <p><em>Sin valores de texto.</em></p>
                @else
                    <p>Valores distintos: <strong>{{ $sc->stats['distintos'] }}</strong></p>
                    <table>
                        <thead><tr><th>Valor</th><th class="num">Cantidad</th><th class="num">%</th></tr></thead>
                        <tbody>
                            @foreach ($sc->stats['top'] as $t)
                                <tr><td>{{ $t->valor }}</td><td class="num">{{ $t->count }}</td><td class="num">{{ $t->pct }}%</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
        @endswitch
    @endforeach

    <div class="footer">
        Generado por SigPEEC el {{ $generado_en->format('d/m/Y H:i') }}
        Análisis descriptivo a nivel de parámetro.
    </div>
</body>
</html>
