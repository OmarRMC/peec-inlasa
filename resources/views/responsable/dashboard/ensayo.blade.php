<x-app-layout>
    <div class="px-4 max-w-7xl mx-auto">

        {{-- Encabezado --}}
        <div class="mb-4">
            <a href="{{ route('reportes.resultados.ensayos') }}"
                class="text-xs text-indigo-600 hover:underline">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
            <h1 class="text-xl font-bold text-gray-800 mt-2">
                Dashboard de estadísticas
            </h1>
            <p class="text-sm text-gray-500">
                {{ $ensayo->paquete->descripcion ?? '' }} — {{ $ensayo->descripcion }}
            </p>
        </div>

        {{-- Selector de ciclo y modo --}}
        <form method="GET" class="bg-white border rounded p-3 mb-4 flex items-center gap-3 flex-wrap">
            <label class="text-sm font-medium text-gray-700">Ciclo:</label>
            <select name="ciclo" onchange="this.form.submit()"
                class="text-sm border rounded px-2 py-1 min-w-[200px]">
                @foreach ($ciclos as $c)
                    <option value="{{ $c->id }}"
                        @if ($cicloSeleccionado && $c->id == $cicloSeleccionado->id) selected @endif>
                        {{ $c->nombre }} (gestión {{ $c->gestion }})
                    </option>
                @endforeach
            </select>

            <label class="text-sm font-medium text-gray-700 ml-2">Modo:</label>
            <select name="modo" onchange="this.form.submit()"
                class="text-sm border rounded px-2 py-1">
                <option value="clasico" @if ($modo === 'clasico') selected @endif>Clásico (media/σ)</option>
                <option value="robusto" @if ($modo === 'robusto') selected @endif>Robusto (ISO 13528)</option>
            </select>

            @if ($cicloSeleccionado)
                <span class="text-xs text-gray-500 ml-auto">
                    Envío: {{ \Carbon\Carbon::parse($cicloSeleccionado->fecha_inicio_envio_resultados)->format('d/m/Y') }}
                    – {{ \Carbon\Carbon::parse($cicloSeleccionado->fecha_fin_envio_resultados)->format('d/m/Y') }}
                </span>
            @endif
        </form>

        @if (!$cicloSeleccionado)
            <div class="bg-yellow-50 border border-yellow-300 text-yellow-800 p-4 rounded">
                Este ensayo aún no tiene ciclos registrados.
            </div>
        @else

            {{-- V1: Cards KPI --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                <div class="bg-white border rounded p-3">
                    <div class="text-xs text-gray-500 uppercase">Labs que enviaron</div>
                    <div class="text-2xl font-bold text-indigo-600 mt-1">
                        {{ $kpis['tasa']['labs_enviaron'] }}
                        <span class="text-sm text-gray-400 font-normal">/ {{ $kpis['tasa']['labs_inscritos'] }}</span>
                    </div>
                </div>
                <div class="bg-white border rounded p-3">
                    <div class="text-xs text-gray-500 uppercase">Tasa de respuesta</div>
                    <div class="text-2xl font-bold text-indigo-600 mt-1">
                        {{ $kpis['tasa']['porcentaje'] !== null ? $kpis['tasa']['porcentaje'] . '%' : '—' }}
                    </div>
                </div>
                <div class="bg-white border rounded p-3">
                    <div class="text-xs text-gray-500 uppercase">Total evaluaciones</div>
                    <div class="text-2xl font-bold text-indigo-600 mt-1">
                        {{ $kpis['pct']['n'] }}
                    </div>
                </div>
                <div class="bg-white border rounded p-3">
                    <div class="text-xs text-gray-500 uppercase">% Satisfactorios</div>
                    <div class="text-2xl font-bold text-green-600 mt-1">
                        {{ $kpis['pct']['pct_satisfactorios'] !== null ? $kpis['pct']['pct_satisfactorios'] . '%' : '—' }}
                    </div>
                </div>
            </div>

            {{-- V5: Donut de desempeno global --}}
            @if ($kpis['pct']['n'] > 0)
                <div class="bg-white border rounded p-4 mb-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Desempeño global del ciclo</h3>
                    <div class="flex flex-col md:flex-row items-center gap-6">
                        <div class="w-full md:w-1/3 max-w-xs">
                            <canvas id="donutDesempeno"></canvas>
                        </div>
                        <div class="flex-1 text-sm">
                            <ul class="space-y-2">
                                <li class="flex items-center gap-2">
                                    <span class="inline-block w-3 h-3 bg-green-500 rounded-full"></span>
                                    Satisfactorios: <strong>{{ $kpis['pct']['satisfactorios'] }}</strong>
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="inline-block w-3 h-3 bg-yellow-500 rounded-full"></span>
                                    Cuestionables: <strong>{{ $kpis['pct']['cuestionables'] }}</strong>
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="inline-block w-3 h-3 bg-red-500 rounded-full"></span>
                                    No satisfactorios: <strong>{{ $kpis['pct']['no_satisfactorios'] }}</strong>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Por cada campo evaluable --}}
            @forelse ($campos as $idx => $c)
                @if ($c->consenso['n'] === 0)
                    @continue
                @endif
                <div class="bg-white border rounded p-4 mb-6">
                    <div class="border-b pb-2 mb-3">
                        <h3 class="text-sm font-bold text-gray-800">{{ $c->label }}</h3>
                        <p class="text-xs text-gray-500">
                            {{ $c->seccion }} → {{ $c->parametro }}
                        </p>
                    </div>

                    {{-- V2: Tabla de stats clasicas + comparativa robusta --}}
                    <div class="overflow-x-auto mb-4">
                        <table class="w-full text-xs">
                            <thead class="bg-gray-50 text-gray-600 uppercase">
                                <tr>
                                    <th class="px-2 py-1 text-left">Método</th>
                                    <th class="px-2 py-1 text-left">N</th>
                                    <th class="px-2 py-1 text-left">Tendencia central</th>
                                    <th class="px-2 py-1 text-left">Dispersión</th>
                                    <th class="px-2 py-1 text-left">CV %</th>
                                    <th class="px-2 py-1 text-left">Mín</th>
                                    <th class="px-2 py-1 text-left">Máx</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-t {{ $modo === 'clasico' ? 'bg-indigo-50' : '' }}">
                                    <td class="px-2 py-1 font-medium">Clásico (media / σ)</td>
                                    <td class="px-2 py-1 font-semibold">{{ $c->consenso['n'] }}</td>
                                    <td class="px-2 py-1">{{ $c->consenso['media'] }} <span class="text-gray-400">(med: {{ $c->consenso['mediana'] }})</span></td>
                                    <td class="px-2 py-1">{{ $c->consenso['desv'] }}</td>
                                    <td class="px-2 py-1">{{ $c->consenso['cv_pct'] }}</td>
                                    <td class="px-2 py-1">{{ $c->consenso['minimo'] }}</td>
                                    <td class="px-2 py-1">{{ $c->consenso['maximo'] }}</td>
                                </tr>
                                <tr class="border-t {{ $modo === 'robusto' ? 'bg-indigo-50' : '' }}">
                                    <td class="px-2 py-1 font-medium">
                                        Robusto (ISO 13528)
                                        @if ($c->consenso_robusto['convergio'])
                                            <span class="text-green-600" title="Convergió en {{ $c->consenso_robusto['iteraciones'] }} iteraciones">
                                                <i class="fas fa-check-circle"></i>
                                            </span>
                                        @elseif ($c->consenso_robusto['iteraciones'] > 0)
                                            <span class="text-yellow-600" title="No convergió en {{ $c->consenso_robusto['iteraciones'] }} iteraciones">
                                                <i class="fas fa-exclamation-circle"></i>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-1 font-semibold">{{ $c->consenso_robusto['n'] }}</td>
                                    <td class="px-2 py-1">{{ $c->consenso_robusto['x_star'] ?? '—' }} <span class="text-gray-400">(x*)</span></td>
                                    <td class="px-2 py-1">{{ $c->consenso_robusto['s_star'] ?? '—' }} <span class="text-gray-400">(s*)</span></td>
                                    <td class="px-2 py-1" colspan="3">
                                        @if ($c->consenso_robusto['s_star'] && $c->consenso['desv'])
                                            <span class="text-xs text-gray-500">
                                                σ clásica vs s* robusta: ratio
                                                <strong>{{ round($c->consenso['desv'] / $c->consenso_robusto['s_star'], 2) }}</strong>
                                                @if ($c->consenso['desv'] / $c->consenso_robusto['s_star'] > 1.5)
                                                    <span class="text-orange-600">— posible contaminación por outliers</span>
                                                @endif
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                        {{-- V7: Histograma --}}
                        <div>
                            <p class="text-xs font-semibold text-gray-600 mb-1">Distribución</p>
                            <canvas id="hist_{{ $c->id_campo }}" height="180"></canvas>
                        </div>
                        {{-- V10: Tendencia inter-ciclos --}}
                        <div>
                            <p class="text-xs font-semibold text-gray-600 mb-1">Tendencia (últimos {{ $c->tendencia->count() }} ciclos)</p>
                            @if ($c->tendencia->count() >= 2)
                                <canvas id="tend_{{ $c->id_campo }}" height="180"></canvas>
                            @else
                                <p class="text-xs text-gray-400 italic">Insuficientes ciclos para tendencia.</p>
                            @endif
                        </div>
                    </div>

                    {{-- V3: Bar chart z-scores --}}
                    @if ($c->zscores->isNotEmpty())
                        <div class="mb-4">
                            <p class="text-xs font-semibold text-gray-600 mb-1">Z-score por laboratorio</p>
                            <div style="position:relative; height:{{ max(180, $c->zscores->count() * 22) }}px;">
                                <canvas id="z_{{ $c->id_campo }}"></canvas>
                            </div>
                        </div>
                    @endif

                    {{-- V4: Tabla outliers --}}
                    @if ($c->outliers->isNotEmpty())
                        <div class="mb-2">
                            <p class="text-xs font-semibold text-red-600 mb-1">
                                <i class="fas fa-exclamation-triangle"></i>
                                Outliers ({{ $c->outliers->count() }})
                            </p>
                            <div class="overflow-x-auto">
                                <table class="w-full text-xs border">
                                    <thead class="bg-red-50">
                                        <tr>
                                            <th class="px-2 py-1 text-left">Código</th>
                                            <th class="px-2 py-1 text-left">Laboratorio</th>
                                            <th class="px-2 py-1 text-right">Valor</th>
                                            <th class="px-2 py-1 text-right">Z-score</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($c->outliers as $o)
                                            <tr class="border-t">
                                                <td class="px-2 py-1">{{ $o->cod_lab ?? '—' }}</td>
                                                <td class="px-2 py-1">{{ $o->nombre_lab }}</td>
                                                <td class="px-2 py-1 text-right">{{ $o->valor }}</td>
                                                <td class="px-2 py-1 text-right font-bold text-red-700">{{ $o->z_score }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    {{-- V8: Box-plot (sin libreria extra: dibujo simple en canvas) --}}
                    @if ($c->boxplot['q1'] !== null)
                        <details class="mt-3">
                            <summary class="text-xs text-gray-500 cursor-pointer hover:text-indigo-600">
                                Ver box-plot (Q1, mediana, Q3 + outliers IQR)
                            </summary>
                            <div class="mt-2 p-3 bg-gray-50 rounded text-xs">
                                <div class="grid grid-cols-3 md:grid-cols-6 gap-2">
                                    <div><span class="text-gray-500">Mín:</span> <strong>{{ $c->boxplot['min'] }}</strong></div>
                                    <div><span class="text-gray-500">Q1:</span> <strong>{{ $c->boxplot['q1'] }}</strong></div>
                                    <div><span class="text-gray-500">Mediana:</span> <strong>{{ $c->boxplot['mediana'] }}</strong></div>
                                    <div><span class="text-gray-500">Q3:</span> <strong>{{ $c->boxplot['q3'] }}</strong></div>
                                    <div><span class="text-gray-500">Máx:</span> <strong>{{ $c->boxplot['max'] }}</strong></div>
                                    <div><span class="text-gray-500">Outliers IQR:</span> <strong>{{ count($c->boxplot['outliers']) }}</strong></div>
                                </div>
                                <canvas id="box_{{ $c->id_campo }}" height="80" class="mt-2"></canvas>
                            </div>
                        </details>
                    @endif
                </div>
            @empty
                <div class="bg-gray-50 border rounded p-6 text-center text-gray-500">
                    Este ensayo no tiene campos evaluables (numéricos) con respuestas en el ciclo seleccionado.
                </div>
            @endforelse

        @endif
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const COLOR_SAT = 'rgba(34,197,94,0.85)';     // green-500
        const COLOR_CUE = 'rgba(234,179,8,0.85)';     // yellow-500
        const COLOR_NS  = 'rgba(239,68,68,0.85)';     // red-500
        const COLOR_BAR = 'rgba(99,102,241,0.7)';     // indigo-500
        const COLOR_LINE = 'rgba(99,102,241,1)';

        function colorZ(z) {
            const a = Math.abs(z);
            if (a < 2) return COLOR_SAT;
            if (a < 3) return COLOR_CUE;
            return COLOR_NS;
        }

        @if ($cicloSeleccionado && $kpis['pct']['n'] > 0)
        new Chart(document.getElementById('donutDesempeno'), {
            type: 'doughnut',
            data: {
                labels: ['Satisfactorios', 'Cuestionables', 'No satisfactorios'],
                datasets: [{
                    data: [{{ $kpis['pct']['satisfactorios'] }}, {{ $kpis['pct']['cuestionables'] }}, {{ $kpis['pct']['no_satisfactorios'] }}],
                    backgroundColor: [COLOR_SAT, COLOR_CUE, COLOR_NS],
                }]
            },
            options: { responsive: true, plugins: { legend: { display: false }}}
        });
        @endif

        @foreach ($campos as $c)
            @if ($c->consenso['n'] === 0) @continue @endif

            // Histograma
            new Chart(document.getElementById('hist_{{ $c->id_campo }}'), {
                type: 'bar',
                data: {
                    labels: @json($c->histograma['bins'] ? array_column($c->histograma['bins'], 'etiqueta') : []),
                    datasets: [{
                        label: 'Frecuencia',
                        data: @json($c->histograma['bins'] ? array_column($c->histograma['bins'], 'count') : []),
                        backgroundColor: COLOR_BAR,
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false }},
                    scales: { x: { ticks: { font: { size: 9 }, maxRotation: 45 }}, y: { beginAtZero: true }}
                }
            });

            // Tendencia
            @if ($c->tendencia->count() >= 2)
            new Chart(document.getElementById('tend_{{ $c->id_campo }}'), {
                type: 'line',
                data: {
                    labels: @json($c->tendencia->pluck('nombre')->all()),
                    datasets: [{
                        label: 'Media de consenso',
                        data: @json($c->tendencia->pluck('media')->all()),
                        borderColor: COLOR_LINE,
                        backgroundColor: 'rgba(99,102,241,0.15)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false }},
                    scales: { x: { ticks: { font: { size: 9 }}}}
                }
            });
            @endif

            // Z-scores por lab
            @if ($c->zscores->isNotEmpty())
            (function () {
                const zs = @json($c->zscores->all());
                new Chart(document.getElementById('z_{{ $c->id_campo }}'), {
                    type: 'bar',
                    data: {
                        labels: zs.map(z => z.cod_lab || '(' + z.id_laboratorio + ')'),
                        datasets: [{
                            label: 'Z-score',
                            data: zs.map(z => z.z_score),
                            backgroundColor: zs.map(z => colorZ(z.z_score)),
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true, maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    afterLabel: ctx => 'Lab: ' + zs[ctx.dataIndex].nombre_lab + ' | Valor: ' + zs[ctx.dataIndex].valor + ' | ' + zs[ctx.dataIndex].desempeno
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: { font: { size: 10 }},
                                title: { display: true, text: 'Z-score' }
                            },
                            y: { ticks: { font: { size: 9 }}}
                        }
                    }
                });
            })();
            @endif

            // Box-plot simplificado (linea + caja dibujada en canvas)
            @if ($c->boxplot['q1'] !== null)
            (function () {
                const cv = document.getElementById('box_{{ $c->id_campo }}');
                if (!cv) return;
                const ctx = cv.getContext('2d');
                const w = cv.width = cv.clientWidth, h = cv.height = 60;
                const bp = @json($c->boxplot);
                const span = bp.max - bp.min || 1;
                const x = v => 20 + ((v - bp.min) / span) * (w - 40);
                ctx.clearRect(0,0,w,h);
                // ejes
                ctx.strokeStyle = '#9ca3af'; ctx.lineWidth = 1;
                ctx.beginPath(); ctx.moveTo(20, h/2); ctx.lineTo(w-20, h/2); ctx.stroke();
                // whiskers
                ctx.beginPath();
                ctx.moveTo(x(bp.whisker_min), h/2-10); ctx.lineTo(x(bp.whisker_min), h/2+10);
                ctx.moveTo(x(bp.whisker_max), h/2-10); ctx.lineTo(x(bp.whisker_max), h/2+10);
                ctx.moveTo(x(bp.whisker_min), h/2); ctx.lineTo(x(bp.q1), h/2);
                ctx.moveTo(x(bp.q3), h/2); ctx.lineTo(x(bp.whisker_max), h/2);
                ctx.stroke();
                // caja
                ctx.fillStyle = 'rgba(99,102,241,0.3)';
                ctx.fillRect(x(bp.q1), h/2-12, x(bp.q3)-x(bp.q1), 24);
                ctx.strokeStyle = '#6366f1'; ctx.lineWidth = 2;
                ctx.strokeRect(x(bp.q1), h/2-12, x(bp.q3)-x(bp.q1), 24);
                // mediana
                ctx.beginPath(); ctx.moveTo(x(bp.mediana), h/2-12); ctx.lineTo(x(bp.mediana), h/2+12); ctx.stroke();
                // outliers
                ctx.fillStyle = COLOR_NS;
                bp.outliers.forEach(v => { ctx.beginPath(); ctx.arc(x(v), h/2, 3, 0, 2*Math.PI); ctx.fill(); });
            })();
            @endif
        @endforeach
    });
    </script>
    @endpush
</x-app-layout>
