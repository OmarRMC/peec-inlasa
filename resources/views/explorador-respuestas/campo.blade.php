<x-app-layout>
    <div class="px-4 max-w-7xl mx-auto">
        {{-- Breadcrumb --}}
        <div class="mb-4">
            <nav class="text-xs text-gray-500 mb-1">
                <a href="{{ route('reportes.resultados.ensayos') }}" class="hover:underline">Reporte de ensayos</a>
                <span class="mx-1">/</span>
                <a href="{{ route('explorador-respuestas.ensayo', $ensayo->id) }}" class="hover:underline">{{ $ensayo->descripcion }}</a>
                <span class="mx-1">/</span>
                @if ($formulario)
                    <a href="{{ route('explorador-respuestas.formulario', [$ensayo->id, $ciclo->id, $formulario->id]) }}" class="hover:underline">
                        {{ $formulario->nombre }}
                    </a>
                    <span class="mx-1">/</span>
                @endif
                <span>{{ $seccion->nombre ?? '' }}</span>
                <span class="mx-1">/</span>
                <span class="text-gray-700">{{ $parametro->nombre ?? '' }}</span>
            </nav>
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h1 class="text-xl font-bold text-gray-800">
                        {{ $campo->etiqueta }}
                    </h1>
                    <div class="text-xs text-gray-500 mt-1 flex items-center gap-3 flex-wrap">
                        <span>Tipo catálogo: <code>{{ $campo->tipo }}</code></span>
                        <span class="px-2 py-0.5 rounded uppercase text-[10px]
                            @switch($tipo)
                                @case('numerico') bg-blue-100 text-blue-700 @break
                                @case('categorico') bg-purple-100 text-purple-700 @break
                                @case('booleano') bg-amber-100 text-amber-700 @break
                                @case('fecha') bg-teal-100 text-teal-700 @break
                                @default bg-gray-100 text-gray-700
                            @endswitch">
                            Tratado como: {{ $tipo }}
                        </span>
                        @if ($tipo === 'numerico' && $campo->tipo !== 'number')
                            <span class="text-amber-600 text-[11px]">
                                <i class="fas fa-info-circle"></i> Detectado como numérico por heurística (≥90% valores parseables)
                            </span>
                        @endif
                    </div>
                </div>
                <a href="{{ route('explorador-respuestas.campo.export', [$ensayo->id, $ciclo->id, $campo->id]) }}"
                    class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-xs px-3 py-1.5 rounded whitespace-nowrap">
                    <i class="fas fa-file-pdf"></i> Exportar PDF
                </a>
            </div>
        </div>

        {{-- Selector de ciclo --}}
        <div class="flex flex-wrap items-center gap-2 mb-4 text-sm">
            <label class="whitespace-nowrap text-gray-700 font-medium">Ciclo:</label>
            <select onchange="window.location.href = '{{ route('explorador-respuestas.campo', [$ensayo->id, '__CICLO__', $campo->id]) }}'.replace('__CICLO__', this.value)"
                class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[200px]">
                @foreach ($ciclos as $c)
                    <option value="{{ $c->id }}" @if ($c->id == $ciclo->id) selected @endif>
                        {{ $c->nombre }} ({{ $c->gestion }})
                    </option>
                @endforeach
            </select>

            <a href="{{ $backUrl }}"
                class="ml-auto flex items-center gap-2 px-4 py-2 border rounded-md text-sm bg-white hover:bg-gray-50 shadow-sm">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>

        {{-- Render por tipo --}}
        @switch($tipo)
            @case('numerico')
                @include('explorador-respuestas.partials.campo-numerico', [
                    'descriptivo' => $descriptivo,
                    'histograma'  => $histograma,
                    'boxplot'     => $boxplot,
                    'valoresLab'  => $valoresLab,
                ])
                @break

            @case('categorico')
                @include('explorador-respuestas.partials.campo-categorico', [
                    'frecuencias' => $frecuencias,
                ])
                @break

            @case('booleano')
                @include('explorador-respuestas.partials.campo-booleano', [
                    'booleano' => $booleano,
                ])
                @break

            @case('fecha')
                @include('explorador-respuestas.partials.campo-fecha', [
                    'fechas' => $fechas,
                ])
                @break

            @default
                @include('explorador-respuestas.partials.campo-texto', [
                    'topValores' => $topValores,
                    'distintos'  => $distintos,
                ])
        @endswitch
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        @switch($tipo)
            @case('numerico')
            (function () {
                const hist = @json($histograma);
                if (hist.bins && hist.bins.length > 0) {
                    new Chart(document.getElementById('chartHistograma'), {
                        type: 'bar',
                        data: {
                            labels: hist.bins.map(b => b.etiqueta),
                            datasets: [{
                                label: 'Frecuencia',
                                data: hist.bins.map(b => b.count),
                                backgroundColor: 'rgba(99,102,241,0.7)',
                            }]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            plugins: { legend: { display: false }},
                            scales: {
                                y: { beginAtZero: true, title: { display: true, text: 'Frecuencia' }},
                                x: { ticks: { font: { size: 9 }, maxRotation: 45, minRotation: 45 }, title: { display: true, text: 'Rango' }}
                            }
                        }
                    });
                }
            })();
            @break

            @case('categorico')
            (function () {
                const data = @json($frecuencias->all());
                if (data.length > 0) {
                    new Chart(document.getElementById('chartCategorico'), {
                        type: 'bar',
                        data: {
                            labels: data.map(d => d.etiqueta),
                            datasets: [{
                                label: 'Frecuencia',
                                data: data.map(d => d.count),
                                backgroundColor: 'rgba(168,85,247,0.7)',
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true, maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: { callbacks: { afterLabel: ctx => data[ctx.dataIndex].pct + '%' }}
                            },
                            scales: { x: { beginAtZero: true }}
                        }
                    });
                }
            })();
            @break

            @case('booleano')
            (function () {
                const b = @json($booleano);
                if (b.n > 0) {
                    new Chart(document.getElementById('chartBooleano'), {
                        type: 'doughnut',
                        data: {
                            labels: ['Marcado', 'No marcado'],
                            datasets: [{
                                data: [b.marcado, b.no_marcado],
                                backgroundColor: ['rgba(34,197,94,0.85)', 'rgba(239,68,68,0.85)'],
                            }]
                        },
                        options: { responsive: true, maintainAspectRatio: false }
                    });
                }
            })();
            @break

            @case('fecha')
            (function () {
                const data = @json($fechas->all());
                if (data.length > 0) {
                    new Chart(document.getElementById('chartFechas'), {
                        type: 'bar',
                        data: {
                            labels: data.map(d => d.periodo),
                            datasets: [{
                                label: 'Cantidad',
                                data: data.map(d => d.count),
                                backgroundColor: 'rgba(20,184,166,0.7)',
                            }]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            plugins: { legend: { display: false }},
                            scales: { y: { beginAtZero: true }}
                        }
                    });
                }
            })();
            @break
        @endswitch
    });
    </script>
    @endpush
</x-app-layout>
