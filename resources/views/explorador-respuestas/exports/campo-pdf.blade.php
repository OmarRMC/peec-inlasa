<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Explorador de respuestas {{ $campo->etiqueta }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1f2937; }
        h1 { font-size: 16px; margin: 0 0 4px 0; }
        h2 { font-size: 12px; margin: 16px 0 6px 0; padding-bottom: 2px; border-bottom: 1px solid #6366F1; color: #4338CA; }
        .meta { font-size: 9px; color: #6B7280; margin-bottom: 12px; }
        .meta strong { color: #1f2937; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 10px; }
        th, td { border: 1px solid #E5E7EB; padding: 4px 6px; text-align: left; vertical-align: top; }
        th { background-color: #F3F4F6; font-weight: bold; font-size: 9px; text-transform: uppercase; color: #374151; }
        .num { text-align: right; font-family: monospace; }
        .grid { width: 100%; }
        .grid td { border: none; padding: 4px; vertical-align: top; }
        .card { border: 1px solid #E5E7EB; padding: 6px 8px; margin-bottom: 4px; }
        .label { font-size: 8px; text-transform: uppercase; color: #6B7280; }
        .value { font-size: 14px; font-weight: bold; color: #1f2937; }
        .footer { margin-top: 20px; padding-top: 6px; border-top: 1px solid #E5E7EB; font-size: 8px; color: #9CA3AF; }
        .badge { display: inline-block; padding: 2px 6px; font-size: 8px; text-transform: uppercase; background: #EEF2FF; color: #4338CA; border-radius: 3px; }
        .tag { display: inline-block; padding: 1px 4px; font-size: 8px; background: #F3F4F6; color: #4B5563; border-radius: 2px; }
    </style>
</head>
<body>
    <h1>{{ $campo->etiqueta }}</h1>
    <div class="meta">
        <strong>Ensayo:</strong> {{ $ensayo->descripcion }}<br>
        <strong>Paquete:</strong> {{ $ensayo->paquete->descripcion ?? '-' }}<br>
        <strong>Ciclo:</strong> {{ $ciclo->nombre }} (gestión {{ $ciclo->gestion }})<br>
        @if ($formulario)
            <strong>Formulario:</strong> {{ $formulario->nombre }}<br>
        @endif
        @if ($seccion)
            <strong>Sección:</strong> {{ $seccion->nombre }} &nbsp;|&nbsp;
            <strong>Parámetro:</strong> {{ $parametro->nombre ?? '' }}<br>
        @endif
        <span class="tag">Tipo catálogo: {{ $campo->tipo }}</span>
        <span class="badge">Tratado como: {{ $tipo }}</span>
    </div>

    @switch($tipo)
        @case('numerico')
            @if ($descriptivo['n'] === 0)
                <p><em>Sin datos numéricos en este ciclo.</em></p>
            @else
                <h2>Estadísticos descriptivos</h2>
                <table class="grid">
                    <tr>
                        <td><div class="card"><div class="label">N</div><div class="value">{{ $descriptivo['n'] }}</div></div></td>
                        <td><div class="card"><div class="label">Completitud</div><div class="value">{{ $descriptivo['pct_completitud'] }}%</div></div></td>
                        <td><div class="card"><div class="label">Media</div><div class="value">{{ $descriptivo['media'] }}</div></div></td>
                        <td><div class="card"><div class="label">Mediana</div><div class="value">{{ $descriptivo['mediana'] }}</div></div></td>
                    </tr>
                    <tr>
                        <td><div class="card"><div class="label">σ</div><div class="value">{{ $descriptivo['desv'] }}</div></div></td>
                        <td><div class="card"><div class="label">CV %</div><div class="value">{{ $descriptivo['cv_pct'] ?? '-' }}</div></div></td>
                        <td><div class="card"><div class="label">Mínimo</div><div class="value">{{ $descriptivo['minimo'] }}</div></div></td>
                        <td><div class="card"><div class="label">Máximo</div><div class="value">{{ $descriptivo['maximo'] }}</div></div></td>
                    </tr>
                    <tr>
                        <td><div class="card"><div class="label">Q1</div><div class="value">{{ $descriptivo['q1'] }}</div></div></td>
                        <td><div class="card"><div class="label">Q3</div><div class="value">{{ $descriptivo['q3'] }}</div></div></td>
                        <td><div class="card"><div class="label">IQR</div><div class="value">{{ $descriptivo['iqr'] }}</div></div></td>
                        <td><div class="card"><div class="label">Outliers</div><div class="value">{{ count($boxplot['outliers']) }}</div></div></td>
                    </tr>
                </table>

                <h2>Histograma (distribución por bins)</h2>
                <table>
                    <thead>
                        <tr><th>Rango</th><th class="num">Frecuencia</th><th>Distribución</th></tr>
                    </thead>
                    <tbody>
                        @php $maxBin = max(array_column($histograma['bins'], 'count') ?: [1]); @endphp
                        @foreach ($histograma['bins'] as $bin)
                            <tr>
                                <td>{{ $bin['etiqueta'] }}</td>
                                <td class="num">{{ $bin['count'] }}</td>
                                <td>
                                    <div style="background:#6366F1; height:8px; width:{{ ($bin['count'] / $maxBin) * 100 }}%;"></div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <h2>Valores enviados por laboratorio ({{ $valoresLab->count() }})</h2>
                <table>
                    <thead>
                        <tr><th>Código</th><th>Laboratorio</th><th class="num">Valor</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($valoresLab as $v)
                            <tr>
                                <td>{{ $v->cod_lab ?? '-' }}</td>
                                <td>{{ $v->nombre_lab }}</td>
                                <td class="num">{{ $v->valor }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            @break

        @case('categorico')
            @if ($frecuencias->isEmpty())
                <p><em>Sin respuestas en este ciclo.</em></p>
            @else
                <h2>Frecuencia por opción ({{ $frecuencias->sum('count') }} respuestas)</h2>
                <table>
                    <thead><tr><th>Opción</th><th class="num">Cantidad</th><th class="num">%</th></tr></thead>
                    <tbody>
                        @foreach ($frecuencias as $f)
                            <tr>
                                <td>{{ $f->etiqueta }}</td>
                                <td class="num">{{ $f->count }}</td>
                                <td class="num">{{ $f->pct }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            @break

        @case('booleano')
            @if ($booleano['n'] === 0)
                <p><em>Sin datos en este ciclo.</em></p>
            @else
                <h2>Distribución marcado / no marcado</h2>
                <table>
                    <tr><td>Total respuestas</td><td class="num">{{ $booleano['n'] }}</td></tr>
                    <tr><td>Marcado</td><td class="num">{{ $booleano['marcado'] }} ({{ $booleano['pct_marcado'] }}%)</td></tr>
                    <tr><td>No marcado</td><td class="num">{{ $booleano['no_marcado'] }} ({{ 100 - $booleano['pct_marcado'] }}%)</td></tr>
                </table>
            @endif
            @break

        @case('fecha')
            @if ($fechas->isEmpty())
                <p><em>Sin fechas registradas.</em></p>
            @else
                <h2>Distribución temporal por mes</h2>
                <table>
                    <thead><tr><th>Período</th><th class="num">Cantidad</th></tr></thead>
                    <tbody>
                        @foreach ($fechas as $f)
                            <tr><td>{{ $f->periodo }}</td><td class="num">{{ $f->count }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            @break

        @default
            @if ($topValores->isEmpty())
                <p><em>Sin respuestas de texto.</em></p>
            @else
                <h2>Valores distintos: {{ $distintos }}</h2>
                <h2>Top valores más frecuentes</h2>
                <table>
                    <thead><tr><th>Valor</th><th class="num">Cantidad</th><th class="num">%</th><th class="num">Long. prom.</th></tr></thead>
                    <tbody>
                        @foreach ($topValores as $t)
                            <tr>
                                <td>{{ $t->valor }}</td>
                                <td class="num">{{ $t->count }}</td>
                                <td class="num">{{ $t->pct }}%</td>
                                <td class="num">{{ $t->longitud_promedio }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
    @endswitch

    <div class="footer">
        Generado por SigPEEC el {{ $generado_en->format('d/m/Y H:i') }}
        Análisis descriptivo (sin evaluación de aptitud).
    </div>
</body>
</html>
