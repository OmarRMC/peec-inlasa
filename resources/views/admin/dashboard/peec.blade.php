<x-app-layout>
    <div class="px-4 max-w-7xl mx-auto">
        <div class="mb-4">
            <h1 class="text-xl font-bold text-gray-800">Dashboard PEEC - Panorama global</h1>
            <p class="text-sm text-gray-500">
                Estadísticas agregadas de todos los ensayos de aptitud y laboratorios participantes en la gestión seleccionada.
            </p>
        </div>

        {{-- Selector de gestion --}}
        <form method="GET" class="bg-white border rounded p-3 mb-4 flex items-center gap-3">
            <label class="text-sm font-medium text-gray-700">Gestión:</label>
            <select name="gestion" onchange="this.form.submit()"
                class="text-sm border rounded px-2 py-1 min-w-[120px]">
                @forelse ($gestiones as $g)
                    <option value="{{ $g }}" @if ($g == $gestion) selected @endif>{{ $g }}</option>
                @empty
                    <option value="{{ $gestion }}">{{ $gestion }}</option>
                @endforelse
            </select>
        </form>

        {{-- V1: Cards KPI globales --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
            <div class="bg-white border rounded p-3">
                <div class="text-xs text-gray-500 uppercase">Ensayos activos</div>
                <div class="text-2xl font-bold text-indigo-600 mt-1">{{ $kpis['ensayos_activos'] }}</div>
            </div>
            <div class="bg-white border rounded p-3">
                <div class="text-xs text-gray-500 uppercase">Laboratorios activos</div>
                <div class="text-2xl font-bold text-indigo-600 mt-1">{{ $kpis['labs_activos'] }}</div>
            </div>
            <div class="bg-white border rounded p-3">
                <div class="text-xs text-gray-500 uppercase">Resultados recibidos</div>
                <div class="text-2xl font-bold text-indigo-600 mt-1">{{ $kpis['total_resultados'] }}</div>
            </div>
            <div class="bg-white border rounded p-3">
                <div class="text-xs text-gray-500 uppercase">% Aptitud global</div>
                <div class="text-2xl font-bold text-green-600 mt-1">
                    {{ $kpis['pct_aptitud'] !== null ? $kpis['pct_aptitud'] . '%' : '-' }}
                </div>
            </div>
        </div>

        {{-- V6: Bar chart tasa respuesta por ensayo --}}
        <div class="bg-white border rounded p-4 mb-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">
                Tasa de respuesta por ensayo
                <span class="text-xs text-gray-400 font-normal">(ciclo más reciente de cada ensayo)</span>
            </h3>
            @if ($tasasPorEnsayo->isNotEmpty())
                <div style="position:relative; height:{{ max(200, $tasasPorEnsayo->count() * 24) }}px;">
                    <canvas id="tasasPorEnsayo"></canvas>
                </div>
            @else
                <p class="text-xs text-gray-400 italic">Sin ensayos en esta gestión.</p>
            @endif
        </div>

        {{-- Comportamiento: inscritos por ensayo / por paquete --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
            {{-- Inscritos por ensayo --}}
            <div class="bg-white border rounded p-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">
                    Laboratorios inscritos por ensayo
                    <span class="text-xs text-gray-400 font-normal">(demanda en la gestión)</span>
                </h3>
                @if ($inscritosPorEnsayo->isNotEmpty())
                    <div style="position:relative; height:{{ max(200, $inscritosPorEnsayo->count() * 24) }}px;">
                        <canvas id="inscritosPorEnsayo"></canvas>
                    </div>
                @else
                    <p class="text-xs text-gray-400 italic">Sin inscripciones en esta gestión.</p>
                @endif
            </div>

            {{-- Inscritos por paquete --}}
            <div class="bg-white border rounded p-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">
                    Laboratorios inscritos por paquete
                    <span class="text-xs text-gray-400 font-normal">(vs cupo máximo)</span>
                </h3>
                @if ($inscritosPorPaquete->isNotEmpty())
                    <div style="position:relative; height:{{ max(200, $inscritosPorPaquete->count() * 28) }}px;">
                        <canvas id="inscritosPorPaquete"></canvas>
                    </div>
                @else
                    <p class="text-xs text-gray-400 italic">Sin inscripciones en esta gestión.</p>
                @endif
            </div>
        </div>

        {{-- Evolución de inscripciones por gestión --}}
        <div class="bg-white border rounded p-4 mb-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">
                Evolución de inscripciones por gestión
                <span class="text-xs text-gray-400 font-normal">(todas las gestiones)</span>
            </h3>
            @if ($evolucion->isNotEmpty())
                <div style="position:relative; height:280px;">
                    <canvas id="evolucionInscripciones"></canvas>
                </div>
            @else
                <p class="text-xs text-gray-400 italic">Sin inscripciones registradas.</p>
            @endif
        </div>

        {{-- V9: Heatmap ensayos x ciclos --}}
        @if (count($heatmap['ensayos']) > 0 && count($heatmap['ciclos']) > 0)
            <div class="bg-white border rounded p-4 mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-1">
                    Heatmap ensayos × ciclos
                </h3>
                <p class="text-xs text-gray-500 mb-3">
                    Color por % de evaluaciones satisfactorias por ciclo. Gris = sin datos numéricos.
                </p>
                <div class="overflow-x-auto">
                    <table class="text-xs border-collapse">
                        <thead>
                            <tr>
                                <th class="px-2 py-1 text-left bg-gray-50 sticky left-0 z-10">Paquete / Ensayo</th>
                                @foreach ($heatmap['ciclos'] as $c)
                                    <th class="px-2 py-1 text-center bg-gray-50 min-w-[80px]" title="{{ $c['nombre'] }}">
                                        {{ Str::limit($c['nombre'], 12, '…') }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($heatmap['ensayos'] as $e)
                                <tr>
                                    <td class="px-2 py-1 bg-white sticky left-0 z-10 border-r"
                                        title="{{ ($e['paquete'] ?? 'Sin paquete') . ' - ' . $e['descripcion'] }}">
                                        <div class="text-[10px] text-gray-500 uppercase leading-tight">
                                            {{ Str::limit($e['paquete'] ?? 'Sin paquete', 30, '…') }}
                                        </div>
                                        <div class="font-medium leading-tight">
                                            {{ Str::limit($e['descripcion'], 35, '…') }}
                                        </div>
                                    </td>
                                    @foreach ($heatmap['ciclos'] as $c)
                                        @php
                                            // Solo dibujar celda si el ciclo pertenece a este ensayo
                                            $perteneceCiclo = $c['id_ensayo'] === $e['id'];
                                            $pct = $perteneceCiclo ? ($heatmap['celdas'][$e['id']][$c['id']] ?? null) : null;
                                            if (!$perteneceCiclo) {
                                                $bg = 'transparent';
                                                $color = 'transparent';
                                                $text = '';
                                            } elseif ($pct === null) {
                                                $bg = '#e5e7eb';
                                                $color = '#9ca3af';
                                                $text = '-';
                                            } elseif ($pct >= 80) {
                                                $bg = 'rgba(34,197,94,0.85)';
                                                $color = 'white';
                                                $text = $pct . '%';
                                            } elseif ($pct >= 50) {
                                                $bg = 'rgba(234,179,8,0.85)';
                                                $color = '#78350f';
                                                $text = $pct . '%';
                                            } else {
                                                $bg = 'rgba(239,68,68,0.85)';
                                                $color = 'white';
                                                $text = $pct . '%';
                                            }
                                        @endphp
                                        <td class="text-center font-semibold"
                                            style="background:{{ $bg }}; color:{{ $color }}; padding:6px;"
                                            @if ($perteneceCiclo && $pct !== null) title="{{ ($e['paquete'] ?? 'Sin paquete') . ' - ' . $e['descripcion'] }} | Ciclo: {{ $c['nombre'] }} | Satisfactorios: {{ $pct }}%" @endif>
                                            {{ $text }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="flex items-center gap-3 mt-3 text-xs text-gray-600">
                    <span class="inline-flex items-center gap-1"><span class="inline-block w-3 h-3" style="background:rgba(34,197,94,0.85)"></span> ≥80%</span>
                    <span class="inline-flex items-center gap-1"><span class="inline-block w-3 h-3" style="background:rgba(234,179,8,0.85)"></span> 50-79%</span>
                    <span class="inline-flex items-center gap-1"><span class="inline-block w-3 h-3" style="background:rgba(239,68,68,0.85)"></span> &lt;50%</span>
                    <span class="inline-flex items-center gap-1"><span class="inline-block w-3 h-3 bg-gray-300"></span> Sin datos</span>
                </div>
            </div>
        @endif

        {{-- V11: Labs con problema persistente --}}
        @if ($labsProblematicos->isNotEmpty())
            <div class="bg-white border rounded p-4 mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-1">
                    <i class="fas fa-exclamation-triangle text-red-500 mr-1"></i>
                    Laboratorios con desempeño problemático persistente
                </h3>
                <p class="text-xs text-gray-500 mb-3">
                    Labs con 2 o más respuestas no satisfactorias (|z| ≥ 3) en los últimos 6 ciclos.
                </p>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead class="bg-red-50 text-gray-700 uppercase">
                            <tr>
                                <th class="px-2 py-1 text-left">Código</th>
                                <th class="px-2 py-1 text-left">Laboratorio</th>
                                <th class="px-2 py-1 text-right">No-satisfactorios</th>
                                <th class="px-2 py-1 text-right">Total evaluaciones</th>
                                <th class="px-2 py-1 text-right">% No-sat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($labsProblematicos as $lab)
                                <tr class="border-t hover:bg-red-50">
                                    <td class="px-2 py-1 font-mono">{{ $lab->cod_lab ?? '-' }}</td>
                                    <td class="px-2 py-1">{{ $lab->nombre_lab }}</td>
                                    <td class="px-2 py-1 text-right font-bold text-red-700">{{ $lab->total_no_sat }}</td>
                                    <td class="px-2 py-1 text-right">{{ $lab->total_evaluaciones }}</td>
                                    <td class="px-2 py-1 text-right font-bold text-red-700">{{ $lab->pct_no_sat }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Tabla detalle de tasas --}}
        @if ($tasasPorEnsayo->isNotEmpty())
            <div class="bg-white border rounded p-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Detalle por ensayo</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead class="bg-gray-50 text-gray-600 uppercase">
                            <tr>
                                <th class="px-2 py-1 text-left">Paquete</th>
                                <th class="px-2 py-1 text-left">Ensayo</th>
                                <th class="px-2 py-1 text-right">Labs enviaron</th>
                                <th class="px-2 py-1 text-right">Labs inscritos</th>
                                <th class="px-2 py-1 text-right">% Respuesta</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tasasPorEnsayo as $t)
                                <tr class="border-t">
                                    <td class="px-2 py-1 text-gray-500 text-[11px]">{{ $t->paquete ?? '-' }}</td>
                                    <td class="px-2 py-1">{{ $t->descripcion }}</td>
                                    <td class="px-2 py-1 text-right">{{ $t->labs_enviaron }}</td>
                                    <td class="px-2 py-1 text-right">{{ $t->labs_inscritos }}</td>
                                    <td class="px-2 py-1 text-right font-bold
                                        @if ($t->porcentaje === null) text-gray-400
                                        @elseif ($t->porcentaje >= 80) text-green-600
                                        @elseif ($t->porcentaje >= 50) text-yellow-600
                                        @else text-red-600
                                        @endif">
                                        {{ $t->porcentaje !== null ? $t->porcentaje . '%' : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        @if ($tasasPorEnsayo->isNotEmpty())
        (function () {
            const tasas = @json($tasasPorEnsayo->all());
            // Label: "Paquete - Ensayo" en 2 lineas (Chart.js soporta array como label)
            const labels = tasas.map(t => {
                const paq = (t.paquete || 'Sin paquete');
                const ens = t.descripcion;
                const paqShort = paq.length > 30 ? paq.slice(0,30)+'...' : paq;
                const ensShort = ens.length > 35 ? ens.slice(0,35)+'...' : ens;
                return [paqShort, ensShort];
            });
            new Chart(document.getElementById('tasasPorEnsayo'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: '% Respuesta',
                        data: tasas.map(t => t.porcentaje ?? 0),
                        backgroundColor: tasas.map(t => {
                            if (t.porcentaje === null) return 'rgba(156,163,175,0.5)';
                            if (t.porcentaje >= 80)   return 'rgba(34,197,94,0.85)';
                            if (t.porcentaje >= 50)   return 'rgba(234,179,8,0.85)';
                            return 'rgba(239,68,68,0.85)';
                        }),
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true, maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                title: items => {
                                    const t = tasas[items[0].dataIndex];
                                    return (t.paquete || 'Sin paquete') + ' - ' + t.descripcion;
                                },
                                afterLabel: ctx => 'Enviaron: ' + tasas[ctx.dataIndex].labs_enviaron + ' / Inscritos: ' + tasas[ctx.dataIndex].labs_inscritos
                            }
                        }
                    },
                    scales: {
                        x: { beginAtZero: true, max: 100, title: { display: true, text: '%' }},
                        y: { ticks: { font: { size: 9 }, autoSkip: false }}
                    }
                }
            });
        })();
        @endif

        @if ($inscritosPorEnsayo->isNotEmpty())
        (function () {
            const datos = @json($inscritosPorEnsayo->all());
            const labels = datos.map(d => {
                const paq = (d.paquete || 'Sin paquete');
                const ens = d.descripcion;
                const paqShort = paq.length > 30 ? paq.slice(0,30)+'...' : paq;
                const ensShort = ens.length > 35 ? ens.slice(0,35)+'...' : ens;
                return [paqShort, ensShort];
            });
            new Chart(document.getElementById('inscritosPorEnsayo'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Labs inscritos',
                        data: datos.map(d => d.labs_inscritos),
                        backgroundColor: 'rgba(79,70,229,0.85)',
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true, maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                title: items => {
                                    const d = datos[items[0].dataIndex];
                                    return (d.paquete || 'Sin paquete') + ' - ' + d.descripcion;
                                },
                                label: ctx => 'Labs inscritos: ' + datos[ctx.dataIndex].labs_inscritos
                            }
                        }
                    },
                    scales: {
                        x: { beginAtZero: true, ticks: { precision: 0 }, title: { display: true, text: 'Laboratorios' }},
                        y: { ticks: { font: { size: 9 }, autoSkip: false }}
                    }
                }
            });
        })();
        @endif

        @if ($inscritosPorPaquete->isNotEmpty())
        (function () {
            const datos = @json($inscritosPorPaquete->all());
            const labels = datos.map(d => {
                const desc = d.descripcion || 'Sin nombre';
                return desc.length > 40 ? desc.slice(0,40)+'...' : desc;
            });
            new Chart(document.getElementById('inscritosPorPaquete'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Labs inscritos',
                        data: datos.map(d => d.labs_inscritos),
                        backgroundColor: datos.map(d => {
                            if (!d.max_participantes) return 'rgba(79,70,229,0.85)';
                            const ocup = d.labs_inscritos / d.max_participantes;
                            if (ocup >= 1)   return 'rgba(239,68,68,0.85)';   // lleno / sobrecupo
                            if (ocup >= 0.8) return 'rgba(234,179,8,0.85)';   // casi lleno
                            return 'rgba(34,197,94,0.85)';                    // con cupo
                        }),
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true, maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                title: items => datos[items[0].dataIndex].descripcion,
                                label: ctx => {
                                    const d = datos[ctx.dataIndex];
                                    const cupo = d.max_participantes ? (' / ' + d.max_participantes + ' cupos') : '';
                                    return 'Inscritos: ' + d.labs_inscritos + cupo;
                                }
                            }
                        }
                    },
                    scales: {
                        x: { beginAtZero: true, ticks: { precision: 0 }, title: { display: true, text: 'Laboratorios' }},
                        y: { ticks: { font: { size: 10 }, autoSkip: false }}
                    }
                }
            });
        })();
        @endif

        @if ($evolucion->isNotEmpty())
        (function () {
            const datos = @json($evolucion->all());
            new Chart(document.getElementById('evolucionInscripciones'), {
                type: 'line',
                data: {
                    labels: datos.map(d => d.gestion),
                    datasets: [
                        {
                            label: 'Inscripciones',
                            data: datos.map(d => d.total_inscripciones),
                            borderColor: 'rgba(79,70,229,1)',
                            backgroundColor: 'rgba(79,70,229,0.15)',
                            tension: 0.3, fill: true,
                        },
                        {
                            label: 'Laboratorios',
                            data: datos.map(d => d.total_labs),
                            borderColor: 'rgba(16,185,129,1)',
                            backgroundColor: 'rgba(16,185,129,0.15)',
                            tension: 0.3, fill: true,
                        }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' }},
                    scales: {
                        x: { title: { display: true, text: 'Gestión' }},
                        y: { beginAtZero: true, ticks: { precision: 0 }}
                    }
                }
            });
        })();
        @endif
    });
    </script>
    @endpush
</x-app-layout>
