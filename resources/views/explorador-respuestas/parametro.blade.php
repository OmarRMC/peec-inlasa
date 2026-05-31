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
            </nav>
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h1 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-cubes text-indigo-500 mr-1"></i>
                        {{ $parametro->nombre }}
                    </h1>
                    <div class="text-xs text-gray-500 mt-1">
                        {{ $campos->count() }} campo(s) · {{ $valoresLab->count() }} laboratorio(s) respondieron
                    </div>
                </div>
                <a href="{{ route('explorador-respuestas.parametro.export', [$ensayo->id, $ciclo->id, $parametro->id]) }}"
                    class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-xs px-3 py-1.5 rounded whitespace-nowrap">
                    <i class="fas fa-file-pdf"></i> Exportar PDF
                </a>
            </div>
        </div>

        {{-- Selector de ciclo --}}
        <div class="flex flex-wrap items-center gap-2 mb-4 text-sm">
            <label class="whitespace-nowrap text-gray-700 font-medium">Ciclo:</label>
            <select onchange="window.location.href = '{{ route('explorador-respuestas.parametro', [$ensayo->id, '__CICLO__', $parametro->id]) }}'.replace('__CICLO__', this.value)"
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

        {{-- Listado de campos del parámetro --}}
        <div class="bg-white border rounded p-4 mb-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Campos del parámetro</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                @foreach ($campos as $c)
                    @php
                        $badge = match ($c->tipo_descriptivo) {
                            'numerico'   => ['bg' => 'bg-blue-100', 'tx' => 'text-blue-700', 'ic' => 'fa-hashtag'],
                            'categorico' => ['bg' => 'bg-purple-100', 'tx' => 'text-purple-700', 'ic' => 'fa-list'],
                            'booleano'   => ['bg' => 'bg-amber-100', 'tx' => 'text-amber-700', 'ic' => 'fa-check-square'],
                            'fecha'      => ['bg' => 'bg-teal-100', 'tx' => 'text-teal-700', 'ic' => 'fa-calendar'],
                            default      => ['bg' => 'bg-gray-100', 'tx' => 'text-gray-700', 'ic' => 'fa-font'],
                        };
                    @endphp
                    <a href="{{ route('explorador-respuestas.campo', [$ensayo->id, $ciclo->id, $c->id_campo]) }}"
                        class="block border rounded p-2 hover:bg-indigo-50 hover:border-indigo-300 transition text-xs">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-gray-800 truncate">{{ $c->label }}</div>
                                <div class="text-gray-500 mt-0.5">{{ $c->snippet }}</div>
                            </div>
                            <span class="text-[10px] {{ $badge['bg'] }} {{ $badge['tx'] }} px-1.5 py-0.5 rounded uppercase whitespace-nowrap">
                                <i class="fas {{ $badge['ic'] }}"></i> {{ $c->tipo_descriptivo }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Constructor de gráficos: selectores --}}
        <div class="bg-white border rounded p-4 mb-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">
                <i class="fas fa-chart-mixed text-indigo-500 mr-1"></i>
                Combinador de campos
            </h3>
            <p class="text-xs text-gray-500 mb-3">
                Selecciona uno o dos campos para ver su distribución combinada.
                Opcionalmente filtra por una categoría de otro campo.
            </p>
            <form method="GET" action="{{ url()->current() }}" class="text-sm">
                <div class="flex flex-wrap gap-2 mb-3">
                    <div class="flex items-center gap-2">
                        <label class="whitespace-nowrap text-gray-700 font-medium">Campo principal:</label>
                        <select name="campo_x"
                            class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[200px]">
                            <option value="">Selecciona</option>
                            @foreach ($campos as $c)
                                <option value="{{ $c->id_campo }}" @if ($idCampoX == $c->id_campo) selected @endif>
                                    {{ $c->label }} ({{ $c->tipo_descriptivo }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="whitespace-nowrap text-gray-700 font-medium">Campo secundario:</label>
                        <select name="campo_y"
                            class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[200px]">
                            <option value="">Solo univariado</option>
                            @foreach ($campos as $c)
                                <option value="{{ $c->id_campo }}" @if ($idCampoY == $c->id_campo) selected @endif>
                                    {{ $c->label }} ({{ $c->tipo_descriptivo }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="whitespace-nowrap text-gray-700 font-medium">Filtrar por:</label>
                        <select name="filtro_campo"
                            class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[180px]">
                            <option value="">Sin filtro</option>
                            @foreach ($campos as $c)
                                @if (in_array($c->tipo_descriptivo, ['categorico', 'booleano']))
                                    <option value="{{ $c->id_campo }}" @if ($idCampoFiltro == $c->id_campo) selected @endif>
                                        {{ $c->label }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="whitespace-nowrap text-gray-700 font-medium">Opción:</label>
                        <select name="filtro_opcion"
                            class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[180px]"
                            @if (!$idCampoFiltro) disabled @endif>
                            <option value="">Cualquier opción</option>
                            @foreach ($opcionesFiltro as $o)
                                <option value="{{ $o->id }}" @if ($idOpcionFiltro == $o->id) selected @endif>
                                    {{ $o->etiqueta }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex gap-2 justify-end">
                    <a href="{{ route('explorador-respuestas.parametro', [$ensayo->id, $ciclo->id, $parametro->id]) }}"
                        class="flex items-center gap-2 px-4 py-2 border rounded-md text-sm bg-white hover:bg-gray-50 shadow-sm">
                        <i class="fas fa-rotate-left"></i> Limpiar
                    </a>
                    <button type="submit"
                        class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-500 transition shadow-md text-sm">
                        <i class="fas fa-chart-line"></i> Generar gráfico
                    </button>
                </div>
            </form>
        </div>

        {{-- Resultado de la combinación --}}
        @if ($combinacion['modo'] !== 'vacio')
            <div class="bg-white border rounded p-4 mb-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-chart-area text-indigo-500 mr-1"></i>
                    @switch($combinacion['modo'])
                        @case('univariado') Distribución de "{{ $combinacion['x_label'] }}" @break
                        @case('scatter') Scatter: "{{ $combinacion['x_label'] }}" vs "{{ $combinacion['y_label'] }}" @break
                        @case('box_agrupado') Box-plot de "{{ $combinacion['x_label'] }}" agrupado por "{{ $combinacion['y_label'] }}" @break
                        @case('tabla_cruzada') Tabla cruzada: "{{ $combinacion['x_label'] }}" × "{{ $combinacion['y_label'] }}" @break
                        @default Resultado
                    @endswitch
                </h3>

                @switch($combinacion['modo'])
                    @case('univariado')
                        @include('explorador-respuestas.partials.combinacion-univariado', ['c' => $combinacion])
                        @break
                    @case('scatter')
                        @include('explorador-respuestas.partials.combinacion-scatter', ['c' => $combinacion])
                        @break
                    @case('box_agrupado')
                        @include('explorador-respuestas.partials.combinacion-box-agrupado', ['c' => $combinacion])
                        @break
                    @case('tabla_cruzada')
                        @include('explorador-respuestas.partials.combinacion-tabla-cruzada', ['c' => $combinacion])
                        @break
                    @case('no_soportado')
                        <p class="text-xs text-amber-600">
                            La combinación seleccionada no genera un gráfico estándar.
                            Prueba otros campos.
                        </p>
                        @break
                @endswitch
            </div>
        @endif

        {{-- Tabla lab × campos --}}
        <div class="bg-white border rounded p-4 mb-4">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">
                    Tabla de respuestas por laboratorio
                </h3>
                <span class="text-[10px] text-gray-500">{{ $valoresLab->count() }} fila(s)</span>
            </div>
            <div class="overflow-auto border rounded" style="max-height: 480px;">
                <table class="w-full text-xs">
                    <thead class="bg-gray-50 text-gray-600 uppercase sticky top-0 z-10 shadow-sm">
                        <tr>
                            <th class="px-2 py-2 text-left bg-gray-50">Código</th>
                            <th class="px-2 py-2 text-left bg-gray-50">Laboratorio</th>
                            @foreach ($campos as $c)
                                <th class="px-2 py-2 text-left bg-gray-50" title="{{ $c->label }}">
                                    {{ \Illuminate\Support\Str::limit($c->label, 25) }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($valoresLab as $row)
                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-2 py-1 font-mono whitespace-nowrap">{{ $row->cod_lab ?? '-' }}</td>
                                <td class="px-2 py-1 whitespace-nowrap">{{ $row->nombre_lab }}</td>
                                @foreach ($campos as $c)
                                    <td class="px-2 py-1">{{ $row->valores[$c->id_campo] ?? '-' }}</td>
                                @endforeach
                            </tr>
                        @empty
                            <tr><td colspan="{{ $campos->count() + 2 }}" class="text-center text-gray-400 italic py-3">
                                Sin respuestas en este ciclo.
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        @if ($combinacion['modo'] === 'univariado' && ($combinacion['tipo_x'] ?? '') === 'numerico')
        (function () {
            const data = @json($combinacion['data']);
            if (data.histograma && data.histograma.bins && data.histograma.bins.length > 0) {
                new Chart(document.getElementById('chartUnivariado'), {
                    type: 'bar',
                    data: {
                        labels: data.histograma.bins.map(b => b.etiqueta),
                        datasets: [{
                            label: 'Frecuencia',
                            data: data.histograma.bins.map(b => b.count),
                            backgroundColor: 'rgba(99,102,241,0.7)',
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { display: false }},
                        scales: { y: { beginAtZero: true }, x: { ticks: { font: { size: 9 }, maxRotation: 45 }}}
                    }
                });
            }
        })();
        @endif

        @if ($combinacion['modo'] === 'univariado' && ($combinacion['tipo_x'] ?? '') === 'categorico')
        (function () {
            const data = @json($combinacion['data']);
            const freq = data.frecuencias || [];
            if (freq.length > 0) {
                new Chart(document.getElementById('chartUnivariado'), {
                    type: 'bar',
                    data: {
                        labels: freq.map(f => f.etiqueta),
                        datasets: [{
                            label: 'Frecuencia',
                            data: freq.map(f => f.count),
                            backgroundColor: 'rgba(168,85,247,0.7)',
                        }]
                    },
                    options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }}}
                });
            }
        })();
        @endif

        @if ($combinacion['modo'] === 'scatter')
        (function () {
            const puntos = @json($combinacion['data']->all());
            if (puntos.length > 0) {
                new Chart(document.getElementById('chartScatter'), {
                    type: 'scatter',
                    data: {
                        datasets: [{
                            label: '{{ $combinacion['x_label'] }} vs {{ $combinacion['y_label'] }}',
                            data: puntos.map(p => ({ x: p.x, y: p.y, _lab: p.nombre_lab, _cod: p.cod_lab })),
                            backgroundColor: 'rgba(99,102,241,0.7)',
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: {
                            tooltip: { callbacks: {
                                label: ctx => `${ctx.raw._lab} (${ctx.raw._cod || '-'}): (${ctx.raw.x}, ${ctx.raw.y})`
                            }}
                        },
                        scales: {
                            x: { title: { display: true, text: '{{ $combinacion['x_label'] }}' }},
                            y: { title: { display: true, text: '{{ $combinacion['y_label'] }}' }}
                        }
                    }
                });
            }
        })();
        @endif

        @if ($combinacion['modo'] === 'box_agrupado')
        (function () {
            // Box-plot manual con bar (Chart.js no tiene boxplot nativo): mostramos min, q1, mediana, q3, max
            const data = @json($combinacion['data']->all());
            if (data.length > 0) {
                new Chart(document.getElementById('chartBoxAgrupado'), {
                    type: 'bar',
                    data: {
                        labels: data.map(d => d.categoria + ' (n=' + d.n + ')'),
                        datasets: [
                            { label: 'Q1', data: data.map(d => d.q1), backgroundColor: 'rgba(99,102,241,0.4)' },
                            { label: 'Mediana', data: data.map(d => d.mediana), backgroundColor: 'rgba(99,102,241,0.85)' },
                            { label: 'Q3', data: data.map(d => d.q3), backgroundColor: 'rgba(99,102,241,0.4)' },
                        ]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom' }},
                        scales: { y: { beginAtZero: false }}
                    }
                });
            }
        })();
        @endif
    });
    </script>
    @endpush
</x-app-layout>
