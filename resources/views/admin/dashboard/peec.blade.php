<x-app-layout>
    <div class="px-4 max-w-7xl mx-auto">
        <div class="mb-4">
            <h1 class="text-xl font-bold text-gray-800">Dashboard PEEC — Panorama global</h1>
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
                    {{ $kpis['pct_aptitud'] !== null ? $kpis['pct_aptitud'] . '%' : '—' }}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
            {{-- V5: Donut desempeno global --}}
            <div class="bg-white border rounded p-4 lg:col-span-1">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Desempeño global</h3>
                @if ($desempeno['n'] > 0)
                    <canvas id="donutGlobal" height="200"></canvas>
                    <ul class="text-xs mt-3 space-y-1">
                        <li class="flex items-center gap-2">
                            <span class="inline-block w-3 h-3 bg-green-500 rounded-full"></span>
                            Satisfactorios: <strong>{{ $desempeno['satisfactorios'] }}</strong>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="inline-block w-3 h-3 bg-yellow-500 rounded-full"></span>
                            Cuestionables: <strong>{{ $desempeno['cuestionables'] }}</strong>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="inline-block w-3 h-3 bg-red-500 rounded-full"></span>
                            No satisfactorios: <strong>{{ $desempeno['no_satisfactorios'] }}</strong>
                        </li>
                        <li class="border-t pt-1 mt-2">
                            Total evaluaciones: <strong>{{ $desempeno['n'] }}</strong>
                        </li>
                    </ul>
                @else
                    <p class="text-xs text-gray-400 italic">Sin evaluaciones registradas en esta gestión.</p>
                @endif
            </div>

            {{-- V6: Bar chart tasa respuesta por ensayo --}}
            <div class="bg-white border rounded p-4 lg:col-span-2">
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
        </div>

        {{-- Tabla detalle de tasas --}}
        @if ($tasasPorEnsayo->isNotEmpty())
            <div class="bg-white border rounded p-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Detalle por ensayo</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead class="bg-gray-50 text-gray-600 uppercase">
                            <tr>
                                <th class="px-2 py-1 text-left">Ensayo</th>
                                <th class="px-2 py-1 text-right">Labs enviaron</th>
                                <th class="px-2 py-1 text-right">Labs inscritos</th>
                                <th class="px-2 py-1 text-right">% Respuesta</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tasasPorEnsayo as $t)
                                <tr class="border-t">
                                    <td class="px-2 py-1">{{ $t->descripcion }}</td>
                                    <td class="px-2 py-1 text-right">{{ $t->labs_enviaron }}</td>
                                    <td class="px-2 py-1 text-right">{{ $t->labs_inscritos }}</td>
                                    <td class="px-2 py-1 text-right font-bold
                                        @if ($t->porcentaje === null) text-gray-400
                                        @elseif ($t->porcentaje >= 80) text-green-600
                                        @elseif ($t->porcentaje >= 50) text-yellow-600
                                        @else text-red-600
                                        @endif">
                                        {{ $t->porcentaje !== null ? $t->porcentaje . '%' : '—' }}
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
        @if ($desempeno['n'] > 0)
        new Chart(document.getElementById('donutGlobal'), {
            type: 'doughnut',
            data: {
                labels: ['Satisfactorios', 'Cuestionables', 'No satisfactorios'],
                datasets: [{
                    data: [{{ $desempeno['satisfactorios'] }}, {{ $desempeno['cuestionables'] }}, {{ $desempeno['no_satisfactorios'] }}],
                    backgroundColor: ['rgba(34,197,94,0.85)', 'rgba(234,179,8,0.85)', 'rgba(239,68,68,0.85)'],
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }}}
        });
        @endif

        @if ($tasasPorEnsayo->isNotEmpty())
        (function () {
            const tasas = @json($tasasPorEnsayo->all());
            new Chart(document.getElementById('tasasPorEnsayo'), {
                type: 'bar',
                data: {
                    labels: tasas.map(t => t.descripcion.length > 40 ? t.descripcion.slice(0,40)+'...' : t.descripcion),
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
                                afterLabel: ctx => 'Enviaron: ' + tasas[ctx.dataIndex].labs_enviaron + ' / Inscritos: ' + tasas[ctx.dataIndex].labs_inscritos
                            }
                        }
                    },
                    scales: {
                        x: { beginAtZero: true, max: 100, title: { display: true, text: '%' }},
                        y: { ticks: { font: { size: 10 }}}
                    }
                }
            });
        })();
        @endif
    });
    </script>
    @endpush
</x-app-layout>
